<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\Auth\AuthLoginRequest;
use App\Http\Resources\Auth\AuthLoginResource;
use App\Models\Administrator;
use App\Models\Customer;
use App\Models\Employee;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(AuthLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $guard = '';

        if (Administrator::where('email', $request->email)->exists()) {
            $guard = "admin-api";
        } elseif (Employee::where('email', $request->email)->exists()) {
            $guard = "employee-api";
        } elseif (Customer::where('email', $request->email)->exists()) {
            $guard = "customer-api";
        } else {
            return response()->json(['status' => 'failed', 'message' => 'User not found'], 404);
        }

        if (!$guard) {
            return response()->json(['status' => 'failed', 'message' => 'Invalid guard'], 401);
        }

        try {
            if (!Auth::guard($guard)->attempt($credentials)) {
                return response()->json(['status' => 'failed', 'message' => 'Invalid credentials'], 401);
            } else {
                $user = Auth::guard($guard)->user();
                $token = JWTAuth::fromUser($user);
                return response()->json(['status' => 'success', 'data' => new AuthLoginResource($user), 'token' => $token, 'message' => 'Successfully logged in'], 200);
            }
        } catch (JWTException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Could not create token', 'error' => $e->getMessage()], 500);
        }
    }

    public function logout()
    {
        $guard = '';
        if (Auth::guard('admin-api')->check()) {
            $guard = 'admin-api';
        } elseif (Auth::guard('employee-api')->check()) {
            $guard = 'employee-api';
        } elseif (Auth::guard('customer-api')->check()) {
            $guard = 'customer-api';
        }

        if (!$guard) {
            return response()->json(['status' => 'failed', 'message' => 'You are not logged in'], 400);
        }

        try {
            Auth::guard($guard)->logout();

            return response()->json(['status' => 'success', 'message' => 'Successfully logged out'], 200);
        } catch (JWTException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Failed to log out', 'error' => $e->getMessage()], 500);
        }
    }
}
