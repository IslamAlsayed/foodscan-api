<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Extra;
use Illuminate\Http\Request;
use App\Http\Resources\ItemResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Items\ItemStoreRequest;
use App\Http\Requests\Items\ItemUpdateRequest;
use App\Traits\SendSmsAndEmail;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ExtraController extends Controller
{
    use SendSmsAndEmail;

    /**
     * Display a listing of the resource.
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
     * Store a newly created resource in storage.
     */
    public function store(ItemStoreRequest $request)
    {
        try {
            $data = Extra::create($request->all());
            $imagePath = $request->file('image')->store('Extras/' . $data->id, 'public');
            $data->update(['image' => $imagePath]);

            return response()->json(['status' => 'success', 'data' => new ItemResource($data), 'message' => 'Extra created successfully'], 200);
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
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
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
     * Display a listing of the resource after filtration.
     */
    public function search(Request $request)
    {
        try {
            $extras = Extra::with('category')->when('status', '1')->get();
            $name = $request->name;
            $description = $request->description;
            $price = $request->price;
            $type = $request->type;
            $status = $request->status;
            $category_id = $request->category_id;

            $filtered = $extras->filter(function ($extra) use ($name, $description, $price, $type, $status, $category_id) {
                return $extra['name'] == $name ||
                    $extra['description'] == $description ||
                    $extra['price'] == $price ||
                    $extra['type'] == $type ||
                    $extra['status'] == $status ||
                    $extra['category_id'] == $category_id;
            });

            return response()->json(['status' => 'success', 'data' => ItemResource::collection($filtered)], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }
}
