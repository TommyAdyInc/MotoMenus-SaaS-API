<?php

namespace Tests\Endpoints;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use UserSeeder;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class UsersTest extends TestCase
{
    const URI = 'http://local-phpunit.dev.api.motomenus.local:40010';

    private $access_token;

    public function setUp(): void
    {
        $this->markTestSkipped('Want to run this local only');

        parent::setUp();

        $this->seed(UserSeeder::class);

        $http = new Client;

        $response = $http->post(self::URI . '/oauth/token', [
            'form_params' => [
                'grant_type'    => 'password',
                'client_id'     => '2',
                'client_secret' => 'vWoiYeTZONl82Ln4XHLpA1qEPFvPy5AbcAbYcUv2',
                'username'      => 'flastname@motomenus.test',
                'password'      => 'temp1212',
                'scope'         => '',
            ],
        ]);

        $this->access_token = json_decode((string)$response->getBody(), true)['access_token'];
    }

    /**
     * @test
     *
     * @return void
     */
    public function create_new_user_returns_user_with_role()
    {
        $http = new Client;

        $response = $http->post(self::URI . '/api/users', [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $this->access_token
            ],
            'json'    => [
                'name'     => 'Test Name',
                'email'    => 'test@test.com',
                'password' => 'test1234',
                'role'     => 'user',
            ],
        ]);

        $response = json_decode((string)$response->getBody(), true);
        $this->assertArrayHasKey('id', $response);
        $this->assertEquals('user', $response['user_role']['role']);
    }

    /** @test * */
    function updating_user_returns_updated_customer_with_role()
    {
        $http = new Client;

        $response = $http->post(self::URI . '/api/users', [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $this->access_token
            ],
            'json'    => [
                'name'     => 'Test Name',
                'email'    => 'test@test.com',
                'password' => 'test1234',
                'role'     => 'user',
            ],
        ]);

        $response = json_decode((string)$response->getBody(), true);
        $this->assertArrayHasKey('id', $response);

        $response = $http->put(self::URI . '/api/users/' . $response['id'], [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $this->access_token
            ],
            'json'    => [
                'name' => 'My Updated Name',
                'role' => 'admin'
            ],
        ]);

        $response = json_decode((string)$response->getBody(), true);
        $this->assertEquals('My Updated Name', $response['name']);
        $this->assertEquals('admin', $response['user_role']['role']);
    }
}
