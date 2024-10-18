<?php

namespace App\DTO;
use Illuminate\Support\Str;

class TokenDTO
{
    public $userId;
    public $maxActiveTokens;
    public $token;
    public $tokenExpiresIn;
    public $tokenType;

    public function __construct($userId, $tokenType)
    {
        $this->userId = $userId;
        $this->tokenType = $tokenType;
        $this->maxActiveTokens = $this->getMaxActiveTokensFromEnv();
        $this->tokenExpiresIn = $this->determineTokenExpiry();
	$this->token = $this->getToken();
    }

    private function getToken()
    {
	return $this->tokenType == 'access' ? Str::random(8) : Str::random(16);
    }

    private function getMaxActiveTokensFromEnv()
    {
        return env('MAX_ACTIVE_TOKENS');
    }

    private function determineTokenExpiry()
    {
        return $this->tokenType == 'access' ? env('ACC_TOKEN_EXP_IN_REDIS') : env('REFRESH_TOKEN_EXP_IN_REDIS');
    }

    public static function fromRequest($userId, $tokenType)
    {
        return new self($userId, $tokenType);
    }
}
