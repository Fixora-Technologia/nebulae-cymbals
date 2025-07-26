<?php

namespace App\Http\Controllers;

use App\Exports\TransactionsExport;
use App\Exports\TransactionItemsExport;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

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
        // Determine transaction type from route
        $transactionType = null;
        $routeName = $request->route()->getName();
        if (strpos($routeName, 'mindo.transactions.in.') === 0) {
            $transactionType = 'in';
        } elseif (strpos($routeName, 'mindo.transactions.out.') === 0) {
            $transactionType = 'out';
        }
        $perPage = 20;
        $query = Transaction::with(['customer', 'user', 'items.product'])->latest();

        // Always filter by transaction type from route if available
        if ($transactionType) {
            $query->where('transaction_type', $transactionType);
        }
        // Otherwise, use request parameter if provided
        elseif ($request->has('transaction_type') && in_array($request->transaction_type, ['in', 'out'])) {
            $query->where('transaction_type', $request->transaction_type);
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
    public function create(Request $request): View
    {
        // Determine transaction type from route
        $transactionType = null;
        $routeName = $request->route()->getName();
        if (strpos($routeName, 'mindo.transactions.in.') === 0) {
            $transactionType = 'in';
        } elseif (strpos($routeName, 'mindo.transactions.out.') === 0) {
            $transactionType = 'out';
        }
        $customers = Customer::orderBy('name', 'asc')->get();

        // Product query differs based on transaction type
        if ($transactionType == 'in') {
            // For stock in, all products can be added
            $products = Product::with(['category', 'unit'])
                ->orderBy('name', 'asc')
                ->get();
        } else { // stock out
            // For stock out, only products with stock > 0 can be added
            $products = Product::with(['category', 'unit'])
                ->where('stock', '>', 0)
                ->orderBy('name', 'asc')
                ->get();
        }

        // Generate a default transaction code
        $defaultTransactionCode = Transaction::generateTransactionCode($transactionType);

        return view('admin.pages.transactions.form', compact('customers', 'products', 'defaultTransactionCode', 'transactionType'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Determine transaction type from route
        $transactionType = null;
        $routeName = $request->route()->getName();
        $redirectRoute = 'mindo.transactions.in.index'; // Default to in

        if (strpos($routeName, 'mindo.transactions.in.') === 0) {
            $transactionType = 'in';
            $redirectRoute = 'mindo.transactions.in.index';
        } elseif (strpos($routeName, 'mindo.transactions.out.') === 0) {
            $transactionType = 'out';
            $redirectRoute = 'mindo.transactions.out.index';
        }
        $this->validate($request, [
            'transaction_date' => ['required', 'date'],
            'transaction_type' => ['required', 'in:in,out'],
            'transaction_code' => ['required', 'string', 'max:50', 'unique:transactions,transaction_code'],
            'customer_id' => ['required_if:transaction_type,out', 'nullable', 'exists:customers,id'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        try {
            DB::beginTransaction();

            // Calculate total value
            $totalValue = 0;
            foreach ($request->items as $item) {
                $totalValue += $item['quantity'] * $item['unit_price'];
            }

            // Create transaction record
            $transaction = Transaction::create([
                'transaction_type' => $request->transaction_type,
                'transaction_code' => $request->transaction_code,
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

            return redirect()->route($redirectRoute)
                ->with('message', 'Transaksi dibuat dengan sukses!');
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
    public function show(Request $request, Transaction $transaction): View
    {
        // Determine transaction type from route
        $transactionType = null;
        $routeName = $request->route()->getName();

        if (strpos($routeName, 'mindo.transactions.in.') === 0) {
            $transactionType = 'in';
        } elseif (strpos($routeName, 'mindo.transactions.out.') === 0) {
            $transactionType = 'out';
        }

        // Check if transaction type matches the route
        if ($transactionType && $transactionType !== $transaction->transaction_type) {
            abort(404, 'Transaction not found in this section');
        }
        $transaction->load(['customer', 'user', 'items.product.unit']);
        $products = Product::with('unit')->where('stock', '>', 0)->get();
        return view('admin.pages.transactions.show', compact('transaction', 'products'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Transaction $transaction): RedirectResponse
    {
        // Determine transaction type from route
        $transactionType = $transaction->transaction_type; // Default to record's type
        $routeName = $request->route()->getName();

        if (strpos($routeName, 'mindo.transactions.in.') === 0) {
            $transactionType = 'in';
        } elseif (strpos($routeName, 'mindo.transactions.out.') === 0) {
            $transactionType = 'out';
        }

        // Check if transaction type matches the route
        if ($transactionType !== $transaction->transaction_type) {
            abort(404, 'Transaction not found in this section');
        }

        // Transactions should not be edited after creation for integrity reasons
        // Redirect to show view instead with the correct route based on type
        $showRoute = 'mindo.transactions.in.show'; // Default to in

        if ($transactionType === 'in') {
            $showRoute = 'mindo.transactions.in.show';
        } elseif ($transactionType === 'out') {
            $showRoute = 'mindo.transactions.out.show';
        }

        return redirect()->route($showRoute, $transaction->id)
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
        // Determine transaction type from route for proper redirect
        $transactionType = $transaction->transaction_type;
        $routeName = $request->route()->getName();
        $showRoute = 'mindo.transactions.in.show'; // Default to in

        if (strpos($routeName, 'mindo.transactions.in.') === 0) {
            $showRoute = 'mindo.transactions.in.show';
        } elseif (strpos($routeName, 'mindo.transactions.out.') === 0) {
            $showRoute = 'mindo.transactions.out.show';
        }

        // Transactions should not be updated after creation for integrity reasons
        return redirect()->route($showRoute, $transaction->id)
            ->with([
                'message' => 'Transactions cannot be updated after creation for data integrity reasons.',
                'alert-type' => 'info'
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Transaction $transaction): RedirectResponse
    {
        // Determine transaction type from route for proper redirect
        $transactionType = $transaction->transaction_type;
        $routeName = $request->route()->getName();
        $indexRoute = 'mindo.transactions.in.index'; // Default to in

        if (strpos($routeName, 'mindo.transactions.in.') === 0) {
            $indexRoute = 'mindo.transactions.in.index';
        } elseif (strpos($routeName, 'mindo.transactions.out.') === 0) {
            $indexRoute = 'mindo.transactions.out.index';
        }

        // Verify transaction type matches the route
        if (strpos($routeName, 'mindo.transactions.in.') === 0 && $transaction->transaction_type !== 'in') {
            abort(404, 'Transaction not found in this section');
        } elseif (strpos($routeName, 'mindo.transactions.out.') === 0 && $transaction->transaction_type !== 'out') {
            abort(404, 'Transaction not found in this section');
        }
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

            return redirect()->route($indexRoute)
                ->with('message', 'Transaksi berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()
                ->with([
                    'message' => 'Delete failed: ' . $e->getMessage(),
                    'alert-type' => 'error'
                ]);
        }
    }

    /**
     * Generate a new transaction code via AJAX request
     * 
     * @param string|null $transactionType The type of transaction ('in' or 'out')
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateTransactionCode(Request $request)
    {
        $transactionType = $request->query('type');
        
        return response()->json([
            'success' => true,
            'transaction_code' => Transaction::generateTransactionCode($transactionType)
        ]);
    }

    /**
     * Export transactions to Excel
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel(Request $request)
    {
        $type = $request->input('type');
        $filename = 'transactions';

        if ($type === 'in') {
            $filename = 'stock-in';
        } elseif ($type === 'out') {
            $filename = 'sales';
        }

        // Collect all filters from the request
        $filters = [
            'customer_id' => $request->input('customer_id'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to')
        ];

        return Excel::download(new TransactionsExport($type, $filters), $filename . '-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Export transactions to CSV
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportCsv(Request $request)
    {
        $type = $request->input('type');
        $filename = 'transactions';

        if ($type === 'in') {
            $filename = 'stock-in';
        } elseif ($type === 'out') {
            $filename = 'sales';
        }

        // Collect all filters from the request
        $filters = [
            'customer_id' => $request->input('customer_id'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to')
        ];

        return Excel::download(new TransactionsExport($type, $filters), $filename . '-' . date('Y-m-d') . '.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    /**
     * Export transactions to PDF
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(Request $request)
    {
        $type = $request->input('type');
        $filename = 'transactions';
        $title = 'All Transactions';

        $query = Transaction::with(['customer', 'user']);

        if ($type === 'in') {
            $query->stockIn();
            $filename = 'stock-in';
            $title = 'Transaksi Barang Masuk';
        } elseif ($type === 'out') {
            $query->stockOut();
            $filename = 'sales';
            $title = 'Transaksi Barang Keluar';
        }

        $transactions = $query->get();

        $pdf = PDF::loadView('admin.pages.transactions.pdf', [
            'transactions' => $transactions,
            'title' => $title
        ]);

        return $pdf->download($filename . '-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export ALL transaction items to Excel
     * 
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportAllItemsExcel()
    {
        // Export all transaction items (no filter by transaction)
        return Excel::download(new TransactionItemsExport(null), 'all-transaction-items-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Export transaction items to Excel
     * 
     * @param Transaction $transaction
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportItemsExcel(Transaction $transaction)
    {
        $code = $transaction->transaction_code;
        return Excel::download(new TransactionItemsExport($transaction->id), 'transaction-' . $code . '-items.xlsx');
    }

    /**
     * Export transaction invoice to PDF
     * 
     * @param Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function exportInvoice(Transaction $transaction)
    {
        $transaction->load(['customer', 'user', 'items.product.unit']);

        $pdf = PDF::loadView('admin.pages.transactions.invoice', [
            'transaction' => $transaction
        ]);

        return $pdf->download('invoice-' . $transaction->transaction_code . '.pdf');
    }
}
