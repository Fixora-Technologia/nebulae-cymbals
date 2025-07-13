<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:TRANSACTION_LIST|TRANSACTION_ADD|TRANSACTION_EDIT|TRANSACTION_DELETE', ['only' => ['index', 'show']]);
        $this->middleware('permission:TRANSACTION_ADD', ['only' => ['create', 'store']]);
        $this->middleware('permission:TRANSACTION_EDIT', ['only' => ['edit', 'update']]);
        $this->middleware('permission:TRANSACTION_DELETE', ['only' => ['destroy']]);
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $perPage = 20;
        $query = Transaction::with(['customer', 'user', 'items.product'])->latest();
        
        // Filter by transaction type if provided
        if ($request->has('type') && in_array($request->type, ['in', 'out'])) {
            $query->where('transaction_type', $request->type);
        }
        
        // Filter by customer if provided
        if ($request->has('customer_id') && $request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }
        
        // Filter by date range if provided
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $data = $query->paginate($perPage);
        $customers = Customer::orderBy('name', 'asc')->pluck('name', 'id');
        
        return view('admin.pages.transactions.index', compact('data', 'customers'))
            ->with('i', ($request->input('page', 1) - 1) * $perPage);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $customers = Customer::orderBy('name', 'asc')->get();
        $products = Product::with(['category', 'unit'])
            ->where('stock', '>', 0)
            ->orderBy('name', 'asc')
            ->get();
        
        return view('admin.pages.transactions.form', compact('customers', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'transaction_date' => ['required', 'date'],
            'transaction_type' => ['required', 'in:in,out'],
            'customer_id' => ['required_if:transaction_type,out', 'nullable', 'exists:customers,id'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);
        
        try {
            DB::beginTransaction();
            
            // Generate transaction code
            $prefix = $request->transaction_type == 'in' ? 'TRX-IN-' : 'TRX-OUT-';
            $transactionCode = $prefix . date('Ymd') . '-' . Str::random(5);
            
            // Calculate total value
            $totalValue = 0;
            foreach ($request->items as $item) {
                $totalValue += $item['quantity'] * $item['unit_price'];
            }
            
            // Create transaction record
            $transaction = Transaction::create([
                'transaction_type' => $request->transaction_type,
                'transaction_code' => $transactionCode,
                'transaction_date' => $request->transaction_date,
                'customer_id' => $request->transaction_type == 'out' ? $request->customer_id : null,
                'user_id' => auth()->id(),
                'total_value' => $totalValue,
                'notes' => $request->notes,
            ]);
            
            // Create transaction items and update stock
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subtotal = $item['quantity'] * $item['unit_price'];
                
                // Create transaction item
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $subtotal,
                ]);
                
                // Update product stock
                if ($request->transaction_type == 'in') {
                    $product->stock += $item['quantity'];
                } else { // 'out'
                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Insufficient stock for {$product->name}");
                    }
                    $product->stock -= $item['quantity'];
                }
                
                $product->save();
            }
            
            DB::commit();
            
            return redirect()->route('mindo.transactions.index')
                ->with('message', 'Transaction created successfully!');
                
        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->withInput()
                ->with([
                    'message' => 'Transaction failed: ' . $e->getMessage(),
                    'alert-type' => 'error'
                ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction): View
    {
        $transaction->load(['customer', 'user', 'items.product.unit']);
        $products = Product::with('unit')->where('stock', '>', 0)->get();
        return view('admin.pages.transactions.show', compact('transaction', 'products'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction): RedirectResponse
    {
        // Transactions should not be edited after creation for integrity reasons
        // Redirect to show view instead
        return redirect()->route('mindo.transactions.show', $transaction->id)
            ->with([
                'message' => 'Transactions cannot be edited after creation for data integrity reasons.',
                'alert-type' => 'info'
            ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction): RedirectResponse
    {
        // Transactions should not be updated after creation for integrity reasons
        return redirect()->route('mindo.transactions.show', $transaction->id)
            ->with([
                'message' => 'Transactions cannot be updated after creation for data integrity reasons.',
                'alert-type' => 'info'
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction): RedirectResponse
    {
        try {
            DB::beginTransaction();
            
            // First, reverse the stock changes
            foreach ($transaction->items as $item) {
                $product = $item->product;
                
                if ($transaction->transaction_type == 'in') {
                    // If this was a stock in, subtract from stock
                    if ($product->stock < $item->quantity) {
                        throw new \Exception("Cannot delete: Insufficient stock for {$product->name}");
                    }
                    $product->stock -= $item->quantity;
                } else { // 'out'
                    // If this was a stock out, add back to stock
                    $product->stock += $item->quantity;
                }
                
                $product->save();
            }
            
            // Delete transaction items and the transaction
            $transaction->items()->delete();
            $transaction->delete();
            
            DB::commit();
            
            return redirect()->route('mindo.transactions.index')
                ->with('message', 'Transaction deleted successfully!');
                
        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->with([
                    'message' => 'Delete failed: ' . $e->getMessage(),
                    'alert-type' => 'error'
                ]);
        }
    }
}
