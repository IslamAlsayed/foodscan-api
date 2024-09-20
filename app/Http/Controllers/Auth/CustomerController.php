<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Exception;
use App\Models\Customer;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\AuthUpdateRequest;
use App\Http\Requests\Auth\AuthRegisterRequest;
use App\Http\Resources\Auth\AuthShowResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $customers = Customer::where('status', '1')->get();

            if ($customers->isEmpty()) {
                return response()->json(['status' => 'failed', 'message' => 'No active customers found'], 404);
            }

            return response()->json(['status' => 'success', 'data' => AuthShowResource::collection($customers)], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AuthRegisterRequest $request)
    {
        try {
            $user = Customer::create($request->all());

            $token = JWTAuth::fromUser($user);
            return response()->json(['status' => 'success', 'data' => new AuthShowResource($user), 'token' => $token, 'message' => 'Customer created successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            if ($customer->status != 1) {
                return response()->json(['status' => 'failed', 'message' => 'Your account is not active'], 403);
            }

            return response()->json(['status' => 'success', 'data' => new AuthShowResource($customer)], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Customer not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AuthUpdateRequest $request,  $id)
    {
        try {
            $customer = Customer::findOrFail($id);

            if ($customer->status != 1) {
                return response()->json(['status' => 'failed', 'message' => 'Your account is not active'], 403);
            }

            $updateData = $request->only(['name', 'email', 'phone', 'role', 'status']);

            if (empty($updateData)) {
                return response()->json(['status' => 'failed', 'message' => 'No valid data to update'], 400);
            }

            if ($request->filled('password')) {
                $customer->password = Hash::make($request->input('password'));
            }

            $customer->update($updateData);
            return response()->json(['status' => 'success', 'data' => new AuthShowResource($customer), 'message' => 'Customer updated successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request,  $id)
    {
        $request->validate(['status' => 'required|in:0,1']);

        try {
            $customer = Customer::findOrFail($id);

            if ($customer->status == 1) {
                return response()->json(['status' => 'failed', 'message' => 'Your account is active already'], 403);
            }

            $updateSuccessful = $customer->update($request->only(['status']));

            if ($updateSuccessful && $customer->wasChanged('status')) {
                return response()->json(['status' => 'success', 'data' => ['status' => $customer->status], 'message' => 'Customer status updated successfully'], 200);
            }

            return response()->json(['status' => 'failed', 'message' => 'Status has not changed.'], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Customer not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $customer = Customer::findOrFail($id);

            if ($customer->status != 1) {
                return response()->json(['status' => 'failed', 'message' => 'Customer account is not active'], 403);
            }

            $customer->delete();

            return response()->json(['status' => 'success', 'message' => 'Customer deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Customer not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }
}
