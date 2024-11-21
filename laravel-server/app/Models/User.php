<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;
use App\Models\ChangeLog;
use App\LogsChanges;

class User extends Authenticatable
{
    use HasFactory, Notifiable, LogsChanges;

    protected $fillable = [
        'name',
        'email',
        'password',
	'is_2fa_enabled'
    ];

    protected $hidden = [

	'created_at',
        'updated_at',
        'password',
        'remember_token',
    ];

    protected $casts = [
	'email_verified_at' => 'datetime',
	'created_at' => 'datetime',
    	'updated_at' => 'datetime',
	'is_2fa_enabled' => 'boolean',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role')
            ->withTimestamps()
            ->withPivot('deleted_at', 'created_by', 'deleted_by');
    }

    public function permissions()
    {
        return $this->roles()->with('permissions')->get()
            ->flatMap(function ($role) {
                return $role->permissions;
            })->pluck('name')->unique();
    }

    public function hasPermission($permission)
    {
    	foreach ($this->roles as $role) {
            if ($role->permissions->contains('slug', $permission)) {
            	return true;
            }
    	}
    	return false;
    }

    protected static function restored()
    {
        // Этот метод ничего не делает, но он будет вызываться при восстановлении пользователя
    }

/*    public function logChangeCreated(): void
    {
    	\App\Models\ChangeLog::create([
            'entity_type' => get_class($this),
            'entity_id' => $this->id,
            'old_values' => null,
            'new_values' => array_diff_key($this->getAttributes(), array_flip($this->getHidden())),
            'user_id' => $this->id,
            'action' => 'created',
    	]);
    }

    protected static function booted()
    {
        static::created(function ($user) {
            $guestRole = Role::where('slug', 'guest')->first();
            if ($guestRole) {
            	$user->roles()->syncWithoutDetaching([$guestRole->id]);
            }
		$user->logChangeCreated();
    	});

    }*/
}
