<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Models\User;

class CheckAuthToken
{
    public function handle(Request $request, Closure $next)
    {
        $authorizationHeader = $request->header('Authorization');

        if (!$authorizationHeader) {
            return response()->json(['message' => 'Invalid Authorization Header'], 401);
        }

        $token = substr($authorizationHeader, 7); // Удаляем 'Bearer '

        $userId = Redis::get('token:' . $token);

        if (!$userId) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        auth()->login($user);

        return $next($request);
    }
}
