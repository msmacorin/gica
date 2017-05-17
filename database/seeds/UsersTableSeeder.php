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
            'email' => 'giovana.razeira@gmail.com',
            'password' => bcrypt('2i37Yx9hDz'),
            'remember_token' => str_random(10),
            'administrator' => true,
            'status' => \App\User::ACTIVE,
        ]);
    }

}
