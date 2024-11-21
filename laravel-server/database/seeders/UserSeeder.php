<?php

// database/seeders/UserSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {
        $adminUser = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin',
            'password' => bcrypt('Admin'),
        ]);

        $adminRole = Role::where('slug', 'admin')->first();
        $adminUser->roles()->syncWithoutDetaching([$adminRole->id]);
    }
}
