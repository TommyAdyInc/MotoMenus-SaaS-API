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

    /**
     * @test
     *
     * @return void
     */
    public function creates_a_deal_pdf_successfully()
    {
        Passport::actingAs($this->user);

        $this->json('POST', '/api/deal', [
            'user_id'      => $this->user->id,
            'customer'     => [
                'first_name' => 'some_name',
                'last_name'  => 'some_name',
                'email'      => 'some_name@test.com',
                'phone'      => '2344567890'
            ],
            'sales_status' => 'Greeting',
        ])->assertStatus(201);

        $response = $this->json('GET', '/api/pdf/1', ['type' => 'deal']);
        $response->assertStatus(201);

        $pdf = base64_decode($response->getContent());

        file_put_contents(storage_path('test.pdf'), $pdf);
    }
}
