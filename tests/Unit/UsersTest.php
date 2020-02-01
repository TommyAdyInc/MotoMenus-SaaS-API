<?php

namespace Tests\Unit;

use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use UserSeeder;

class UsersTest extends TestCase
{
    private $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(UserSeeder::class);

        $this->user = User::find(1);
    }

    /** @test * */
    function can_retrieve_authenticated_user()
    {
        Passport::actingAs($this->user);

        $response = $this->get('/api/user');
        $response->assertStatus(201);
    }

    /**
     * @test
     *
     * @return void
     */
    public function create_new_user_returns_user_with_role()
    {
        Passport::actingAs($this->user);

        $response = $this->post('/api/users', [
            'name'     => 'Test Name',
            'email'    => 'test@test.com',
            'password' => 'test1234',
            'role'     => 'user',
        ]);

        $response->assertStatus(201);
        $response = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('id', $response);
        $this->assertEquals('user', $response['role']);
    }

    /** @test * */
    function updating_user_returns_updated_user_with_role()
    {
        Passport::actingAs($this->user);

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

        $response = json_decode((string)$response->getContent(), true);
        $this->assertEquals('My Updated Name', $response['name']);
        $this->assertEquals('admin', $response['role']);
    }

    /** @test * */
    function user_roles_can_only_edit_own_user_entry()
    {
        Passport::actingAs($this->user);

        $response = $this->post('/api/users', [
            'name'     => 'Test Name',
            'email'    => 'test@test.com',
            'password' => 'test1234',
            'role'     => 'user',
        ]);

        $response->assertStatus(201);
        $response = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('id', $response);

        // Attempt to upgrade user role
        Passport::actingAs(User::find($response['id']));
        $try_upgrade_role_response = $this->put('/api/users/' . $response['id'], [
            'name' => 'My Updated Name',
            'role' => 'admin'
        ]);
        $try_upgrade_role_response->assertStatus(422);
        $try_upgrade_role_response = json_decode($try_upgrade_role_response->getContent(), true);

        $this->assertEquals('Not allowed to modify user role.', $try_upgrade_role_response['error']);

        // Attempt to modify other user account when having user role
        Passport::actingAs(User::find($response['id']));
        $response = $this->put('/api/users/' . $this->user->id, [
            'name' => 'My Updated Name',
            'role' => 'user'
        ]);
        $response->assertStatus(422);
        $response = json_decode($response->getContent(), true);

        $this->assertEquals('Can only modify own user data.', $response['error']);
    }
}
