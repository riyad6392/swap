<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // cloth category
        $category = [
            [
                'name' => 'T-shirt',
                'slug' => 't-shirt',
                'parent_id' => null,
                'description' => 'T-shirt',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Shirt',
                'slug' => 'shirt',
                'parent_id' => null,
                'description' => 'Shirt',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Pants',
                'slug' => 'pants',
                'parent_id' => null,
                'description' => 'Pants',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Jacket',
                'slug' => 'jacket',
                'parent_id' => null,
                'description' => 'Jacket',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Shoes',
                'slug' => 'shoes',
                'parent_id' => null,
                'description' => 'Shoes',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Accessories',
                'slug' => 'accessories',
                'parent_id' => null,
                'description' => 'Accessories',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Hat',
                'slug' => 'hat',
                'parent_id' => null,
                'description' => 'Hat',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,

            ],
            [
                'name' => 'Bag',
                'slug' => 'bag',
                'parent_id' => null,
                'description' => 'Bag',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Socks',
                'slug' => 'socks',
                'parent_id' => null,
                'description' => 'Socks',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Underwear',
                'slug' => 'underwear',
                'parent_id' => null,
                'description' => 'Underwear',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Swimwear',
                'slug' => 'swimwear',
                'parent_id' => null,
                'description' => 'Swimwear',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Sportswear',
                'slug' => 'sportswear',
                'parent_id' => null,
                'description' => 'Sportswear',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Sleepwear',
                'slug' => 'sleepwear',
                'parent_id' => null,
                'description' => 'Sleepwear',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Formalwear',
                'slug' => 'formalwear',
                'parent_id' => null,
                'description' => 'Formalwear',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Casualwear',
                'slug' => 'casualwear',
                'parent_id' => null,
                'description' => 'Casualwear',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Outerwear',
                'slug' => 'outerwear',
                'parent_id' => null,
                'description' => 'Outerwear',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Loungewear',
                'slug' => 'loungewear',
                'parent_id' => null,
                'description' => 'Loungewear',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Maternitywear',
                'slug' => 'maternitywear',
                'parent_id' => null,
                'description' => 'Maternitywear',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Petite',
                'slug' => 'petite',
                'parent_id' => null,
                'description' => 'Petite',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'Plus Size',
                'slug' => 'plus-size',
                'parent_id' => null,
                'description' => 'Plus Size',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ]

        ];

        Category::Insert($category);
    }
}
