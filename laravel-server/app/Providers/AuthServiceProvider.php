<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Role;
use App\Models\User;
use App\Models\Permission;

class AuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //$this->registerPolicies();

        Gate::define('get-list-user', function (User $user) {
            return $user->hasPermission('get-list-user');
        });

        Gate::define('read-user', function (User $user) {
            return $user->hasPermission('read-user');
        });

        Gate::define('update-user', function (User $user) {
            return $user->hasPermission('update-user');
        });

        Gate::define('admin-actions', function (User $user) {
            return $user->role->name == 'Admin';
        });
    }
}
