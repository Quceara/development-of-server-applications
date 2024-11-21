<?php

namespace Database\Seeders;
use App\Models\Role;
use App\Models\Permission;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);

        $admin = Role::where('slug', 'admin')->first();
        $user = Role::where('slug', 'user')->first();
        $guest = Role::where('slug', 'guest')->first();

        $allPermissions = Permission::all();

        $admin->permissions()->sync($allPermissions);

        $user->permissions()->attach([
            Permission::where('slug', 'get-list-user')->first()->id,
            Permission::where('slug', 'read-user')->first()->id,
            Permission::where('slug', 'update-user')->first()->id,
        ]);

        $guest->permissions()->attach(Permission::where('slug', 'get-list-user')->first()->id);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
