<?php

use App\StoreSetting;
use Illuminate\Database\Seeder;

class StoreSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        StoreSetting::create([
            'default_interest_rate' => 13.49,
        ]);
    }
}
