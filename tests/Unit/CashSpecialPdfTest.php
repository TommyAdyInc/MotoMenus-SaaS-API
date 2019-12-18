<?php

namespace Tests\Unit;

use App\User;
use CashSpecialSeeder;
use Laravel\Passport\Passport;
use Tests\TestCase;
use UserSeeder;

class CashSpecialPdfTest extends TestCase
{
    private $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(UserSeeder::class);
        $this->seed(CashSpecialSeeder::class);

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

        $response = $this->json('GET', '/api/pdf/cash-special');
        $response
            // ->dump()
            ->assertStatus(201);

        // check local if PDF created correctly
        // $pdf = base64_decode($response->getContent());

        // file_put_contents(storage_path('test.pdf'), $pdf);
    }
}
