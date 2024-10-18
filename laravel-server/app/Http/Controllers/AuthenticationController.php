<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use App\DTO\RegisterDTO;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\RegisterResource;
use App\DTO\LoginDTO;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\LoginResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use App\DTO\TokenRequestDTO;
use App\DTO\TokenDTO;
use App\Http\Requests\ChangePassword;
use App\DTO\ChangePasswordDTO;

class AuthenticationController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $registerDTO = RegisterDTO::fromRequest($request);

        $user = User::create([
            'name' => $registerDTO->name,
            'email' => $registerDTO->email,
            'password' => Hash::make($registerDTO->password)
        ]);
        return response()->json(new RegisterResource($user), 201);
    }

    public function login(LoginRequest $request)
    {
    	$tokenRequestDTO = TokenRequestDTO::fromRequest($request);
    	$loginDTO = LoginDTO::fromRequest($request);

    	$user = User::where('email', $loginDTO->email)->first();

    	if (!Hash::check($loginDTO->password, $user->password)) {
            return response()->json(['message' => 'Invalid password'], 401);
    	}

    	$newAccessToken = $this->createToken($user->id, 'access');
    	$newRefreshToken = $this->createToken($user->id, 'refresh');

    	if ($tokenRequestDTO->refreshToken) {
            $this->logout($request);
    	}

    	return response()->json([
            'refresh_token' => $newRefreshToken,
            'access_token' => $newAccessToken
        ], 200)
            ->cookie('access_token', $newAccessToken, $tokenRequestDTO->accessTokenExpiresIn)
            ->cookie('refresh_token', $newRefreshToken, $tokenRequestDTO->refreshTokenExpiresIn);
    }

    function refreshToken(Request $request)
    {
        $tokenRequestDTO = TokenRequestDTO::fromRequest($request);
        $userId = Redis::get('refresh_token:' . $tokenRequestDTO->refreshToken);

        if (!$userId)
	{
            return response()->json(['message' => 'Invalid refresh token'], 401);
    	}

    	if ($tokenRequestDTO->accessToken)
	{
            Redis::del('access_token:' . $tokenRequestDTO->accessToken);
            Redis::lrem('user_access_tokens:' . $userId, 1, $tokenRequestDTO->accessToken);
    	}

    	$newAccessToken = $this->createToken($userId, 'access');

        return response()->json([
            'access_token' => $newAccessToken
    	], 200)->cookie('access_token', $newAccessToken, $tokenRequestDTO->accessTokenExpiresIn);
    }

    public function logout(Request $request)
    {
    	$tokenRequestDTO = TokenRequestDTO::fromRequest($request);

    	$userIdAccess = Redis::get('access_token:' . $tokenRequestDTO->accessToken);
    	$userIdRefresh = Redis::get('refresh_token:' . $tokenRequestDTO->refreshToken);

    	$accessTokenDTO = TokenDTO::fromRequest($userIdAccess, 'access');
    	$refreshTokenDTO = TokenDTO::fromRequest($userIdRefresh, 'refresh');

   	Redis::del('access_token:' . $tokenRequestDTO->accessToken);
    	Redis::del('refresh_token:' . $tokenRequestDTO->refreshToken);
    	Redis::lrem('user_access_tokens:' . $accessTokenDTO->userId, 1, $tokenRequestDTO->accessToken);
    	Redis::lrem('user_refresh_tokens:' . $refreshTokenDTO->userId, 1, $tokenRequestDTO->refreshToken);
    	Redis::expire('user_refresh_tokens:' . $refreshTokenDTO->userId, $refreshTokenDTO->tokenExpiresIn);
    	Redis::expire('user_access_tokens:' . $accessTokenDTO->userId, $accessTokenDTO->tokenExpiresIn);

        return response()->json(['message' => 'Logged out successfully'], 200)
            ->cookie('access_token', '', -1)
            ->cookie('refresh_token', '', -1);
    }

    public function logoutAll(Request $request)
    {
    	$tokenRequestDTO = TokenRequestDTO::fromRequest($request);

    	$userIdAccess = Redis::get('access_token:' . $tokenRequestDTO->accessToken);
    	$userIdRefresh = Redis::get('refresh_token:' . $tokenRequestDTO->refreshToken);

    	$accessTokenDTO = TokenDTO::fromRequest($userIdAccess, 'access');
    	$refreshTokenDTO = TokenDTO::fromRequest($userIdRefresh, 'refresh');

    	$userAccessTokens = Redis::lrange('user_access_tokens:' . $accessTokenDTO->userId, 0, -1);
    	$userRefreshTokens = Redis::lrange('user_refresh_tokens:' . $refreshTokenDTO->userId, 0, -1);

    	foreach ($userAccessTokens as $token)
	{
            Redis::del('access_token:' . $token);
    	}

    	foreach ($userRefreshTokens as $token)
	{
            Redis::del('refresh_token:' . $token);
    	}

    	Redis::del('user_access_tokens:' . $accessTokenDTO->userId);
    	Redis::del('user_refresh_tokens:' . $refreshTokenDTO->userId);

    	return response()->json(['message' => 'Logged out ALL successfully'], 200)
            ->cookie('access_token', '', -1)
            ->cookie('refresh_token', '', -1);
    }

    public function getToken(Request $request)
    {
    	$tokenRequestDTO = TokenRequestDTO::fromRequest($request);
    	$userId = Redis::get('access_token:' . $tokenRequestDTO->accessToken);

    	$user = User::find($userId);
    	$tokenDTO = TokenDTO::fromRequest($userId, 'access');

    	return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
	    'token' => $tokenRequestDTO->accessToken,
	    'type' => $tokenDTO->tokenType
    	], 200);
    }

    public function getAllToken(Request $request)
    {
    	$tokenRequestDTO = TokenRequestDTO::fromRequest($request);
    	$userId = Redis::get('access_token:' . $tokenRequestDTO->accessToken);

    	$accessTokens = Redis::lrange('user_access_tokens:' . $userId, 0, -1);
    	$refreshTokens = Redis::lrange('user_refresh_tokens:' . $userId, 0, -1);

    	$validAccessTokens = [];
    	$validRefreshTokens = [];

        foreach ($accessTokens as $token) {
            $userIdInRedis = Redis::get('access_token:' . $token);

            if ($userIdInRedis) {
                $validAccessTokens[] = $token;
            } else {
                Redis::lrem('user_access_tokens:' . $userId, 0, $token);
            }
    	}

    	foreach ($refreshTokens as $token) {
            $userIdInRedis = Redis::get('refresh_token:' . $token);

            if ($userIdInRedis) {
                $validRefreshTokens[] = $token;
            } else {
                Redis::lrem('user_refresh_tokens:' . $userId, 0, $token);
            }
    	}

    	return response()->json([
            'access_tokens' => $validAccessTokens,
            'refresh_tokens' => $validRefreshTokens,
    	], 200);
    }

    public function changePassword(ChangePassword $request)
    {
	$tokenRequestDTO = TokenRequestDTO::fromRequest($request);
	$ChangePasswordDTO = ChangePasswordDTO::fromRequest($request);

	$userId = Redis::get('access_token:' . $tokenRequestDTO->accessToken);

        $user = User::where('id', $userId)->first();

        if (!Hash::check($ChangePasswordDTO->oldPassword, $user->password)) {
            return response()->json(['message' => 'Invalid password'], 401);
        }

	$user->password = Hash::make($ChangePasswordDTO->newPassword);
	$user->save();

	return response()->json(['message' => 'Password updated successfully'], 200);
    }

    public function createToken($userId, $tokenType)
    {
        $tokenDTO = TokenDTO::fromRequest($userId, $tokenType);
        $tokenCount = Redis::llen('user_' . $tokenType . '_tokens:' . $tokenDTO->userId);

        if ($tokenCount >= $tokenDTO->maxActiveTokens)
        {
            $oldToken = Redis::rpop('user_' . $tokenType . '_tokens:' . $tokenDTO->userId);
            Redis::del($tokenType . '_token:' . $oldToken);
        }

        Redis::setex($tokenType . '_token:' . $tokenDTO->token, $tokenDTO->tokenExpiresIn, $tokenDTO->userId);
        Redis::lpush('user_' . $tokenType . '_tokens:' . $tokenDTO->userId, $tokenDTO->token);
        Redis::expire('user_' . $tokenType . '_tokens:' . $tokenDTO->userId, $tokenDTO->tokenExpiresIn);

        return $tokenDTO->token;
    }

}

