<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redis;

class RefreshCheckTokens
{
    public function handle(Request $request, Closure $next): Response
    {
        $refreshToken = $request->cookie('refresh_token');

        $userId = Redis::get('refresh_token:' . $refreshToken);

        if (!$userId) {
            return redirect('/form/login');
        }

        return $next($request);
    }
}
