<?php

namespace App\DTOs;

class GetUserDTO
{


    public function __construct()
    {
    }

    public static function fromRequest($request): GetUserDTO
    {
        return new self();
    }
}
