<?php

namespace App\DTO;

class RoleCollectionDTO
{
    public $roles;

    public function __construct(array $roles)
    {
        $this->roles = $roles;
    }
}
