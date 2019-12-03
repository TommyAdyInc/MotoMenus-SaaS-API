<?php

use App\StoreSetting;
use Illuminate\Database\Seeder;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(StoreSettingsSeeder::class);
        $this->call(UserSeeder::class);
    }
}