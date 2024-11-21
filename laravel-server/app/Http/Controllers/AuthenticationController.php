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
use App\Http\Requests\Update2faStatusRequest;
use App\Http\Requests\Verify2faRequest;
use App\Http\Resources\LoginResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use App\DTO\TokenRequestDTO;
use App\DTO\TokenDTO;
use App\Http\Requests\ChangePassword;
use App\DTO\ChangePasswordDTO;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Part\TextPart;

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
    $accessToken = $request->header('Authorization');
    if ($accessToken && str_starts_with($accessToken, 'Bearer ')) {
        $accessToken = str_replace('Bearer ', '', $accessToken);

        $userIdAccess = Redis::get('access_token:' . $accessToken);
        if ($userIdAccess) {
            $this->logout($request);
        }
    }

    $loginDTO = LoginDTO::fromRequest($request);

    $user = User::where('email', $loginDTO->email)->first();
    if (!$user || !Hash::check($loginDTO->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    if (!$user->is_2fa_enabled) {
        $newAccessToken = $this->createToken($user->id, 'access');
        $newRefreshToken = $this->createToken($user->id, 'refresh');

        return response()->json([
            'refresh_token' => $newRefreshToken,
            'access_token' => $newAccessToken,
        ], 200);
    }

    $rateLimitKey = "2fa:rate_limit:{$user->id}";
    $requestCount = Redis::get($rateLimitKey);

    if (!$requestCount) {
        Redis::set($rateLimitKey, 1, 'EX', 30);
    } else {
        if ($requestCount >= 3) {
            return response()->json(['message' => 'Too many requests. Please try again in 30 seconds.'], 429);
        }
        Redis::incr($rateLimitKey);
    }

    $oldKeys = Redis::keys("2fa:{$user->id}:*");

    if (!empty($oldKeys)) {

    	$oldKeys = array_map(function ($k) {
            return str_replace('laravel_database_', '', $k);
    	}, $oldKeys);

    	Redis::del($oldKeys);
    }

    $deviceId = Str::uuid()->toString();
    $hashedDeviceId = hash('sha256', $deviceId);

    $code = random_int(100000, 999999);
    $hashedCode = hash('sha256', $code);

    $key = "2fa:{$user->id}:{$hashedDeviceId}";
    Redis::set($key, json_encode(['code' => $hashedCode]));
    Redis::expire($key, 300);

    Mail::send([], [], function ($message) use ($user, $code) {
        $message->to($user->email)
            ->subject('Your 2FA Code')
            ->text("Your 2FA code is: {$code}")
            ->html("<p>Your 2FA code is: <strong>{$code}</strong></p>");
    });

    return response()->json([
        'device_id' => $deviceId,
        'message' => '2FA code sent to your email. Please confirm it to proceed.',
    ], 200);
}

    public function verify2fa(Verify2faRequest $request)
    {
    	$deviceId = $request->header('Device-ID');
    	if (!$deviceId) {
            return response()->json(['message' => 'Device ID is required'], 400);
    	}

    	if (!$code) {
            return response()->json(['message' => 'Access code is required'], 400);
    	}

    	$hashedDeviceId = hash('sha256', $deviceId);

    	$key = "2fa:{$request->attributes->get('userId')}:{$hashedDeviceId}";
    	$storedCodeData = Redis::get($key);

    	if (!$storedCodeData) {
            return response()->json(['message' => 'Invalid or expired 2FA code'], 400);
    	}

    	$storedCodeData = json_decode($storedCodeData, true);

    	if (hash('sha256', $request->code) !== $storedCodeData['code']) {
            return response()->json(['message' => 'Invalid 2FA code'], 400);
    	}

    	$userId = $request->attributes->get('userId');
    	$user = User::find($userId);

    	if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
    	}

    	$accessToken = $this->createToken($user->id, 'access');
    	$refreshToken = $this->createToken($user->id, 'refresh');

        return response()->json([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
    	], 200);
    }

    public function update2faStatus(Update2faStatusRequest $request)
    {
    	$userId = $request->attributes->get('userId');

    	$user = User::find($userId);

    	if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
    	}

    	if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid password'], 401);
    	}

    	$user->update([
            'is_2fa_enabled' => $request->is_2fa_enabled,
    	]);

    	return response()->json([
            'message' => $request->is_2fa_enabled ? '2FA enabled successfully' : '2FA disabled successfully'
    	], 200);
    }

    public function refreshToken(Request $request)
    {
    	$userId = $request->attributes->get('userId');

    	$newAccessToken = $this->createToken($userId, 'access');

    	return response()->json([
            'access_token' => $newAccessToken
    	], 200);
    }

    public function logout(Request $request)
    {
    	$accessToken = $request->attributes->get('accessToken');
    	$userIdAccess = $request->attributes->get('userId');

        Redis::del('access_token:' . $accessToken);
        Redis::lrem('user_access_tokens:' . $userIdAccess, 1, $accessToken);
        Redis::expire('user_access_tokens:' . $userIdAccess, 3600);

    	return response()->json(['message' => 'Logged out successfully'], 200);
    }

    public function logoutAll(Request $request)
    {
    	$userIdAccess = $request->attributes->get('userId');
    	$userAccessTokens = Redis::lrange('user_access_tokens:' . $userIdAccess, 0, -1);

    	foreach ($userAccessTokens as $token) {
            Redis::del('access_token:' . $token);
    	}

    	Redis::del('user_access_tokens:' . $userIdAccess);

    	return response()->json(['message' => 'Logged out ALL successfully'], 200);
    }

    public function getToken(Request $request)
    {
    	$userId = $request->attributes->get('userId');

    	$user = User::find($userId);

    	if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

    	return response()->json([
            'name' => $user->name,
            'email' => $user->email,
    	], 200);
    }

    public function getAllToken(Request $request)
    {
    	$userId = $request->attributes->get('userId');

    	$accessTokens = Redis::lrange('user_access_tokens:' . $userId, 0, -1);

    	$validAccessTokens = [];

        foreach ($accessTokens as $token) {
            $userIdInRedis = Redis::get('access_token:' . $token);

            if ($userIdInRedis) {
                $validAccessTokens[] = $token;
            } else {
            	Redis::lrem('user_access_tokens:' . $userId, 0, $token);
            }
    	}

    	return response()->json([
            'access_tokens' => $validAccessTokens,
    	], 200);
    }

    public function changePassword(ChangePassword $request)
    {
    	$changePasswordDTO = ChangePasswordDTO::fromRequest($request);

    	$userId = $request->attributes->get('userId');

	$user = User::find($userId);

    	if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
    	}

    	if (!Hash::check($changePasswordDTO->oldPassword, $user->password)) {
            return response()->json(['message' => 'Invalid password'], 401);
    	}

    	$user->password = Hash::make($changePasswordDTO->newPassword);
    	$user->save();

    	return response()->json(['message' => 'Password updated successfully'], 200);
    }

    private function createToken($userId, $tokenType)
    {
    	$tokenDTO = TokenDTO::fromRequest($userId, $tokenType);
    	$tokenCount = Redis::llen('user_' . $tokenType . '_tokens:' . $tokenDTO->userId);

    	while ($tokenCount >= $tokenDTO->maxActiveTokens) {
            $oldToken = Redis::rpop('user_' . $tokenType . '_tokens:' . $tokenDTO->userId);
            Redis::del($tokenType . '_token:' . $oldToken);
            $tokenCount--;
    	}

    	$hashedToken = hash('sha256', $tokenDTO->token);
    	Redis::setex($tokenType . '_token:' . $hashedToken, $tokenDTO->tokenExpiresIn, $tokenDTO->userId);
    	Redis::lpush('user_' . $tokenType . '_tokens:' . $tokenDTO->userId, $hashedToken);
    	Redis::expire('user_' . $tokenType . '_tokens:' . $tokenDTO->userId, $tokenDTO->tokenExpiresIn);

    	return $tokenDTO->token;
    }


}
