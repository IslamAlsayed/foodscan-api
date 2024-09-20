<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class CheckAdminToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = Auth::guard('admin-api')->user();
            if (!$user || $user->role !== 'admin') {
                throw new TokenInvalidException('You are not a admin');
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Token has expired'], 401);
        }


        return $next($request);
    }
}
