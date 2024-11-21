<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ChangeLog;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\LogsChanges;

class Permission extends Model
{
    use HasFactory, SoftDeletes, LogsChanges;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'created_by',
        'deleted_by',
    ];
    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at', 'created_by', 'deleted_by'
    ];
    protected $casts = [
        'created_by' => 'integer',
        'deleted_by' => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
}
