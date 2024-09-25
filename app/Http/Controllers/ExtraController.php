<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Extra;
use Illuminate\Http\Request;
use App\Http\Resources\ItemResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Items\ItemStoreRequest;
use App\Http\Requests\Items\ItemUpdateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Handles all extras related requests.
 *
 * @author IslamAlsayed eslamalsayed8133@gmail.com
 */
class ExtraController extends Controller
{
    /**
     * Get all items with categories.
     * 
     * @return ItemResource Returns a JSON response containing a collection of active extras with their categories.
     */
    public function index()
    {
        try {
            $extras = Extra::with('category')->where('status', '1')->get();

            if ($extras->isEmpty()) {
                return response()->json(['status' => 'failed', 'message' => 'No active extras found'], 404);
            }

            return response()->json(['status' => 'success', 'data' => ItemResource::collection($extras)], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created extra in storage.
     *
     * @param ItemStoreRequest  $request The request object containing the extra data.
     * @return ItemResource Returns a JSON response indicating the success or failure of the operation. If successful, it includes the created extra data. If failed, it includes an error message.
     */
    public function store(ItemStoreRequest $request)
    {
        try {
            $extra = Extra::create($request->all());

            if ($request->hasFile('image')) {
                if ($extra->image) Storage::disk('public')->delete($extra->image);

                $imagePath = $request->file('image')->store('Extras/' . $extra->id, 'public');
                $updateData['image'] = $imagePath;
            } else {
                $updateData['image'] = null;
            }

            $extra = Extra::find($extra->id);

            return response()->json(['status' => 'success', 'data' => new ItemResource($extra), 'message' => 'Extra created successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * show information about a specific extra.
     *
     * @param int $id The ID of the extra to retrieve.
     * @return ItemResource Returns a JSON response containing the extra data.
     */
    public function show($id)
    {
        try {
            $extra = Extra::with('category')->findOrFail($id);

            if ($extra->status != 1) {
                return response()->json(['status' => 'failed', 'message' => 'This is extra not active'], 403);
            }

            return response()->json(['status' => 'success', 'data' => new ItemResource($extra)], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Extra not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update a specific extra.
     *
     * @param ItemResource $extra The extra to update.
     * @return JsonResponse Returns a JSON response indicating the success or failure of the update operation.
     */
    public function update(ItemUpdateRequest $request, $id)
    {
        try {
            $extra = Extra::findOrFail($id);

            if ($extra->status != 1) {
                return response()->json(['status' => 'failed', 'message' => 'This is extra not active'], 403);
            }

            $updateData = $request->only(['name', 'description', 'price', 'type', 'status', 'image', 'category_id']);

            if ($request->hasFile('image')) {
                if ($extra->image) Storage::disk('public')->delete($extra->image);

                $imagePath = $request->file('image')->store('Extras/' . $extra->id, 'public');
                $updateData['image'] = $imagePath;
            }

            if (empty($updateData)) {
                return response()->json(['status' => 'failed', 'message' => 'No valid data to update'], 400);
            }

            $extra->update($updateData);
            return response()->json(['status' => 'success', 'data' => new ItemResource($extra), 'message' => 'Extra updated successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Item not found'], 404);
        } catch (Exception $e) {
            return response()->json(['message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Updates the status of a extra.
     *
     * @param ItemResource $extra The extra to update.
     * @return ItemResource The updated extra.
     */
    public function updateStatus(Request $request,  $id)
    {
        $request->validate(['status' => 'required|in:0,1']);

        try {
            $extra = Extra::findOrFail($id);

            if ($extra->status == 1) {
                return response()->json(['status' => 'failed', 'message' => 'The extra is active already'], 403);
            }

            $updateSuccessful = $extra->update($request->only(['status']));

            if ($updateSuccessful && $extra->wasChanged('status')) {
                return response()->json(['status' => 'success', 'data' => ['status' => $extra->status], 'message' => 'Extra status updated successfully'], 200);
            }

            return response()->json(['status' => 'failed', 'message' => 'Status has not changed.'], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Extra not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * destroy extra by id.
     *
     * @param integer $id
     * @return message.
     */
    public function destroy($id)
    {
        try {
            $extra = Extra::findOrFail($id);

            if ($extra->status != 1) {
                return response()->json(['status' => 'failed', 'message' => 'This is extra not active'], 403);
            }

            $extra->delete();

            return response()->json(['status' => 'success', 'message' => 'Extra deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Extra not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * search extra by [name, description, price, type, status, category_id].
     *
     * @param Request $request.
     * @return ItemResource Returns a JSON response containing a collection of active extras with their categories.
     */
    public function search(Request $request)
    {
        try {
            $extras = Extra::with('category')->where('status', '1')->get();

            $filtered = $extras->filter(function ($extra) use ($request) {
                return $extra['name'] == $request->name ||
                    $extra['description'] == $request->description ||
                    $extra['price'] == $request->price ||
                    $extra['type'] == $request->type ||
                    $extra['status'] == $request->status ||
                    $extra['category_id'] == $request->category_id;
            });

            if ($filtered->isEmpty()) {
                return response()->json(['status' => 'failed', 'message' => 'No extras found'], 404);
            }

            return response()->json(['status' => 'success', 'data' => ItemResource::collection($filtered)], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }
}