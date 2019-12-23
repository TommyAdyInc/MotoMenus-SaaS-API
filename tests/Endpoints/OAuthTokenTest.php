<?php

namespace Tests\Endpoints;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use UserSeeder;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class OAuthTokenTest extends TestCase
{
    public function setUp(): void
    {
        $this->markTestSkipped('Want to run this local only');

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
        $http = new Client;

        $response = $http->post(config('app.url') . ':40010/api/oauth/token', [
            'form_params' => [
                'grant_type'    => 'password',
                'client_id'     => '2',
                'client_secret' => 'vWoiYeTZONl82Ln4XHLpA1qEPFvPy5AbcAbYcUv2',
                'username'      => 'flastname@motomenus.test',
                'password'      => 'temp1212',
                'scope'         => '',
                'provider'      => 'users',
            ],
        ]);

        $this->assertArrayHasKey('access_token', json_decode((string)$response->getBody(), true)['token']);

        $response = $http->post(config('app.url') . ':40010/api/oauth/token', [
            'form_params' => [
                'grant_type'    => 'password',
                'client_id'     => '2',
                'client_secret' => 'vWoiYeTZONl82Ln4XHLpA1qEPFvPy5AbcAbYcUv2',
                'username'      => 'tommyady@whaterverdomain.com',
                'password'      => 'temp1212',
                'scope'         => '',
                'provider'      => 'superadmins',
            ],
        ]);

        $this->assertArrayHasKey('access_token', json_decode((string)$response->getBody(), true)['token']);
    }
}
