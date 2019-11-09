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
}
