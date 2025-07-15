<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\News;
use App\Models\Pesan;
use App\Models\Activity;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with statistics.
     */
    public function index(): View
    {
        // Member statistics
        $totalExtraordinaryMembers = Member::where('is_extraordinary_member', true)->count();
        $totalNonExtraordinaryMembers = Member::where('is_extraordinary_member', false)->count();
        $totalMembers = Member::count();

        // Other statistics
        $totalNews = News::count();
        $totalPesan = Pesan::count();
        $totalActivities = Activity::count();
        
        // Inventory statistics
        $totalProducts = Product::count();
        $totalCustomers = Customer::count();
        $totalTransactions = Transaction::count();
        
        // Get current month and year for default chart display
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        return view('admin.pages.dashboard.index', compact(
            'totalExtraordinaryMembers',
            'totalNonExtraordinaryMembers',
            'totalMembers',
            'totalNews',
            'totalPesan',
            'totalActivities',
            'totalProducts',
            'totalCustomers',
            'totalTransactions',
            'currentMonth',
            'currentYear'
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
}
