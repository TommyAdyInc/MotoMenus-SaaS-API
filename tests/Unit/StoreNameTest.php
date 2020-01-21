<?php

namespace Tests\Unit;

use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use UserSeeder;

class StoreNameTest extends TestCase
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
    public function admins_or_super_admins_can_update_store_name()
    {
        Passport::actingAs($this->user);

        $this->json('PUT', '/api/settings/store-name', [
            'name' => 'Another Store Name'
        ])
            ->assertStatus(201);
    }
}
