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
            'icon_class' => 'map-icon-circle map-icon-general-contractor',
        ]);
        PostType::create([
            'name' => 'Rua deserta',
            'icon_class' => 'map-icon-square map-icon-crosshairs',
        ]);
        PostType::create([
            'name' => 'Assédio',
            'icon_class' => 'map-icon-shield map-icon-physiotherapist',
        ]);
    }

}
