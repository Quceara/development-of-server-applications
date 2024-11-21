<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class EnsureUserLoggedOut
{
    public function handle(Request $request, Closure $next)
    {
        $accessToken = $request->header('Authorization');

        if ($accessToken && str_starts_with($accessToken, 'Bearer ')) {

            $accessToken = str_replace('Bearer ', '', $accessToken);
            $userIdAccess = Redis::get('access_token:' . $accessToken);

            if ($userIdAccess) {
                return response()->json(['message' => 'log out of your account first'], 400);
            }
        }

        return $next($request);
    }
}
