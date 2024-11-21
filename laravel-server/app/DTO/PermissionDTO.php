<?php

namespace App\DTO;

class PermissionDTO
{
    public $id;
    public $name;
    public $slug;
    public $description;

    public function __construct($id, $name, $slug, $description)
    {
        $this->id = $id;
        $this->name = $name;
        $this->slug = $slug;
        $this->description = $description;
    }
}
