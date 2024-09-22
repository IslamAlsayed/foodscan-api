<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Addon;
use Illuminate\Http\Request;
use App\Http\Resources\ItemResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Items\ItemStoreRequest;
use App\Http\Requests\Items\ItemUpdateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Handles all addons related requests.
 *
 * @author IslamAlsayed eslamalsayed8133@gmail.com
 */
class AddonController extends Controller
{
    /**
     * Get all items with categories.
     * 
     * @return ItemResource Returns a JSON response containing a collection of active meals with their categories.
     */
    public function index()
    {
        try {
            $addons = Addon::with('category')->where('status', '1')->get();

            if ($addons->isEmpty()) {
                return response()->json(['status' => 'failed', 'message' => 'No active addons found'], 404);
            }

            return response()->json(['status' => 'success', 'data' => ItemResource::collection($addons)], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created meal in storage.
     *
     * @param ItemStoreRequest  $request The request object containing the meal data.
     * @return ItemResource Returns a JSON response indicating the success or failure of the operation. If successful, it includes the created meal data. If failed, it includes an error message.
     */
    public function store(ItemStoreRequest $request)
    {
        try {
            $addon = Addon::create($request->all());

            if ($request->hasFile('image')) {
                if ($addon->image) Storage::disk('public')->delete($addon->image);

                $imagePath = $request->file('image')->store('Addons/' . $addon->id, 'public');
                $updateData['image'] = $imagePath;
            } else {
                $updateData['image'] = null;
            }

            $addon = Addon::find($addon->id);

            return response()->json(['status' => 'success', 'data' => new ItemResource($addon), 'message' => 'Addon created successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * show information about a specific meal.
     *
     * @param int $id The ID of the meal to retrieve.
     * @return ItemResource Returns a JSON response containing the meal data.
     */
    public function show($id)
    {
        try {
            $addon = Addon::with('category')->findOrFail($id);

            if ($addon->status != 1) {
                return response()->json(['status' => 'failed', 'message' => 'This is addon not active'], 403);
            }

            return response()->json(['status' => 'success', 'data' => new ItemResource($addon)], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Addon not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update a specific meal.
     *
     * @param ItemResource $meal The meal to update.
     * @return JsonResponse Returns a JSON response indicating the success or failure of the update operation.
     */
    public function update(ItemUpdateRequest $request, $id)
    {
        try {
            $addon = Addon::findOrFail($id);

            if ($addon->status != 1) {
                return response()->json(['status' => 'failed', 'message' => 'This is addon not active'], 403);
            }

            $updateData = $request->only(['name', 'description', 'price', 'type', 'status', 'image', 'category_id']);

            if ($request->hasFile('image')) {
                if ($addon->image) Storage::disk('public')->delete($addon->image);

                $imagePath = $request->file('image')->store('Addons/' . $addon->id, 'public');
                $updateData['image'] = $imagePath;
            }

            if (empty($updateData)) {
                return response()->json(['status' => 'failed', 'message' => 'No valid data to update'], 400);
            }

            $addon->update($updateData);
            return response()->json(['status' => 'success', 'data' => new ItemResource($addon), 'message' => 'Addon updated successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Item not found'], 404);
        } catch (Exception $e) {
            return response()->json(['message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Updates the status of a meal.
     *
     * @param ItemResource $meal The meal to update.
     * @return ItemResource The updated meal.
     */
    public function updateStatus(Request $request,  $id)
    {
        $request->validate(['status' => 'required|in:0,1']);

        try {
            $addon = Addon::findOrFail($id);

            if ($addon->status == 1) {
                return response()->json(['status' => 'failed', 'message' => 'The addon is active already'], 403);
            }

            $updateSuccessful = $addon->update($request->only(['status']));

            if ($updateSuccessful && $addon->wasChanged('status')) {
                return response()->json(['status' => 'success', 'data' => ['status' => $addon->status], 'message' => 'Addon status updated successfully'], 200);
            }

            return response()->json(['status' => 'failed', 'message' => 'Status has not changed.'], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Addon not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * destroy meal by id.
     *
     * @param integer $id
     * @return message.
     */
    public function destroy($id)
    {
        try {
            $addon = Addon::findOrFail($id);

            if ($addon->status != 1) {
                return response()->json(['status' => 'failed', 'message' => 'This is addon not active'], 403);
            }

            $addon->delete();

            return response()->json(['status' => 'success', 'message' => 'Addon deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Addon not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * search meal by [name, description, price, type, status, category_id].
     *
     * @param Request $request.
     * @return ItemResource Returns a JSON response containing a collection of active meals with their categories.
     */
    public function search(Request $request)
    {
        try {
            $addons = Addon::with('category')->where('status', '1')->get();
            $name = $request->name;
            $description = $request->description;
            $price = $request->price;
            $type = $request->type;
            $status = $request->status;
            $category_id = $request->category_id;

            $filtered = $addons->filter(function ($addon) use ($name, $description, $price, $type, $status, $category_id) {
                return $addon['name'] == $name ||
                    $addon['description'] == $description ||
                    $addon['price'] == $price ||
                    $addon['type'] == $type ||
                    $addon['status'] == $status ||
                    $addon['category_id'] == $category_id;
            });

            return response()->json(['status' => 'success', 'data' => ItemResource::collection($filtered)], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }
}
