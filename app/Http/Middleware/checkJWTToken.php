<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;

class checkJWTToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $guard)
    {
        try {
            config(['auth.defaults.guard' => 'student']);

            $user = JWTAuth::parseToken()->authenticate();
            $token = JWTAuth::getToken();
            $payload = JWTAuth::getPayload($token)->toArray();
            
            if ($payload['type'] != $guard) {
                return response()->json('Not authorized', 200);
            }

        } catch (Exception $e) {
            if ($e instanceof TokenInvalidException) {
                return response()->json('Token is Invalid', 200);
            } else if ($e instanceof TokenExpiredException) {
                return response()->json('Token is Expired', 200);
            } else {
                return response()->json('Authorization Token not found', 404);
            }
        }

        return $next($request);
    }
}
