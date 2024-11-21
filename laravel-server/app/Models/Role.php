<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\LogsChanges;
use App\Models\ChangeLog;

class Role extends Model
{
    use HasFactory, LogsChanges, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'created_by',
        'deleted_by',
    ];

    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    protected $casts = [
        'created_by' => 'integer',
        'deleted_by' => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'roles_and_permissions');
    }
}
