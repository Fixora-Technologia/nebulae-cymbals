<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TransactionItemController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:TRANSACTION_LIST|TRANSACTION_ADD|TRANSACTION_EDIT|TRANSACTION_DELETE');
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        // Ensure a transaction ID is provided
        if (!$request->has('transaction_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction ID is required'
            ], 400);
        }
        
        $transaction = Transaction::find($request->transaction_id);
        
        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }
        
        $items = TransactionItem::with(['product.unit'])
            ->where('transaction_id', $transaction->id)
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $items,
            'transaction' => $transaction
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): RedirectResponse
    {
        // Items should only be created as part of a transaction
        return redirect()->route('mindo.transactions.create')
            ->with('message', 'Items can only be created as part of a transaction');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, [
            'transaction_id' => ['required', 'exists:transactions,id'],
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['required', 'numeric', 'min:0'],
        ]);
        
        try {
            DB::beginTransaction();
            
            $transaction = Transaction::findOrFail($request->transaction_id);
            $product = Product::findOrFail($request->product_id);
            $subtotal = $request->quantity * $request->unit_price;
            
            // Create the transaction item
            $item = TransactionItem::create([
                'transaction_id' => $request->transaction_id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'unit_price' => $request->unit_price,
                'subtotal' => $subtotal,
            ]);
            
            // Update product stock
            if ($transaction->transaction_type == 'in') {
                $product->stock += $request->quantity;
            } else { // 'out'
                if ($product->stock < $request->quantity) {
                    throw new \Exception("Insufficient stock for {$product->name}");
                }
                $product->stock -= $request->quantity;
            }
            $product->save();
            
            // Update transaction total
            $transaction->total_value += $subtotal;
            $transaction->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Item added successfully',
                'data' => $item->load('product'),
                'transaction' => $transaction
            ]);
                
        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to add item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TransactionItem $transactionItem): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $transactionItem->load(['product', 'transaction'])
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransactionItem $transactionItem): RedirectResponse
    {
        // Individual items cannot be edited separately from the transaction
        // Determine correct route based on transaction type
        $transaction = $transactionItem->transaction;
        $route = $transaction->transaction_type === 'in' ? 'mindo.transactions.in.show' : 'mindo.transactions.out.show';
        
        return redirect()->route($route, $transactionItem->transaction_id)
            ->with('message', 'Items cannot be edited individually after creation');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransactionItem $transactionItem): RedirectResponse
    {
        // Individual items cannot be updated separately from the transaction
        // Determine correct route based on transaction type
        $transaction = $transactionItem->transaction;
        $route = $transaction->transaction_type === 'in' ? 'mindo.transactions.in.show' : 'mindo.transactions.out.show';
        
        return redirect()->route($route, $transactionItem->transaction_id)
            ->with('message', 'Items cannot be updated individually after creation');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransactionItem $transactionItem): JsonResponse
    {
        try {
            DB::beginTransaction();
            
            $transaction = $transactionItem->transaction;
            $product = $transactionItem->product;
            
            // Update product stock
            if ($transaction->transaction_type == 'in') {
                // If this was a stock in, subtract from stock
                if ($product->stock < $transactionItem->quantity) {
                    throw new \Exception("Cannot remove: Insufficient stock for {$product->name}");
                }
                $product->stock -= $transactionItem->quantity;
            } else { // 'out'
                // If this was a stock out, add back to stock
                $product->stock += $transactionItem->quantity;
            }
            $product->save();
            
            // Update transaction total
            $transaction->total_value -= $transactionItem->subtotal;
            $transaction->save();
            
            // Delete the item
            $transactionItem->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Item removed successfully',
                'transaction' => $transaction->fresh()
            ]);
                
        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove item: ' . $e->getMessage()
            ], 500);
        }
    }
}
