<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Meal;
use Illuminate\Http\Request;
use App\Http\Resources\ItemResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Items\ItemStoreRequest;
use App\Http\Requests\Items\ItemUpdateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Handles all meals related requests.
 *
 * @author IslamAlsayed eslamalsayed8133@gmail.com
 */
class MealController extends Controller
{
    /**
     * Get all items with categories.
     * 
     * @return ItemResource Returns a JSON response containing a collection of active meals with their categories.
     */
    public function index()
    {
        try {
            $meals = Meal::with('category')->where('status', '1')->get();

            if ($meals->isEmpty()) {
                return response()->json(['status' => 'failed', 'message' => 'No active meals found'], 404);
            }

            return response()->json(['status' => 'success', 'data' => ItemResource::collection($meals)], 200);
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
            $meal = Meal::create($request->all());

            if ($request->hasFile('image')) {
                if ($meal->image) Storage::disk('public')->delete($meal->image);

                $imagePath = $request->file('image')->store('Meals/' . $meal->id, 'public');
                $updateData['image'] = $imagePath;
            } else {
                $updateData['image'] = null;
            }
            $meal = Meal::find($meal->id);

            return response()->json(['status' => 'success', 'data' => new ItemResource($meal), 'message' => 'Meal created successfully'], 200);
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
            $meal = Meal::with('category')->findOrFail($id);

            if ($meal->status != 1) {
                return response()->json(['status' => 'failed', 'message' => 'This is meal not active'], 403);
            }

            return response()->json(['status' => 'success', 'data' => new ItemResource($meal)], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Meal not found'], 404);
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
            $meal = Meal::findOrFail($id);

            if ($meal->status != 1) {
                return response()->json(['status' => 'failed', 'message' => 'This is meal not active'], 403);
            }

            $updateData = $request->only(['name', 'description', 'price', 'type', 'status', 'image', 'category_id']);

            if ($request->hasFile('image')) {
                if ($meal->image) Storage::disk('public')->delete($meal->image);

                $imagePath = $request->file('image')->store('Meals/' . $meal->id, 'public');
                $updateData['image'] = $imagePath;
            }

            if (empty($updateData)) {
                return response()->json(['status' => 'failed', 'message' => 'No valid data to update'], 400);
            }

            $meal->update($updateData);
            return response()->json(['status' => 'success', 'data' => new ItemResource($meal), 'message' => 'Meal updated successfully'], 200);
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
            $meal = Meal::findOrFail($id);

            if ($meal->status == 1) {
                return response()->json(['status' => 'failed', 'message' => 'The meal is active already'], 403);
            }

            $updateSuccessful = $meal->update($request->only(['status']));

            if ($updateSuccessful && $meal->wasChanged('status')) {
                return response()->json(['status' => 'success', 'data' => ['status' => $meal->status], 'message' => 'Meal status updated successfully'], 200);
            }

            return response()->json(['status' => 'failed', 'message' => 'Status has not changed.'], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Meal not found'], 404);
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
            $meal = Meal::findOrFail($id);

            if ($meal->status != 1) {
                return response()->json(['status' => 'failed', 'message' => 'This is meal not active'], 403);
            }

            $meal->delete();

            return response()->json(['status' => 'success', 'message' => 'Meal deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Meal not found'], 404);
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
            $meals = Meal::with('category')->where('status', '1')->get();
            $name = $request->name;
            $description = $request->description;
            $price = $request->price;
            $type = $request->type;
            $status = $request->status;
            $category_id = $request->category_id;

            $filtered = $meals->filter(function ($meal) use ($name, $description, $price, $type, $status, $category_id) {
                return $meal['name'] == $name ||
                    $meal['description'] == $description ||
                    $meal['price'] == $price ||
                    $meal['type'] == $type ||
                    $meal['status'] == $status ||
                    $meal['category_id'] == $category_id;
            });

            return response()->json(['status' => 'success', 'data' => ItemResource::collection($filtered)], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }
}
