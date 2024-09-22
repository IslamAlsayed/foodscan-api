<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Exception;
use App\Models\Administrator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\AuthUpdateRequest;
use App\Http\Resources\Auth\AuthShowResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Handles administrator authentication and authorization.
 *
 * @author IslamAlsayed eslamalsayed8133@gmail.com
 */
class AdministratorController extends Controller
{
    /**
     * Get the administrator data.
     *
     * @param Request $request Request object from the admin controller or any other controller.
     * @return AuthShowResource object containing the administrator data.
     */
    public function index()
    {
        try {
            $administrators = Administrator::where('status', '1')->get();

            if ($administrators->isEmpty()) {
                return response()->json(['status' => 'failed', 'message' => 'No active administrators found'], 404);
            }

            return response()->json(['status' => 'success', 'data' => AuthShowResource::collection($administrators)], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get the administrator data.
     * 
     * @param Request $request Request object from the admin controller or any other controller.
     * @return AuthShowResource object containing the administrator data.
     */
    public function show($id)
    {
        try {
            $administrator = Administrator::findOrFail($id);

            if ($administrator->status != 1) {
                return response()->json(['status' => 'failed', 'message' => 'Your account is not active'], 403);
            }

            return response()->json(['status' => 'success', 'data' => new AuthShowResource($administrator)], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Administrator not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the administrator data.
     * 
     * @param AuthShowResource $administrator Administrator object.
     * @return AuthShowResource object containing the updated administrator data.
     */
    public function update(AuthUpdateRequest $request,  $id)
    {
        try {
            $administrator = Administrator::findOrFail($id);

            if ($administrator->status != 1) {
                return response()->json(['status' => 'failed', 'message' => 'Your account is not active'], 403);
            }

            $updateData = $request->only(['name', 'email', 'phone', 'role', 'status']);

            if (empty($updateData)) {
                return response()->json(['status' => 'failed', 'message' => 'No valid data to update'], 400);
            }

            if ($request->filled('password')) {
                $administrator->password = Hash::make($request->input('password'));
            }

            $administrator->update($updateData);
            return response()->json(['status' => 'success', 'data' => new AuthShowResource($administrator), 'message' => 'Administrator updated successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Updates an existing administrator's status.
     * 
     * @param Request $request Request object containing the updated status.
     * @return Response JSON response indicating the success or failure of the operation.
     */
    public function updateStatus(Request $request,  $id)
    {
        $request->validate(['status' => 'required|in:0,1']);

        try {
            $administrator = Administrator::findOrFail($id);

            if ($administrator->status == 1) {
                return response()->json(['status' => 'failed', 'message' => 'Your account is active already'], 403);
            }

            $updateSuccessful = $administrator->update($request->only(['status']));

            if ($updateSuccessful && $administrator->wasChanged('status')) {
                return response()->json(['status' => 'success', 'data' => ['status' => $administrator->status], 'message' => 'Administrator status updated successfully'], 200);
            }

            return response()->json(['status' => 'failed', 'message' => 'Status has not changed.'], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Administrator not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * destroy administrator if status account is activated.
     * 
     * @param int $id The ID of the administrator to be updated.
     * @return Response JSON response indicating the success or failure of the operation.
     */
    public function destroy($id)
    {
        try {
            $administrator = Administrator::findOrFail($id);

            if ($administrator->status != 1) {
                return response()->json(['status' => 'failed', 'message' => 'Administrator account is not active'], 403);
            }

            $administrator->delete();

            return response()->json(['status' => 'success', 'message' => 'Administrator deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Administrator not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }
}
