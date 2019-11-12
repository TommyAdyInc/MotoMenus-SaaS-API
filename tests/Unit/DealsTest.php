<?php

namespace Tests\Unit;

use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use UserSeeder;

class DealsTest extends TestCase
{
    private $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(UserSeeder::class);

        $this->user = User::find(1);
    }

    private function validParams($overrides = [])
    {
        return array_merge([
            'user_id'     => $this->user->id,
            'customer'    => [
                'first_name' => 'some_name',
                'last_name'  => 'some_name',
                'email'      => 'some_name@test.com',
                'phone'      => '2344567890'
            ],
            'sales_status' => 'Greeting',
        ], $overrides);
    }

    /** @test * */
    function having_wrong_keys_in_filter_for_customer_fails_validation()
    {
        Passport::actingAs($this->user);

        $response = $this->json('GET', '/api/deal', [
            'customer' => [
                'some_name'
            ]
        ]);

        $response->assertStatus(422);

        $response = $this->json('GET', '/api/deal', [
            'customer' => [
                'name' => 'some_name'
            ]
        ]);

        $response->assertStatus(422);

        $response = $this->json('GET', '/api/deal', [
            'customer' => [
                'first_name' => 'some_name',
                'last_name'  => 'some name',
                'phone'      => '2345678909',
            ]
        ]);

        $response->assertStatus(201);
    }

    /** @test * */
    function user_cannot_create_deal_for_other_user()
    {
        $user1 = User::create([
            'name'     => 'Test Name',
            'email'    => 'test@test.com',
            'password' => 'test1234',
            'role'     => 'user',
        ]);

        $user2 = User::create([
            'name'     => 'Test Name two',
            'email'    => 'testtwo@test.com',
            'password' => 'test1234',
            'role'     => 'user',
        ]);

        Passport::actingAs($user1);

        $response = $this->json('POST', '/api/deal', $this->validParams([
            'user_id' => $user2->id,
        ]));

        $response->assertStatus(422)
            ->assertJsonFragment(['User not allowed to create deal for other user']);
    }

    /** @test * */
    function creating_a_deal_requires_a_customer()
    {
        Passport::actingAs($this->user);

        $response = $this->json('POST', '/api/deal', $this->validParams([
            'customer' => [
                'some_name'
            ]
        ]));

        $response->assertStatus(422);

        $customer = $this->user->customers()->create([
            'first_name' => 'First',
            'last_name'  => 'Last',
            'phone'      => '999-999-9999',
            'email'      => 'first@last.com',
        ]);

        $response = $this->json('POST', '/api/deal', $this->validParams([
            'customer' => [
                'id' => $customer->id,
            ]
        ]));

        $response->assertStatus(201);
    }

    /** @test * */
    function user_id_must_match_customer_for_user()
    {
        Passport::actingAs($this->user);

        $customer = $this->user->customers()->create([
            'first_name' => 'First',
            'last_name'  => 'Last',
            'phone'      => '999-999-9999',
            'email'      => 'first@last.com',
        ]);

        $user1 = User::create([
            'name'     => 'Test Name',
            'email'    => 'test@test.com',
            'password' => 'test1234',
            'role'     => 'user',
        ]);

        $response = $this->json('POST', '/api/deal', $this->validParams([
            'user_id'  => $user1->id,
            'customer' => [
                'id' => $customer->id,
            ]
        ]));

        $response->assertStatus(422)
            ->assertJsonFragment(['Customer must belong to user']);
    }

    /** @test * */
    function sales_status_must_be_one_of_valid_options()
    {
        $this->json('POST', '/api/deal', $this->validParams(['sales_status' => 'F&I']))
            ->assertStatus(201);

        $this->json('POST', '/api/deal', $this->validParams(['sales_status' => 'Not valid status']))
            ->assertStatus(422)
            ->assertJsonFragment(['The selected sale status is invalid.']);
    }

    /** @test * */
    function payment_options_must_be_valid_selections()
    {
        Passport::actingAs($this->user);

        $this->json('POST', '/api/deal', $this->validParams([
            'payment_options' => 'not an array'
        ]))
            ->assertStatus(422)
            ->assertJsonFragment(['The payment options must be an array.']);


        $this->json('POST', '/api/deal', $this->validParams([
            'payment_options' => [
                'down_payment_options' => [1000, 2000, 'not a number']
            ]
        ]))
            ->assertStatus(422)
            ->assertJsonFragment(['The payment_options.down_payment_options.2 must be a number.']);

        $this->json('POST', '/api/deal', $this->validParams([
            'payment_options' => [
                'months' => [65]
            ]
        ]))
            ->assertStatus(422)
            ->assertJsonFragment(['The selected payment_options.months.0 is invalid.']);
    }

    /** @test * */
    function customer_types_must_be_valid_options()
    {
        Passport::actingAs($this->user);

        $this->json('POST', '/api/deal', $this->validParams([
            'customer_type' => [
                'Dead',
                'Not a valid option'
            ]
        ]))
            ->assertStatus(422)
            ->assertJsonFragment(['Customer types must match any of ' . join(',', config('customer_types'))]);
    }

    /** @test * */
    function it_creates_a_deal_with_correct_data_for_user_and_customer()
    {
        Passport::actingAs($this->user);

        $this->json('POST', '/api/deal', $this->validParams())
            ->assertStatus(201);
    }

    // TODO: add tests for Units, Trades, Accessories, Purchase Info, Payment Schedule and F&I
}
