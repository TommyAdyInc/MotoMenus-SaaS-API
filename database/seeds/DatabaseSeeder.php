<?php

use App\StoreSetting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(StoreSetting::class);
        $this->call(UserSeeder::class);
    }
}
