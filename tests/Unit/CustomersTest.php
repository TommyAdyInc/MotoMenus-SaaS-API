<?php

namespace Tests\Unit;

use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use UserSeeder;

class CustomersTest extends TestCase
{
    private $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(UserSeeder::class);
        $this->user = User::find(1);
    }

    /**
     * @test
     *
     * @return void
     */
    public function create_new_customer_returns_customer_and_note()
    {
        Passport::actingAs($this->user);

        $response = $this->post('/api/customers', [
            'first_name' => 'First',
            'last_name'  => 'Last',
            'phone'      => '999-999-9999',
            'email'      => 'first@last.com',
            'note'       => 'This is a test note',
        ]);

        $response->assertStatus(201);

        $response = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('id', $response);
        $this->assertEquals('This is a test note', $response['note']['note']);
    }

    /** @test * */
    function updating_customer_returns_updated_customer_and_updated_note()
    {
        Passport::actingAs($this->user);

        $response = $this->post('/api/customers', [
            'first_name' => 'First',
            'last_name'  => 'Last',
            'phone'      => '999-999-9999',
            'email'      => 'first@last.com',
            'note'       => 'This is a note',
        ]);

        $response = json_decode((string)$response->getContent(), true);
        $this->assertArrayHasKey('id', $response);

        $response = $this->put('/api/customers/' . $response['id'], [
            'first_name' => 'FirstName',
            'note'       => 'This is an updated note',
        ]);

        $response = json_decode((string)$response->getContent(), true);
        $this->assertEquals('FirstName', $response['first_name']);
        $this->assertEquals('This is an updated note', $response['note']['note']);
    }

    /** @test * */
    function users_with_user_role_can_only_view_own_customers()
    {
        Passport::actingAs($this->user);

        $response = $this->post('/api/customers', [
            'first_name' => 'First',
            'last_name'  => 'Last',
            'phone'      => '999-999-9999',
            'email'      => 'first@last.com',
            'note'       => 'This is a test note',
        ]);

        $response->assertStatus(201);

        $user_admin_customer = json_decode($response->getContent(), true);

        $response = $this->post('/api/users', [
            'name'     => 'Test Name',
            'email'    => 'test@test.com',
            'password' => 'test1234',
            'role'     => 'user',
        ]);

        $response->assertStatus(201);
        $user = json_decode($response->getContent(), true);

        Passport::actingAs(User::find($user['id']));
        $response = $this->get('/api/customers/' . $user_admin_customer['id']);
        $response->assertStatus(422);
        $response = json_decode($response->getContent(), true);
        $this->assertEquals('Cannot view other user customer.', $response['error']);
    }

    /** @test * */
    function users_with_user_role_can_only_update_own_customers()
    {
        Passport::actingAs($this->user);

        $response = $this->post('/api/customers', [
            'first_name' => 'First',
            'last_name'  => 'Last',
            'phone'      => '999-999-9999',
            'email'      => 'first@last.com',
            'note'       => 'This is a test note',
        ]);

        $response->assertStatus(201);

        $user_admin_customer = json_decode($response->getContent(), true);

        $response = $this->post('/api/users', [
            'name'     => 'Test Name',
            'email'    => 'test@test.com',
            'password' => 'test1234',
            'role'     => 'user',
        ]);

        $response->assertStatus(201);
        $user = json_decode($response->getContent(), true);

        Passport::actingAs(User::find($user['id']));
        $response = $this->put('/api/customers/' . $user_admin_customer['id'], ['first_name' => 'Try to update']);
        $response->assertStatus(422);
        $response = json_decode($response->getContent(), true);
        $this->assertEquals('Cannot update other user customer.', $response['error']);
    }
}
