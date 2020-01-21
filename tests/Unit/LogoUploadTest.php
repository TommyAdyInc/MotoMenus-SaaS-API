<?php

namespace Tests\Unit;

use App\User;
use Illuminate\Http\UploadedFile;
use Laravel\Passport\Passport;
use Tests\TestCase;
use UserSeeder;

class LogoUploadTest extends TestCase
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
    public function creates_a_store_logo_successfully()
    {
        Passport::actingAs($this->user);

        $this->json('POST', '/api/settings/logo', [
            'file' => UploadedFile::fake()->image('mylogo.jpg', 1200, 800)
        ])
            ->assertStatus(201);
    }
}
