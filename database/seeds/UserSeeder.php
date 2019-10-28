<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!User::where('email', 'flastname@motomenus.test')->first()) {
            User::create([
                'name'   => 'First LastName',
                'email'        => 'flastname@motomenus.test',
                'password'     => Hash::make('temp1212'),
            ]);
        }
    }
}
