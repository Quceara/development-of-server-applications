<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class CheckRefreshToken
{
    public function handle(Request $request, Closure $next)
    {
        $refreshToken = $request->header('X-Refresh-Token');

        if (!$refreshToken) {
            return response()->json(['message' => 'Refresh token is missing'], 401);
        }

        $hashedToken = hash('sha256', $refreshToken);

        $userId = Redis::get('refresh_token:' . $hashedToken);

        if (!$userId) {
            return response()->json(['message' => 'Invalid refresh token'], 401);
        }

        $request->attributes->set('userId', $userId);

        return $next($request);
    }
}
