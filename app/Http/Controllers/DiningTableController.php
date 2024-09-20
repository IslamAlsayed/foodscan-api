<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\DiningTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Dining_TableResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\DiningTables\DiningTableStoreRequest;
use App\Http\Requests\DiningTables\DiningTableUpdateRequest;

class DiningTableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $diningTables = DiningTable::where('status', '1')->get();

            if ($diningTables->isEmpty()) {
                return response()->json(['status' => 'failed', 'message' => 'No active diningTables found'], 404);
            }

            return response()->json(['status' => 'success', 'data' => Dining_TableResource::collection($diningTables)], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DiningTableStoreRequest $request)
    {
        try {
            $data = DiningTable::create($request->all());

            return response()->json(['status' => 'success', 'data' => new Dining_TableResource($data), 'message' => 'DiningTable created successfully'], 200);
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
            $diningTable = DiningTable::findOrFail($id);

            if ($diningTable->status != 1) {
                return response()->json(['status' => 'failed', 'message' => 'This is diningTable not active'], 403);
            }

            return response()->json(['status' => 'success', 'data' => new Dining_TableResource($diningTable)], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'DiningTable not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DiningTableUpdateRequest $request, $id)
    {
        try {
            $diningTable = DiningTable::findOrFail($id);

            if ($diningTable->status != 1) {
                return response()->json(['status' => 'failed', 'message' => 'This is diningTable not active'], 403);
            }

            $updateData = $request->only(['floor', 'size', 'status']);

            if (empty($updateData)) {
                return response()->json(['status' => 'failed', 'message' => 'No valid data to update'], 400);
            }

            $diningTable->update($updateData);
            return response()->json(['status' => 'success', 'data' => new Dining_TableResource($diningTable), 'message' => 'DiningTable updated successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Item not found'], 404);
        } catch (Exception $e) {
            return response()->json(['message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request,  $id)
    {
        $request->validate(['status' => 'required|in:0,1']);

        try {
            $diningTable = DiningTable::findOrFail($id);

            if ($diningTable->status == 1) {
                return response()->json(['status' => 'failed', 'message' => 'The diningTable is active already'], 403);
            }

            $updateSuccessful = $diningTable->update($request->only(['status']));

            if ($updateSuccessful && $diningTable->wasChanged('status')) {
                return response()->json(['status' => 'success', 'data' => ['status' => $diningTable->status], 'message' => 'DiningTable status updated successfully'], 200);
            }

            return response()->json(['status' => 'failed', 'message' => 'Status has not changed.'], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'DiningTable not found'], 404);
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
            $diningTable = DiningTable::findOrFail($id);

            if ($diningTable->status != 1) {
                return response()->json(['status' => 'failed', 'message' => 'This is diningTable not active'], 403);
            }

            $diningTable->delete();

            return response()->json(['status' => 'success', 'message' => 'DiningTable deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'DiningTable not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display a listing of the resource after filtration.
     */
    public function search(Request $request)
    {
        try {
            $categories = DiningTable::where('status', '1')->get();
            $floor = $request->floor;
            $size = $request->size;
            $status = $request->status;

            $filtered = $categories->filter(function ($category) use ($floor, $size, $status) {
                return $category['name'] == $floor ||
                    $category['description'] == $size ||
                    $category['status'] == $status;
            });

            return response()->json(['status' => 'success', 'data' => Dining_TableResource::collection($filtered)], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }
}
