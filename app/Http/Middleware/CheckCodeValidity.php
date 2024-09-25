<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class CheckCodeValidity
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
                return response()->json(['status' => 'failed', 'message' => 'You are not an admin'], 403);
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Token has expired'], 401);
        }

        return $next($request);
    }
}
