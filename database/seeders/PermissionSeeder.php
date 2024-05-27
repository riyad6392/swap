<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

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
            'user' => [
                'user.index',
                'user.create',
                'user.edit',
                'user.delete',
            ],
        ];

        foreach ($permissions as $group => $permission) {
            foreach ($permission as $name) {
                Permission::create([
                    'name' => $name,
                    'group' => $group,
                    'guard_name' => 'web',
                ]);
            }
        }

    }
}
