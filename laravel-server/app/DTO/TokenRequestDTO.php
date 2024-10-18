<?php

namespace App\DTO;

class TokenRequestDTO
{
    public $accessToken;
    public $refreshToken;
    public $accessTokenExpiresIn;
    public $refreshTokenExpiresIn;

    public function __construct($accessToken, $refreshToken, $accessTokenExpiresIn, $refreshTokenExpiresIn)
    {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->accessTokenExpiresIn = $accessTokenExpiresIn;
        $this->refreshTokenExpiresIn = $refreshTokenExpiresIn;
    }

    public static function fromRequest($request)
    {
        $accessToken = $request->cookie('access_token');
        $refreshToken = $request->cookie('refresh_token');

        $accessTokenExpiresIn = env('ACC_TOKEN_EXP_IN_COOKIE');
        $refreshTokenExpiresIn = env('REFRESH_TOKEN_EXP_IN_COOKIE');

        return new self($accessToken, $refreshToken, $accessTokenExpiresIn, $refreshTokenExpiresIn);
    }
}
