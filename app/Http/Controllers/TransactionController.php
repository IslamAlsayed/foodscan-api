<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\PayMobController;
use App\Http\Resources\TransactionResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $transactions = Transaction::where('status', '1')->get();

            if ($transactions->isEmpty()) {
                return response()->json(['status' => 'failed', 'message' => 'No active transactions found'], 404);
            }

            return response()->json(['status' => 'success', 'data' => TransactionResource::collection($transactions)], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $data = Transaction::create([
            'amount' => 1050,
            'payment_type' => 'online',
            'payment_status' => 'pending',
            'order_id' => '1',
            'customer_id' => '1',
        ]);
        $PaymentKey = PayMobController::pay($data->amount, $data->order_id);

        return ['PaymentKey' => $PaymentKey];
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $transaction = Transaction::findOrFail($id);

            if ($transaction->status != 1) {
                return response()->json(['status' => 'failed', 'message' => 'This is transaction not active'], 403);
            }

            return response()->json(['status' => 'success', 'data' => new TransactionResource($transaction)], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Transaction not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request,  $id)
    {
        $request->validate(['status' => 'required|in:0,1']);

        try {
            $transaction = Transaction::findOrFail($id);

            if ($transaction->status == 1) {
                return response()->json(['status' => 'failed', 'message' => 'The transaction is active already'], 403);
            }

            $updateSuccessful = $transaction->update($request->only(['status']));

            if ($updateSuccessful && $transaction->wasChanged('status')) {
                return response()->json(['status' => 'success', 'data' => ['status' => $transaction->status], 'message' => 'Transaction status updated successfully'], 200);
            }

            return response()->json(['status' => 'failed', 'message' => 'Status has not changed.'], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Transaction not found'], 404);
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
            $transaction = Transaction::findOrFail($id);

            if ($transaction->status != 1) {
                return response()->json(['status' => 'failed', 'message' => 'This is transaction not active'], 403);
            }

            $transaction->delete();

            return response()->json(['status' => 'success', 'message' => 'Transaction deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Transaction not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }
}