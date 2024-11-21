<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redis;

class OtherCheckTokens
{
    public function handle(Request $request, Closure $next): Response
    {
        $accessToken = $request->cookie('access_token');

	$userId = Redis::get('access_token:' . $accessToken);

        if (!$userId) {
            return redirect('/form/login');
        }

        return $next($request);
    }
}
