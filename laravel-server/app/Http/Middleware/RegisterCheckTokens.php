<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Redis;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RegisterCheckTokens
{
    public function handle(Request $request, Closure $next): Response
    {
        $accessToken = $request->cookie('access_token');
        $refreshToken = $request->cookie('refresh_token');

        if (Redis::get('access_token:' . $accessToken) && Redis::get('refresh_token:' . $refreshToken)) {
            return redirect('/form/login');
        }

        return $next($request);
    }
}
