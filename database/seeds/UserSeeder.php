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
            $user = User::create([
                'name'   => 'First LastName',
                'email'        => 'flastname@motomenus.test',
                'password'     => 'temp1212',
            ]);

            $user->user_role()->create(['role' => 'admin']);
        }
    }
}
