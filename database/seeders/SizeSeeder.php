<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // cloth sizes
        $sizes = [
            [
                'name' => 'XS',
                'slug' => 'xs',
                'description' => 'Extra Small',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'S',
                'slug' => 's',
                'description' => 'Small',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'M',
                'slug' => 'm',
                'description' => 'Medium',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'L',
                'slug' => 'l',
                'description' => 'Large',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'XL',
                'slug' => 'xl',
                'description' => 'Extra Large',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
            [
                'name' => 'XXL',
                'slug' => 'xxl',
                'description' => 'Extra Extra Large',
                'created_by' => '1',
                'updated_by' => '1',
                'is_published' =>false,
            ],
        ];

        Size::Insert($sizes);
    }
}
