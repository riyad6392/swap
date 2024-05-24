<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'brand' => [
                'brand.index',
                'brand.create',
                'brand.edit',
                'brand.delete',
            ],
            'category' => [
                'category.index',
                'category.create',
                'category.edit',
                'category.delete',
            ],
            'color' => [
                'color.index',
                'color.create',
                'color.edit',
                'color.delete',
            ],
            'size' => [
                'size.index',
                'size.create',
                'size.edit',
                'size.delete',
            ],

        ];

    }
}
