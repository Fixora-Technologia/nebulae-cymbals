<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionItemController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes don't have 'mindo' prefix - only authentication routes are publicly accessible
Route::get('/', function() {
    // If user is already authenticated, redirect to dashboard
    if (Auth::check()) {
        return redirect()->route('mindo.home');
    }
    return view('public.pages.welcome');
})->name('home');

Auth::routes(['verify' => true]);

// Admin routes (prefixed with 'mindo')
Route::middleware(['auth', 'can:DASHBOARD'])->prefix('mindo')->name('mindo.')->group(function () {
    // Dashboard
    Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('home');
    Route::post('/dashboard/data', [DashboardController::class, 'getDashboardData'])->name('dashboard.data');
    Route::get('/dashboard/monthly-sales', [DashboardController::class, 'getMonthlySalesData'])->name('dashboard.monthly-sales');
    Route::get('/dashboard/sales-comparison', [DashboardController::class, 'getSalesComparisonData'])->name('dashboard.sales-comparison');
    Route::get('/dashboard/top-products', [DashboardController::class, 'getTopProductsData'])->name('dashboard.top-products');
    Route::get('/dashboard/top-categories', [DashboardController::class, 'getTopCategoriesData'])->name('dashboard.top-categories');

    // Resource controllers
    Route::resources([
        'roles'                   => RoleController::class,
        'users'                   => UserController::class,
        'permissions'             => PermissionController::class,
        'product-categories'     => ProductCategoryController::class,
        'units'                  => UnitController::class,
        'products'               => ProductController::class,
        'customers'              => CustomerController::class,
        // Replaced with separate transaction in/out routes
        'transaction-items'      => TransactionItemController::class,
    ]);
    
    // Transaction In routes
    Route::group(['prefix' => 'transactions/in', 'as' => 'transactions.in.'], function () {
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::get('/create', [TransactionController::class, 'create'])->name('create');
        Route::post('/', [TransactionController::class, 'store'])->name('store');
        Route::get('/{transaction}', [TransactionController::class, 'show'])->name('show');
        Route::get('/{transaction}/edit', [TransactionController::class, 'edit'])->name('edit');
        Route::put('/{transaction}', [TransactionController::class, 'update'])->name('update');
        Route::delete('/{transaction}', [TransactionController::class, 'destroy'])->name('destroy');
        Route::get('/export/excel', [TransactionController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/csv', [TransactionController::class, 'exportCsv'])->name('export.csv');
        Route::get('/export/pdf', [TransactionController::class, 'exportPdf'])->name('export.pdf');
    });
    
    // Transaction Out routes
    Route::group(['prefix' => 'transactions/out', 'as' => 'transactions.out.'], function () {
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::get('/create', [TransactionController::class, 'create'])->name('create');
        Route::post('/', [TransactionController::class, 'store'])->name('store');
        Route::get('/{transaction}', [TransactionController::class, 'show'])->name('show');
        Route::get('/{transaction}/edit', [TransactionController::class, 'edit'])->name('edit');
        Route::put('/{transaction}', [TransactionController::class, 'update'])->name('update');
        Route::delete('/{transaction}', [TransactionController::class, 'destroy'])->name('destroy');
        Route::get('/export/excel', [TransactionController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/csv', [TransactionController::class, 'exportCsv'])->name('export.csv');
        Route::get('/export/pdf', [TransactionController::class, 'exportPdf'])->name('export.pdf');
    });

    // Common transaction routes
    Route::prefix('transactions')->group(function () {
        Route::get('/generate-code', [TransactionController::class, 'generateTransactionCode'])->name('transactions.generate-code');
        Route::get('/export/items/excel', [TransactionController::class, 'exportAllItemsExcel'])->name('transactions.export.items.excel');
        Route::get('/{transaction}/export/items', [TransactionController::class, 'exportItemsExcel'])->name('transactions.export.items');
        Route::get('/{transaction}/export/invoice', [TransactionController::class, 'exportInvoice'])->name('transactions.export.invoice');
    });

    // API routes for transaction items
    Route::get('api/transaction-items', [TransactionItemController::class, 'index']);
    Route::post('api/transaction-items', [TransactionItemController::class, 'store']);
    Route::get('api/transaction-items/{transactionItem}', [TransactionItemController::class, 'show']);
    Route::delete('api/transaction-items/{transactionItem}', [TransactionItemController::class, 'destroy']);

    // Activity logs
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('activity-logs/{activity}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
    
    // Product SKU generation
    Route::get('products/generate-sku', [ProductController::class, 'generateSku'])->name('products.generate-sku');
    
    // Transaction code generation
    Route::get('transactions/generate-code', [TransactionController::class, 'generateTransactionCode'])->name('transactions.generate-code');
    
    // Export routes
    // Products export
    Route::get('products/export/excel', [ProductController::class, 'exportExcel'])->name('products.export.excel');
    Route::get('products/export/csv', [ProductController::class, 'exportCsv'])->name('products.export.csv');
    Route::get('products/export/pdf', [ProductController::class, 'exportPdf'])->name('products.export.pdf');
    
    // Customers export
    Route::get('customers/export/excel', [CustomerController::class, 'exportExcel'])->name('customers.export.excel');
    Route::get('customers/export/csv', [CustomerController::class, 'exportCsv'])->name('customers.export.csv');
    Route::get('customers/export/pdf', [CustomerController::class, 'exportPdf'])->name('customers.export.pdf');
    
    // Transactions export
    Route::get('transactions/export/excel', [TransactionController::class, 'exportExcel'])->name('transactions.export.excel');
    Route::get('transactions/export/csv', [TransactionController::class, 'exportCsv'])->name('transactions.export.csv');
    Route::get('transactions/export/pdf', [TransactionController::class, 'exportPdf'])->name('transactions.export.pdf');
    Route::get('transactions/export/items/excel', [TransactionController::class, 'exportAllItemsExcel'])->name('transactions.export.items.excel');
    Route::get('transactions/{transaction}/export/items', [TransactionController::class, 'exportItemsExcel'])->name('transactions.export.items');
    Route::get('transactions/{transaction}/export/invoice', [TransactionController::class, 'exportInvoice'])->name('transactions.export.invoice');
    
    // Notifications
    Route::get('notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/unread', [\App\Http\Controllers\Admin\NotificationController::class, 'getUnreadNotifications'])->name('notifications.unread');
    Route::post('notifications/{id}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/read-all', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('notifications/{id}', [\App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('notifications.destroy');
});
