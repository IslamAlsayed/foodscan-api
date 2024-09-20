<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\DiningTable;
use Illuminate\Http\Request;
use App\Traits\SendSmsAndEmail;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\OrderResource;
use App\Http\Controllers\PayMobController;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderController extends Controller
{
    use SendSmsAndEmail;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $orders = Order::with('employee')->with('customer')->with('dining_table')->where('status', '1')->get();

            if ($orders->isEmpty()) {
                return response()->json(['status' => 'failed', 'message' => 'No active orders found'], 404);
            }

            return response()->json(['status' => 'success', 'data' => OrderResource::collection($orders)], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "notes" => "nullable|string",
            // "products" => "required",
            "status" => "nullable|in:1,0",
            "order_status" => "string|in:in_progress,cancelled,done",
            "payment_type" => "string|in:cashed,online,unpaid",
            "payment_status" => "string|in:paid,pending,unpaid",
            "transaction_id" => "nullable",
            "employee_id" => "required|exists:employees,id",
            "customer_id" => "required|exists:customers,id",
            "dining_table_id" => "required|exists:dining_tables,id",
        ]);

        $models = [
            'employee' => Employee::findOrFail($validated['employee_id']),
            'customer' => Customer::findOrFail($validated['customer_id']),
            'dining_table' => DiningTable::findOrFail($validated['dining_table_id']),
        ];

        foreach ($models as $type => $model) {
            if ($model->status === 0) {
                return response()->json(['status' => 'failed', 'message' => "This $type is not active"], 403);
            }
        }

        try {
            $order = Order::create($request->all());
            $order->order_status = 'in_progress';

            $order = Order::FindOrFail($order->id);

            $PaymentKey = PayMobController::pay($order);

            $this->SendSmsAndEmail($order);

            DB::commit();
            return view('payment.paymob_iframe')->with(['token' => $PaymentKey]);
            // return response()->json(['status' => 'success', 'data' => new OrderResource($order), 'message' => 'Order created successfully'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $order = Order::with('employee')->with('customer')->with('dining_table')->findOrFail($id);

            if ($order->status != 1) {
                return response()->json(['status' => 'failed', 'message' => 'This is order not active'], 403);
            }

            return response()->json(['status' => 'success', 'data' => new OrderResource($order)], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Order not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);

            if ($order->status != 1) {
                return response()->json(['status' => 'failed', 'message' => 'This is order not active'], 403);
            }

            $updateData = $request->only([
                'total',
                'notes',
                'order_status',
                'payment_type',
                'payment_status',
                'transaction_id',
                'employee_id',
                'customer_id',
                'dining_table_id'
            ]);

            if (empty($updateData)) {
                return response()->json(['status' => 'failed', 'message' => 'No valid data to update'], 400);
            }

            $order->update($updateData);

            $this->SendSmsAndEmail($order);

            return response()->json(['status' => 'success', 'data' => new OrderResource($order), 'message' => 'Order updated successfully'], 200);
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
            $order = Order::findOrFail($id);

            if ($order->status == 1) {
                return response()->json(['status' => 'failed', 'message' => 'The order is active already'], 403);
            }

            $updateSuccessful = $order->update($request->only(['status']));

            if ($updateSuccessful && $order->wasChanged('status')) {
                return response()->json(['status' => 'success', 'data' => ['status' => $order->status], 'message' => 'Order status updated successfully'], 200);
            }

            return response()->json(['status' => 'failed', 'message' => 'Status has not changed.'], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Order not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateOrderStatus(Request $request,  $id)
    {
        $request->validate(['order_status' => 'required|in:in_progress,cancelled,done']);

        try {
            $order = Order::findOrFail($id);

            if ($order->status != 1) {
                return response()->json(['status' => 'failed', 'message' => 'This is order not active'], 403);
            }

            $updateSuccessful = $order->update($request->only(['order_status']));

            if ($updateSuccessful && $order->wasChanged('order_status')) {
                $this->SendSmsAndEmail($order);
                DB::commit();
                return response()->json(['status' => 'success', 'data' => ['order_status' => $order->order_status], 'message' => 'Order status updated successfully'], 200);
            }

            DB::rollBack();
            return response()->json(['status' => 'failed', 'message' => 'Status has not changed.'], 400);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['status' => 'failed', 'message' => 'Order not found'], 404);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $order = Order::findOrFail($id);

            if ($order->status != 1) {
                return response()->json(['status' => 'failed', 'message' => 'This is order not active'], 403);
            }

            $order->delete();

            return response()->json(['status' => 'success', 'message' => 'Order deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Order not found'], 404);
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
            $orders = Order::with('employee')->with('customer')->with('dining_table')->where('status', '1')->get();
            $floor = $request->floor;
            $size = $request->size;
            $status = $request->status;

            $filtered = $orders->filter(function ($order) use ($floor, $size, $status) {
                return $order['name'] == $floor ||
                    $order['description'] == $size ||
                    $order['status'] == $status;
            });

            return response()->json(['status' => 'success', 'data' => OrderResource::collection($filtered)], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }
}
