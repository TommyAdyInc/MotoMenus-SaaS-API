<?php

namespace Tests\Unit;

use App\SuperAdmin;
use App\User;
use SuperAdminSeeder;
use Tests\PassportMultiAuth;
use Tests\TestCase;
use UserSeeder;

class SuperAdminsTest extends TestCase
{
    private $user;
    private $super_admin;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(UserSeeder::class);
        $this->seed(SuperAdminSeeder::class);

        $this->user = User::find(1);
        $this->super_admin = SuperAdmin::find(1);
    }

    /**
     * @test
     *
     * @return void
     */
    public function tenant_user_cannot_retrieve_global_settings_only_super_admins_can()
    {
        PassportMultiAuth::actingAs($this->user);

        $this->json('GET', '/api/global-settings')->assertStatus(401);

        PassportMultiAuth::actingAs($this->super_admin);

        $this->json('GET', '/api/global-settings')->assertStatus(201);
    }

    /** @test * */
    function only_super_admins_can_update_global_settings()
    {
        PassportMultiAuth::actingAs($this->user);

        $this->json('PUT', '/api/global-settings', ['document_fee' => 123])->assertStatus(401);

        PassportMultiAuth::actingAs($this->super_admin);

        $this->json('PUT', '/api/global-settings', ['document_fee' => 123])->assertStatus(201);
    }

    /** @test * */
    function super_admins_can_access_tenant_routes()
    {
        PassportMultiAuth::actingAs($this->super_admin);

        $this->json('GET', '/api/customers')
            ->assertStatus(201);
    }

    /** @test * */
    function super_admins_can_access_tenant_admin_routes()
    {
        PassportMultiAuth::actingAs($this->super_admin);

        $this->json('GET', '/api/users')
            ->assertStatus(201);
    }

    /** @test * */
    function super_admins_can_create_tenant_users()
    {
        PassportMultiAuth::actingAs($this->super_admin);

        $this->json('POST','/api/users', [
            'name'     => 'Test Name',
            'email'    => 'test@test.com',
            'password' => 'test1234',
            'role'     => 'user',
        ])->assertStatus(201);
    }

    /** @test * */
    function super_admins_can_update_tenant_users()
    {
        PassportMultiAuth::actingAs($this->super_admin);

        $response = $this->post('/api/users', [
            'name'     => 'Test Name',
            'email'    => 'test@test.com',
            'password' => 'test1234',
            'role'     => 'user',
        ]);

        $response = json_decode((string)$response->getContent(), true);
        $this->assertArrayHasKey('id', $response);

        $response = $this->put('/api/users/' . $response['id'], [
            'name' => 'My Updated Name',
            'role' => 'admin'
        ]);

        $response->assertStatus(201);
        $response = json_decode((string)$response->getContent(), true);
        $this->assertEquals('My Updated Name', $response['name']);
        $this->assertEquals('admin', $response['role']);
    }
}
