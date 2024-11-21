<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            'Admin' => Permission::all(),
            'User' => Permission::whereIn('slug', ['get-list-user', 'read-user', 'update-user'])->get(),
            'Guest' => Permission::whereIn('slug', ['get-list-user'])->get(),
        ];

        foreach ($roles as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName], [
                'slug' => strtolower($roleName),
                'description' => $roleName . ' role'
            ]);
            $role->permissions()->sync($permissions->pluck('id')->toArray());
        }
    }
}
