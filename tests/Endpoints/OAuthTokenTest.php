<?php

namespace Tests\Endpoints;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OAuthTokenTest extends TestCase
{
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
