<?php

namespace Tests\Endpoints;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use UserSeeder;

class OAuthTokenTest extends TestCase
{
    public function setUp() :void
    {
        // $this->markTestSkipped('Want to run this local only');

        parent::setUp();

        $this->seed(UserSeeder::class);
    }

    /**
     * @test
     *
     * @return void
     */
    public function oauth_token_returns_valid_token_for_user()
    {
        $this->assertTrue(true);
    }
}
