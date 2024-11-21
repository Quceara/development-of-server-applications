<?php
// database/seeders/PermissionSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [

            ['name' => 'get-list-user', 'slug' => 'get-list-user', 'description' => 'Get list of users'],
            ['name' => 'read-user', 'slug' => 'read-user', 'description' => 'Read user details'],
            ['name' => 'create-user', 'slug' => 'create-user', 'description' => 'Create a new user'],
            ['name' => 'update-user', 'slug' => 'update-user', 'description' => 'Update user details'],
            ['name' => 'delete-user', 'slug' => 'delete-user', 'description' => 'Delete a user'],

            ['name' => 'get-list-role', 'slug' => 'get-list-role', 'description' => 'Get list of roles'],
            ['name' => 'read-role', 'slug' => 'read-role', 'description' => 'Read role details'],
            ['name' => 'create-role', 'slug' => 'create-role', 'description' => 'Create a new role'],
            ['name' => 'update-role', 'slug' => 'update-role', 'description' => 'Update role details'],
            ['name' => 'delete-role', 'slug' => 'delete-role', 'description' => 'Delete a role'],

            ['name' => 'get-list-permission', 'slug' => 'get-list-permission', 'description' => 'Get list of permissions'],
            ['name' => 'read-permission', 'slug' => 'read-permission', 'description' => 'Read permission details'],
            ['name' => 'create-permission', 'slug' => 'create-permission', 'description' => 'Create a new permission'],
            ['name' => 'update-permission', 'slug' => 'update-permission', 'description' => 'Update permission details'],
            ['name' => 'delete-permission', 'slug' => 'delete-permission', 'description' => 'Delete a permission'],

            ['name' => 'restore-user', 'slug' => 'restore-user', 'description' => 'Restore a soft-deleted user'],
            ['name' => 'soft-delete-role', 'slug' => 'soft-delete-role', 'description' => 'Soft delete a role'],
            ['name' => 'restore-role', 'slug' => 'restore-role', 'description' => 'Restore a soft-deleted role'],
            ['name' => 'soft-delete-permission', 'slug' => 'soft-delete-permission', 'description' => 'Soft delete a permission'],
            ['name' => 'restore-permission', 'slug' => 'restore-permission', 'description' => 'Restore a soft-deleted permission'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['slug' => $permission['slug']], $permission);
        }
    }
}
