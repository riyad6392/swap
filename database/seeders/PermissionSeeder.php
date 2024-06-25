<?php

namespace Database\Seeders;

use App\Models\Admin;
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
            'admin' => [
                'admin.index',
                'admin.create',
                'admin.edit',
                'admin.delete',
            ],
            'role' => [
                'role.index',
                'role.create',
                'role.edit',
                'role.delete',
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
        $user = Admin::create([
            'name' => 'Imtiaz Ur Rahman Khan',
            'phone' => '01516174119',
            'email' => 'k.r.imtiaz@gmail.com',
            'password' => Hash::make('password')
        ]);
//
        $role = Role::create(['name' => 'Super Admin', 'guard_name' => 'admin-api']);
        $role->givePermissionTo(Permission::all());
        $user->assignRole($role);
    }


}
