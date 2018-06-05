<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create(['name' => 'Daniel', 'email' => 'admin@craftimes.com', 'password' => Hash::make('123456')]);
    }
}
