<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        \App\User::create([
            'name' => 'Macorin',
            'email' => 'marcelo@macor.in',
            'password' => bcrypt(123),
            'remember_token' => str_random(10)
        ]);
    }

}
