<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class CheckAccessToken
{
    public function handle(Request $request, Closure $next)
    {
        $accessToken = $request->header('Authorization');

        if (!$accessToken || !str_starts_with($accessToken, 'Bearer ')) {
            return response()->json(['message' => 'Access token missing or invalid'], 401);
        }

        $accessToken = str_replace('Bearer ', '', $accessToken);

        $hashedAccessToken = hash('sha256', $accessToken);

        $userIdAccess = Redis::get('access_token:' . $hashedAccessToken);

        if (!$userIdAccess) {
            return response()->json(['message' => 'Invalid access token'], 401);
        }

        $request->attributes->set('userId', $userIdAccess);
        $request->attributes->set('accessToken', $hashedAccessToken);

        return $next($request);
    }
}
