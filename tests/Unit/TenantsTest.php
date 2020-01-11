<?php

namespace Tests\Unit;

use App\SuperAdmin;
use SuperAdminSeeder;
use Tests\PassportMultiAuth;
use Tests\TestCase;

class TenantsTest extends TestCase
{
    private $super_admin;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(SuperAdminSeeder::class);
        $this->super_admin = SuperAdmin::find(1);
    }

    /**
     * @test
     */
    public function retrieve_all_tenants_in_database()
    {
        PassportMultiAuth::actingAs($this->super_admin);

        $this->json('GET', '/api/tenants')
            ->assertStatus(201);
    }

    /**
     * @test
     */
    public function retrieve_specific_tenant_from_database()
    {
        PassportMultiAuth::actingAs($this->super_admin);

        $this->json('GET', '/api/tenants/1')
            ->assertStatus(201)
            ->assertJsonFragment(['000-tenant-phpunit']);
    }

    /** @test * */
    function create_new_tenant_returns_newly_created_tenant()
    {
        PassportMultiAuth::actingAs($this->super_admin);

        $this->json('POST', '/api/tenants', [
            'fqdn'       => 'motomenus.demo.app',
            'store_name' => 'Demo Store'
        ])
            ->assertStatus(201)
            ->assertJsonFragment(['Demo Store'])
            ->assertJsonFragment(['motomenus.demo.app']);
    }

    /** @test * */
    function tenants_can_be_archived()
    {
        PassportMultiAuth::actingAs($this->super_admin);

        $this->json('DELETE', '/api/tenants/1')
            ->assertStatus(201);
    }
}
