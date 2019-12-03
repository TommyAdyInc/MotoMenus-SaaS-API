<?php

namespace Tests;

use App\TenantCustomer;
use Hyn\Tenancy\Database\Connection;
use Hyn\Tenancy\Environment;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Models\Website;
use Hyn\Tenancy\Repositories\HostnameRepository;
use Hyn\Tenancy\Repositories\WebsiteRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

//    use RefreshDatabase;
//    use DatabaseTransactions;

    protected static $is_first_time_through = true;

    const WEBSITE_UUID = '000-phpunit';
    const HOSTNAME_FQDN = 'phpunit.dev.api.motomenus.local';
    const TENANT_CUSTOMER_NAME = 'phpunit';

    protected function setUp() :void
    {
        parent::setUp();

        // If this is the first time through, run the first time through tasks.
        if (static::$is_first_time_through) {
            static::$is_first_time_through = false;
            try {
                $this->runFirstTimeThroughTasks();
                dump('Begin the test suite.');
            } catch (\Exception $e) {
                dump($e->getMessage());
            }
        }

        try {
            $this->switchActiveTenant();

            $this->truncateAllTables();
        } catch(\Exception $e) {
            dump($e->getMessage());
        }
    }

    protected function tearDown() :void
    {
        // Skip parent tear down as it causes issues with running all tests at once
        // parent::tearDown();
    }

    /**
     * Only run this one time at the very start of the test suite.
     */
    protected function runFirstTimeThroughTasks(): void
    {
        dump('Starting first time through tasks...');

        // Create tenant if doesn't exist.
        $website_repository = app(WebsiteRepository::class);
        $website = $website_repository->findByUuid(self::WEBSITE_UUID);
        if ($website instanceof Website) {
            throw new \Exception('Website could not be created! The website with a UUID of ' . $website->uuid . ' already exists.');
        }

        dump('Creating a new tenant to use for testing.');
        $website = new Website;
        $website->uuid = self::WEBSITE_UUID;
        $website_repository->create($website);

        $hostname_repository = app(HostnameRepository::class);

        $hostname = $hostname_repository->findByHostname(self::HOSTNAME_FQDN);
        if ($hostname instanceof Hostname) {
            throw new \Exception('Hostname could not be created. The hostname with a FQDN of ' . $hostname->fqdn . ' already exists.');
        }

        $hostname = new Hostname;
        $hostname->fqdn = self::HOSTNAME_FQDN;
        $hostname_repository->create($hostname);
        $hostname_repository->attach($hostname, $website);

        dump('... finished first time through tasks.');
    }

    /**
     * Set the active tenant.
     * This is run at the beginning of each and every test that is run.
     */
    protected function switchActiveTenant()
    {
        dump('Switching active tenant...');
        // ensure we have database tenant connection
        config()->set('database.default', 'tenant');

        $tenancy = app(Environment::class);
        $website_repository = app(WebsiteRepository::class);
        $website = $website_repository->findByUuid(self::WEBSITE_UUID);

        $hostname = $website->hostnames->first();

        if(!$hostname) {
            throw new \Exception('Could not determine correct hostname.');
        }

        $current_hostname = $tenancy->hostname($hostname);
        dump('Current Hostname FQDN = ' . $current_hostname->fqdn);

        // Switches the tenant and reconfigures the app.
        $current_website = $tenancy->tenant($website);
        dump('Current Website UUID = ' . $current_website->uuid);

        $tenancy->tenant();
        $tenancy->identifyHostname();
    }

    /**
     * Truncate all tables in the current active tenants database since we don't have the luxury of using the
     * RefreshDatabase or DatabaseTransactions traits. Currently, the Laravel Tenancy package is not compatible with
     * those traits.
     */
    protected function truncateAllTables(): void
    {
        $table_names = Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        foreach ($table_names as $table_name) {
            if ($table_name == 'migrations') {
                continue;
            }
            DB::table($table_name)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
