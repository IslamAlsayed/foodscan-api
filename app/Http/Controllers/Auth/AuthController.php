<?php

namespace App\Http\Controllers\Auth;

use Exception;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Administrator;
use App\Jobs\SendUserEmailJob;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Jobs\NotifySessionExpirationJob;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\Auth\AuthLoginRequest;
use App\Http\Resources\Auth\AuthShowResource;
use App\Http\Resources\Auth\AuthLoginResource;
use App\Http\Requests\Auth\AuthRegisterRequest;

/**
 * Handles authentication and authorization.
 * user registration and login.
 *
 * @author IslamAlsayed eslamalsayed8133@gmail.com
 */
class AuthController extends Controller
{
    /**
     * Registers a new user.
     *
     * @param AuthRegisterRequest $request The request containing user data.
     * @return \Illuminate\Http\JsonResponse The response containing status, data, token, and message.
     */
    public function store(AuthRegisterRequest $request)
    {
        $email = $request->input('email');
        $role = $request->input('role');
        $message = '';

        if (Administrator::where('email', $email)->exists()) {
            $message = "This email is already registered as an administrator!";
        } elseif (Employee::where('email', $email)->exists()) {
            $message = "This email is already registered as an employee!";
        } elseif (Customer::where('email', $email)->exists()) {
            $message = "This email is already registered as a customer!";
        } else {
            $message = false;
        }

        if ($message) {
            return response()->json(['status' => 'failed', 'message' => $message], 422);
        }

        try {
            $user = $this->createUser($request, $role);
            $token = JWTAuth::fromUser($user);
            SendUserEmailJob::dispatch($user, 'register');
            return response()->json(['status' => 'success', 'data' => new AuthShowResource($user), 'token' => $token, 'message' => 'Administrator created successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Registers a new user.
     *
     * @param Request $request The HTTP request object, and the role of the user to be registered.

     * @return [json] The store method returns a JSON response with the status of the operation, the data of the created user, the token for authentication, and a success message.
     */
    private function createUser($request, $role)
    {
        switch ($role) {
            case 'admin':
                $user = Administrator::create($request->all());
                break;
            case 'casher':
                $user = Employee::create($request->all());
                break;
            case 'user':
                $user = Customer::create($request->all());
                break;
            default:
                throw new Exception('Invalid guard');
        }
        return $user;
    }

    /**
     * Logs in a user.
     *
     * @param AuthLoginRequest $request The request containing user credentials.
     * @return \Illuminate\Http\JsonResponse The response containing status, data, token, and message.
     */
    public function login(AuthLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $guards = [
            'admin-api' => Administrator::class,
            'employee-api' => Employee::class,
            'customer-api' => Customer::class,
        ];

        $guard = '';
        $user = null;

        foreach ($guards as $key => $model) {
            $user = $model::where('email', $request->email)->first();
            if ($user) {
                $guard = $key;
                break;
            }
        }

        if (!$user || $user->status != 1) {
            return response()->json(['status' => 'failed', 'message' => 'User not found or inactive'], 401);
        }

        try {
            if (!Auth::guard($guard)->attempt($credentials)) {
                return response()->json(['status' => 'failed', 'message' => 'Invalid credentials'], 401);
            }

            $token = JWTAuth::fromUser($user);
            SendUserEmailJob::dispatch($user, 'logged in');

            return response()->json(['status' => 'success', 'data' => new AuthLoginResource($user), 'token' => $token, 'message' => 'Successfully logged in'], 200);
        } catch (JWTException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Could not create token', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Logs out the currently authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse The response containing status and message.
     */
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
            SendUserEmailJob::dispatch(Auth::guard($guard)->user(), 'logged out');
            Auth::guard($guard)->logout();
            return response()->json(['status' => 'success', 'message' => 'Successfully logged out'], 200);
        } catch (JWTException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Failed to log out', 'error' => $e->getMessage()], 500);
        }
    }
}
