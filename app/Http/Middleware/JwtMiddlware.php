<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JwtMiddlware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['status' => 'Token invalido'], 401);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                $token = JWTAuth::getToken();
                $newToken = JWTAuth::refresh($token);
                $user = JWTAuth::setToken($newToken)->toUser();
                //send new token on response header
                return response()->json(['status' => 'Token expirado', 'tokenRefresh' => $newToken], 401);
            } else {
                return response()->json(['status' => 'Error de autenticación'], 401);
            }
        }
        return $next($request);
    }
}
