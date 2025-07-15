<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with statistics.
     */
    public function index(): View
    {
        // Inventory statistics
        $totalProducts = Product::count();
        $totalCustomers = Customer::count();
        $totalTransactions = Transaction::count();

        // Get current month and year for default chart display
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Calculate stock in/out totals for current month up to today
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfToday = Carbon::now()->endOfDay();

        // Stock In
        $stockIn = Transaction::where('transaction_type', 'in')
            ->whereBetween('created_at', [$startOfMonth, $endOfToday])
            ->pluck('id');
        $stockInQty = TransactionItem::whereIn('transaction_id', $stockIn)->sum('quantity');
        $stockInValue = TransactionItem::whereIn('transaction_id', $stockIn)
            ->select(DB::raw('SUM(quantity * unit_price) as total'))->value('total') ?? 0;

        // Stock Out
        $stockOut = Transaction::where('transaction_type', 'out')
            ->whereBetween('created_at', [$startOfMonth, $endOfToday])
            ->pluck('id');
        $stockOutQty = TransactionItem::whereIn('transaction_id', $stockOut)->sum('quantity');
        $stockOutValue = TransactionItem::whereIn('transaction_id', $stockOut)
            ->select(DB::raw('SUM(quantity * unit_price) as total'))->value('total') ?? 0;

        return view('admin.pages.dashboard.index', compact(
            'totalProducts',
            'totalCustomers',
            'totalTransactions',
            'currentMonth',
            'currentYear',
            'stockInQty',
            'stockInValue',
            'stockOutQty',
            'stockOutValue'
        ));
    }

    /**
     * Get monthly sales data for charts
     */
    public function getMonthlySalesData(Request $request): JsonResponse
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        // Get daily sales data for the selected month
        $dailySales = Transaction::stockOut()
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->select(
                DB::raw('DAY(created_at) as day'),
                DB::raw('SUM(total_value) as total_value')
            )
            ->groupBy(DB::raw('DAY(created_at)'))
            ->orderBy('day')
            ->get();

        // Get daily quantity data for the selected month
        $dailyQuantity = TransactionItem::join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.transaction_type', 'out')
            ->whereMonth('transactions.created_at', $month)
            ->whereYear('transactions.created_at', $year)
            ->select(
                DB::raw('DAY(transactions.created_at) as day'),
                DB::raw('SUM(transaction_items.quantity) as total_quantity')
            )
            ->groupBy(DB::raw('DAY(transactions.created_at)'))
            ->orderBy('day')
            ->get();

        // Format data for Chart.js
        $daysInMonth = Carbon::createFromDate($year, $month)->daysInMonth;
        $days = range(1, $daysInMonth);

        $values = array_fill(0, $daysInMonth, 0);
        foreach ($dailySales as $sale) {
            $values[$sale->day - 1] = (float) $sale->total_value;
        }

        $quantities = array_fill(0, $daysInMonth, 0);
        foreach ($dailyQuantity as $qty) {
            $quantities[$qty->day - 1] = (int) $qty->total_quantity;
        }

        return response()->json([
            'days' => $days,
            'values' => $values,
            'quantities' => $quantities
        ]);
    }

    /**
     * Get sales comparison data for the last 12 months
     */
    public function getSalesComparisonData(): JsonResponse
    {
        // Get monthly sales data for the last 12 months
        $startDate = Carbon::now()->subMonths(11)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $monthlySales = Transaction::stockOut()
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_value) as total_value')
            )
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $monthlyQuantity = TransactionItem::join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.transaction_type', 'out')
            ->where('transactions.created_at', '>=', $startDate)
            ->where('transactions.created_at', '<=', $endDate)
            ->select(
                DB::raw('YEAR(transactions.created_at) as year'),
                DB::raw('MONTH(transactions.created_at) as month'),
                DB::raw('SUM(transaction_items.quantity) as total_quantity')
            )
            ->groupBy(DB::raw('YEAR(transactions.created_at)'), DB::raw('MONTH(transactions.created_at)'))
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Generate labels for the last 12 months
        $months = [];
        $values = [];
        $quantities = [];

        for ($i = 0; $i < 12; $i++) {
            $date = Carbon::now()->subMonths(11 - $i);
            $months[] = $date->format('M Y');

            $year = $date->year;
            $month = $date->month;

            // Find sales data for this month
            $sale = $monthlySales->first(function ($item) use ($year, $month) {
                return $item->year == $year && $item->month == $month;
            });

            $values[] = $sale ? (float) $sale->total_value : 0;

            // Find quantity data for this month
            $qty = $monthlyQuantity->first(function ($item) use ($year, $month) {
                return $item->year == $year && $item->month == $month;
            });

            $quantities[] = $qty ? (int) $qty->total_quantity : 0;
        }

        return response()->json([
            'months' => $months,
            'values' => $values,
            'quantities' => $quantities
        ]);
    }

    /**
     * Get top 10 best selling products (with 'Others') for the selected month/year
     */
    public function getTopProductsData(Request $request): \Illuminate\Http\JsonResponse
    {
        $month = $request->input('month', \Carbon\Carbon::now()->month);
        $year = $request->input('year', \Carbon\Carbon::now()->year);

        // Get product sales by quantity and revenue for the selected month/year
        $query = \App\Models\TransactionItem::join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->where('transactions.transaction_type', 'out')
            ->whereMonth('transactions.created_at', $month)
            ->whereYear('transactions.created_at', $year)
            ->groupBy('products.id', 'products.name')
            ->select(
                'products.name as product_name',
                \DB::raw('SUM(transaction_items.quantity) as total_quantity'),
                \DB::raw('SUM(transaction_items.quantity * transaction_items.unit_price) as total_revenue')
            )
            ->orderByDesc('total_quantity');

        $allProducts = $query->get();
        $topProducts = $allProducts->take(10);

        $totalQty = $allProducts->sum('total_quantity');
        $totalRevenue = $allProducts->sum('total_revenue');

        // Prepare chart data
        $labels = $topProducts->pluck('product_name')->toArray();
        $quantities = $topProducts->pluck('total_quantity')->toArray();
        $revenues = $topProducts->pluck('total_revenue')->toArray();
        $percentages = $topProducts->map(function ($item) use ($totalQty) {
            return $totalQty > 0 ? round($item->total_quantity / $totalQty * 100, 2) : 0;
        })->toArray();

        // Others
        $othersQty = $totalQty - array_sum($quantities);
        $othersRevenue = $totalRevenue - array_sum($revenues);
        $othersPercentage = $totalQty > 0 ? round($othersQty / $totalQty * 100, 2) : 0;
        if ($othersQty > 0) {
            $labels[] = 'Others';
            $quantities[] = $othersQty;
            $revenues[] = $othersRevenue;
            $percentages[] = $othersPercentage;
        }

        return response()->json([
            'labels' => $labels,
            'quantities' => $quantities,
            'revenues' => $revenues,
            'percentages' => $percentages,
            'total_quantity' => $totalQty,
            'total_revenue' => $totalRevenue
        ]);
    }

    /**
     * Get top 5 best selling product categories (with 'Others') for the selected month/year
     */
    public function getTopCategoriesData(Request $request): \Illuminate\Http\JsonResponse
    {
        $month = $request->input('month', \Carbon\Carbon::now()->month);
        $year = $request->input('year', \Carbon\Carbon::now()->year);

        $query = \App\Models\TransactionItem::join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->join('product_categories', 'products.category_id', '=', 'product_categories.id')
            ->where('transactions.transaction_type', 'out')
            ->whereMonth('transactions.created_at', $month)
            ->whereYear('transactions.created_at', $year)
            ->groupBy('product_categories.id', 'product_categories.name')
            ->select(
                'product_categories.name as category_name',
                \DB::raw('SUM(transaction_items.quantity) as total_quantity'),
                \DB::raw('SUM(transaction_items.quantity * transaction_items.unit_price) as total_revenue')
            )
            ->orderByDesc('total_quantity');

        $allCategories = $query->get();
        $topCategories = $allCategories->take(5);

        $totalQty = $allCategories->sum('total_quantity');
        $totalRevenue = $allCategories->sum('total_revenue');

        $labels = $topCategories->pluck('category_name')->toArray();
        $quantities = $topCategories->pluck('total_quantity')->toArray();
        $revenues = $topCategories->pluck('total_revenue')->toArray();
        $percentages = $topCategories->map(function ($item) use ($totalQty) {
            return $totalQty > 0 ? round($item->total_quantity / $totalQty * 100, 2) : 0;
        })->toArray();

        // Others
        $othersQty = $totalQty - array_sum($quantities);
        $othersRevenue = $totalRevenue - array_sum($revenues);
        $othersPercentage = $totalQty > 0 ? round($othersQty / $totalQty * 100, 2) : 0;
        if ($othersQty > 0) {
            $labels[] = 'Others';
            $quantities[] = $othersQty;
            $revenues[] = $othersRevenue;
            $percentages[] = $othersPercentage;
        }

        return response()->json([
            'labels' => $labels,
            'quantities' => $quantities,
            'revenues' => $revenues,
            'percentages' => $percentages,
            'total_quantity' => $totalQty,
            'total_revenue' => $totalRevenue
        ]);
    }
}
