<?php

namespace App\DTOs;

class RefreshTokenRequest.php
{


    public function __construct()
    {

    }

    public static function fromRequest($request): RefreshTokenRequest.php
    {
        return new self();
    }
}
