<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Categories\CategoryStoreRequest;
use App\Http\Requests\Categories\CategoryUpdateRequest;
use App\Http\Resources\CategoryResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Handles all categories related requests.
 *
 * @author IslamAlsayed eslamalsayed8133@gmail.com
 */
class CategoryController extends Controller
{
    /**
     * Get all categories.
     * 
     * @return CategoryResource Returns a JSON response containing a collection of active categories.
     */
    public function index()
    {
        try {
            $categories = Category::where('status', '1')->get();

            if ($categories->isEmpty()) {
                return response()->json(['status' => 'failed', 'message' => 'No active categories found'], 404);
            }

            return response()->json(['status' => 'success', 'data' => CategoryResource::collection($categories)], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created category in storage.
     *
     * @param CategoryResource $request The request object containing the category data.
     * @return CategoryStoreRequest Returns a JSON response containing the newly created category.
     */
    public function store(CategoryStoreRequest $request)
    {
        try {
            $category = Category::create($request->all());

            if ($request->hasFile('image')) {
                if ($category->image) Storage::disk('public')->delete($category->image);

                $imagePath = $request->file('image')->store('Categories/' . $category->id, 'public');
                $updateData['image'] = $imagePath;
            } else {
                $updateData['image'] = null;
            }

            $category = Category::find($category->id);

            return response()->json(['status' => 'success', 'data' => new CategoryResource($category), 'message' => 'Category created successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * show information about a specific category.
     *
     * @param int $id The ID of the category to retrieve.
     * @return CategoryResource Returns a JSON response containing the category information.
     */
    public function show($id)
    {
        try {
            $category = Category::findOrFail($id);

            if ($category->status != 1) {
                return response()->json(['status' => 'failed', 'message' => 'This is category not active'], 403);
            }

            return response()->json(['status' => 'success', 'data' => new CategoryResource($category)], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Category not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update a specific category.
     *
     * @param CategoryUpdateRequest $request The request object containing the updated category data.
     * @return CategoryResource Returns a JSON response containing the updated category information.
     */
    public function update(CategoryUpdateRequest $request, $id)
    {
        try {
            $category = Category::findOrFail($id);

            if ($category->status != 1) {
                return response()->json(['status' => 'failed', 'message' => 'This is category not active'], 403);
            }

            $updateData = $request->only(['name', 'description', 'image', 'status']);

            if ($request->hasFile('image')) {
                if ($category->image) Storage::disk('public')->delete($category->image);

                $imagePath = $request->file('image')->store('Categories/' . $category->id, 'public');
                $updateData['image'] = $imagePath;
            }

            if (empty($updateData)) {
                return response()->json(['status' => 'failed', 'message' => 'No valid data to update'], 400);
            }

            $category->update($updateData);
            return response()->json(['status' => 'success', 'data' => new CategoryResource($category), 'message' => 'Category updated successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Item not found'], 404);
        } catch (Exception $e) {
            return response()->json(['message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Updates the status of a category.
     *
     * @param Request $request The request object.
     * @return CategoryResource The category resource
     */
    public function updateStatus(Request $request,  $id)
    {
        $request->validate(['status' => 'required|in:0,1']);

        try {
            $category = Category::findOrFail($id);

            if ($category->status == 1) {
                return response()->json(['status' => 'failed', 'message' => 'The category is active already'], 403);
            }

            $updateSuccessful = $category->update($request->only(['status']));

            if ($updateSuccessful && $category->wasChanged('status')) {
                return response()->json(['status' => 'success', 'data' => ['status' => $category->status], 'message' => 'Category status updated successfully'], 200);
            }

            return response()->json(['status' => 'failed', 'message' => 'Status has not changed.'], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Category not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * destroy category by id.
     *
     * @param integer $id
     * @return message.
     */
    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);

            if ($category->status != 1) {
                return response()->json(['status' => 'failed', 'message' => 'This is category not active'], 403);
            }

            $category->delete();

            return response()->json(['status' => 'success', 'message' => 'Category deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Category not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * search category by [name, description, price].
     *
     * @param Request $request.
     * @return CategoryResponse Returns a JSON response containing a collection of active categories.
     */
    public function search(Request $request)
    {
        try {
            $categories = Category::where('status', '1')->get();
            $name = $request->name;
            $description = $request->description;
            $status = $request->status;

            $filtered = $categories->filter(function ($category) use ($name, $description, $status) {
                return $category['name'] == $name ||
                    $category['description'] == $description ||
                    $category['status'] == $status;
            });

            return response()->json(['status' => 'success', 'data' => CategoryResource::collection($filtered)], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }
}
