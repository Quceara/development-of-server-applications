<?php

namespace App\DTO;

class PermissionCollectionDTO
{
    public $permissions;

    public function __construct(array $permissions)
    {
        $this->permissions = $permissions;
    }
}
