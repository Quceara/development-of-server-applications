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
        // Извлечение токенов из заголовков
        $accessToken = $request->header('Authorization') ? str_replace('Bearer ', '', $request->header('Authorization')) : null;
        $refreshToken = $request->header('X-Refresh-Token');

        // Получение времени жизни токенов из переменных окружения
        $accessTokenExpiresIn = env('ACC_TOKEN_EXP_IN_REDIS');
        $refreshTokenExpiresIn = env('REFRESH_TOKEN_EXP_IN_REDIS');

        return new self($accessToken, $refreshToken, $accessTokenExpiresIn, $refreshTokenExpiresIn);
    }
}
