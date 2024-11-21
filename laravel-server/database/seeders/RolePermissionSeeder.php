<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $userRole = Role::firstOrCreate(['name' => 'User']);
        $guestRole = Role::firstOrCreate(['name' => 'Guest']);

        $permissions = [
            ['name' => 'get-list-user', 'description' => 'Get list of users'],
            ['name' => 'read-user', 'description' => 'Read user data'],
            ['name' => 'update-user', 'description' => 'Update user data'],
        ];

        foreach ($permissions as $permissionData) {
            $permission = Permission::firstOrCreate($permissionData);

            $adminRole->permissions()->syncWithoutDetaching($permission);
            if ($permissionData['name'] == 'get-list-user') {
                $guestRole->permissions()->syncWithoutDetaching($permission);
                $userRole->permissions()->syncWithoutDetaching($permission);
            } elseif (in_array($permissionData['name'], ['read-user', 'update-user'])) {
                $userRole->permissions()->syncWithoutDetaching($permission);
            }
        }
    }
}
