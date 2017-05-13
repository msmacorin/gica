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
            'name' => 'Administrador',
            'email' => 'gica@gica.com',
            'password' => bcrypt(123),
            'remember_token' => str_random(10),
            'administrator' => true,
            'status' => \App\User::ACTIVE,
        ]);
    }

}
