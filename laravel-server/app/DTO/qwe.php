<?php

namespace App\DTOs;

class qwe
{

    public function __construct()
    {
    }

    public static function fromRequest($request): qwe
    {
        return new self();
    }
}
