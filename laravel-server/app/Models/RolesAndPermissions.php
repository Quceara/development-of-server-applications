<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolesAndPermissions extends Model
{
    protected $casts = [
    	'created_by' => 'integer',
    ];

}
