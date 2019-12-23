<?php


use App\SuperAdmin;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!SuperAdmin::where('email', 'tommyady@whaterverdomain.com')->first()) {
            SuperAdmin::create([
                'name'     => 'Tommy Ady',
                'email'    => 'tommyady@whaterverdomain.com',
                'password' => 'temp1212',
            ]);
        }
    }
}
