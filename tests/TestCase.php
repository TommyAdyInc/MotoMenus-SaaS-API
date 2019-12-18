<?php

namespace Tests;

use Hyn\Tenancy\Database\Connection;
use Hyn\Tenancy\Environment;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Models\Website;
use Hyn\Tenancy\Repositories\HostnameRepository;
use Hyn\Tenancy\Repositories\WebsiteRepository;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected static $is_first_time_through = true;

    const WEBSITE_UUID = '000-tenant-phpunit';
    const HOSTNAME_FQDN = 'phpunit.dev.api.motomenus.local';

    protected function setUp() :void
    {
        parent::setUp();

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
        } catch(\Exception $e) {
            dump($e->getMessage());
        }

        $this->truncateAllTables();
    }

    /**
     * Only run this one time at the very start of the test suite.
     */
    private function runFirstTimeThroughTasks(): void
    {
        dump('Starting first time through tasks...');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        $this->handleSystem();
        $this->handleTenant();
        dump('...finished first time through tasks.');
    }

    private function handleSystem(): void
    {
        extract(config('database.connections.connection_system'));

        dump('Dropping system database...');
        $this->execProcess("mysql --host=$host --password=$password --port=$port --protocol=tcp --user=$username --execute \"DROP DATABASE IF EXISTS `$database`;\"  --verbose");

        dump('Re-creating system database...');
        $this->execProcess("mysql --default-character-set=utf8 --host=$host --password=$password --port=$port --protocol=tcp --user=$username --execute \"CREATE DATABASE `$database`;\"  --verbose");

        dump('Migrating system database...');
        Artisan::call('migrate --database="connection_system"');

        dump('Seeding system database...');
        Artisan::call('db:seed --database="connection_system"');
    }

    private function handleTenant(): void
    {
        extract(config('database.connections.connection_system'));

        dump('Dropping tenant database and user...');
        $this->execProcess("mysql --host=$host --password=$password --port=$port --protocol=tcp --user=$username --execute \"DROP DATABASE IF EXISTS `" . self::WEBSITE_UUID . "`; DROP USER IF EXISTS `" .  self::WEBSITE_UUID . "`\"  --verbose");

        dump('Re-creating tenant database...');
        $website_repository = app(WebsiteRepository::class);
        $website = $website_repository->findByUuid(self::WEBSITE_UUID);
        if ($website instanceof Website) {
            throw new \Exception('Website could not be created! The website with a UUID of ' . $website->uuid . ' already exists.');
        }

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
    }

    private function execProcess($command)
    {
        $process = new Process($command);
        $process->setTimeout(1200);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        dump($process->getOutput());
    }

    /**
     * Set the active tenant.
     * This is run at the beginning of each and every test that is run.
     */
    protected function switchActiveTenant()
    {
        // dump('Switching active tenant...');

        $website_repository = app(WebsiteRepository::class);
        $website = $website_repository->findByUuid(self::WEBSITE_UUID);

        $hostname_repository = app(HostnameRepository::class);
        $hostname = $hostname_repository->findByHostname(self::HOSTNAME_FQDN);

        if (!$hostname) {
            throw new \Exception('Could not determine correct hostname while switching active tenant.');
        }

        $tenancy = app(Environment::class);

        $current_hostname = $tenancy->hostname($hostname);
        // dump('Current Hostname FQDN = ' . $current_hostname->fqdn);

        // Switches the tenant and reconfigures the app.
        $current_website = $tenancy->tenant($website);
        // dump('Current Website UUID = ' . $current_website->uuid);
    }

    /**
     * Truncate all tables in the current active tenants database.
     */
    protected function truncateAllTables(): void
    {
        // Ensure we have tenant database connection.
        config()->set('database.default', 'tenant');

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
