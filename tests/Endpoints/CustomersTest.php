<?php

namespace Tests\Endpoints;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use UserSeeder;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class CustomersTest extends TestCase
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
    public function create_new_customer_returns_customer()
    {
        $http = new Client;

        $response = $http->post(self::URI . '/api/customers', [
            'headers'     => [
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $this->access_token
            ],
            'json' => [
                'first_name' => 'First',
                'last_name'  => 'Last',
                'phone'      => '999-999-9999',
                'email'      => 'first@last.com',
            ],
        ]);

        $this->assertArrayHasKey('id', json_decode((string)$response->getBody(), true));
    }

    /** @test * */
    function updating_customer_returns_updated_customer()
    {
        $http = new Client;

        $response = $http->post(self::URI . '/api/customers', [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $this->access_token
            ],
            'json'    => [
                'first_name' => 'First',
                'last_name'  => 'Last',
                'phone'      => '999-999-9999',
                'email'      => 'first@last.com',
            ],
        ]);

        $response = json_decode((string)$response->getBody(), true);
        $this->assertArrayHasKey('id', $response);

        $response = $http->put(self::URI . '/api/customers/' . $response['id'], [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $this->access_token
            ],
            'json'    => [
                'first_name' => 'FirstName',
            ],
        ]);

        $response = json_decode((string)$response->getBody(), true);
        $this->assertEquals('FirstName', $response['first_name']);
    }
}
