<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Categories\CategoryStoreRequest;
use App\Http\Requests\Categories\CategoryUpdateRequest;
use App\Http\Resources\CategoryResource;
use App\Traits\SendSmsAndEmail;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryController extends Controller
{
    use SendSmsAndEmail;

    /**
     * Display a listing of the resource.
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
     * Store a newly created resource in storage.
     */
    public function store(CategoryStoreRequest $request)
    {
        try {
            $data = Category::create($request->all());
            $imagePath = $request->file('image')->store('Categories/' . $data->id, 'public');
            $data->update(['image' => $imagePath]);

            return response()->json(['status' => 'success', 'data' => new CategoryResource($data), 'message' => 'Category created successfully'], 200);
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
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
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
     * Display a listing of the resource after filtration.
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
