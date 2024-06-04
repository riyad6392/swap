<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // colors for clothes

        $colors = [
            [
                'name' => 'Red',
                'slug' => 'red',
                'color_code' => '#FF0000',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Green',
                'slug' => 'green',
                'color_code' => '#008000',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Blue',
                'slug' => 'blue',
                'color_code' => '#0000FF',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Yellow',
                'slug' => 'yellow',
                'color_code' => '#FFFF00',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Orange',
                'slug' => 'orange',
                'color_code' => '#FFA500',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Purple',
                'slug' => 'purple',
                'color_code' => '#800080',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Pink',
                'slug' => 'pink',
                'color_code' => '#FFC0CB',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Brown',
                'slug' => 'brown',
                'color_code' => '#A52A2A',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Black',
                'slug' => 'black',
                'color_code' => '#000000',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'White',
                'slug' => 'white',
                'color_code' => '#FFFFFF',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Gray',
                'slug' => 'gray',
                'color_code' => '#808080',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Beige',
                'slug' => 'beige',
                'color_code' => '#F5F5DC',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Maroon',
                'slug' => 'maroon',
                'color_code' => '#800000',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Navy',
                'slug' => 'navy',
                'color_code' => '#000080',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Teal',
                'slug' => 'teal',
                'color_code' => '#008080',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Olive',
                'slug' => 'olive',
                'color_code' => '#808000',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Lime',
                'slug' => 'lime',
                'color_code' => '#00FF00',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Cyan',
                'slug' => 'cyan',
                'color_code' => '#00FFFF',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Magenta',
                'slug' => 'magenta',
                'color_code' => '#FF00FF',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Silver',
                'slug' => 'silver',
                'color_code' => '#C0C0C0',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Gold',
                'slug' => 'gold',
                'color_code' => '#FFD700',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
        ];

        Color::Insert($colors);
    }
}
