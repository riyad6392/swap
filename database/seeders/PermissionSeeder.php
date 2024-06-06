<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
                    'guard_name' => 'admin-api',
                ]);
            }
        }



//            // Create admin User and assign the role to him.
//            $user = User::create([
//                'first_name' => 'riyad',
//                'last_name' => 'riyad',
//                'phone' => '017777777',
//                'email' => 'riyadgp@gmail.com',
//                'is_approved_by_admin'=>true,
//                'password' => Hash::make('password')
//            ]);
//
//            $role = Role::create(['name' => 'Admin']);
//
//            $permissions = Permission::pluck('id', 'id')->all();
//
//            $role->syncPermissions($permissions);
//
//            $user->assignRole([$role->id]);
    }


}
