<?php

use Illuminate\Database\Seeder;
use App\PostType;

class PostTypeTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        PostType::create([
            'name' => 'Iluminação',
            'icon' => 'img/type_icons/small-light-bulb.png',
        ]);
        PostType::create([
            'name' => 'Rua deserta',
            'icon' => 'img/type_icons/road-perspective.png',
        ]);
        PostType::create([
            'name' => 'Assédio',
            'icon' => 'img/type_icons/thumb-down.png',
        ]);
    }

}
