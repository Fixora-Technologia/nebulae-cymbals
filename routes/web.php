<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\OrganizationalPositionController;
use App\Http\Controllers\SectorController;
use App\Http\Controllers\RegulationController;
use App\Http\Controllers\CouncilController;
use App\Http\Controllers\ManagementController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\GaleriController;
use App\Http\Controllers\PesanController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\TestimoniController;
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

// Public routes don't have 'mindo' prefix
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/news/{news}', [HomeController::class, 'newsDetail'])->name('news.detail');
Route::get('/gallery/{galeri}', [HomeController::class, 'galeriDetail'])->name('galeri.detail');
Route::get('/galleryAll', [GaleriController::class, 'galleryAll'])->name('gallery.all');
Route::get('/history', [HomeController::class, 'history'])->name('history');
Route::get('/vision-mission', [HomeController::class, 'visionMission'])->name('vision-mission');
Route::get('/sectors', [HomeController::class, 'sectors'])->name('sectors');
Route::get('/dpk-apindo-jabar', [HomeController::class, 'dpkApindoJabar'])->name('dpkApindoJabar');
Route::get('/managements', [HomeController::class, 'managements'])->name('managements');
Route::get('/regulations', [HomeController::class, 'regulations'])->name('regulations');
Route::get('/news', [HomeController::class, 'news'])->name('allNews');
Route::get('/calendar', [HomeController::class, 'calendar'])->name('calendar.index');
Route::get('/activity/{activity}', [HomeController::class, 'activityShow'])->name('activity.show');
Route::get('/testimoni', [TestimoniController::class, 'index'])->name('testimoni.index');
Route::get('/news', [NewsController::class, 'allNews'])->name('news.index');

Auth::routes(['verify' => true]);

// Admin routes (prefixed with 'mindo')
Route::middleware(['auth', 'can:DASHBOARD'])->prefix('mindo')->name('mindo.')->group(function () {
    // Dashboard
    Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard/monthly-sales', [App\Http\Controllers\DashboardController::class, 'getMonthlySalesData'])->name('dashboard.monthly-sales');
    Route::get('/dashboard/sales-comparison', [App\Http\Controllers\DashboardController::class, 'getSalesComparisonData'])->name('dashboard.sales-comparison');

    // Resource controllers
    Route::resources([
        'roles'                   => RoleController::class,
        'users'                   => UserController::class,
        'permissions'             => PermissionController::class,
        'members'                 => MemberController::class,
        'news'                    => NewsController::class,
        'organizational-positions' => OrganizationalPositionController::class,
        'sectors'                 => SectorController::class,
        'regulations'             => RegulationController::class,
        'councils'                => CouncilController::class,
        'managements'            => ManagementController::class,
        'galeri'                 => GaleriController::class,
        'pesan'                => PesanController::class,
        'activities'                => ActivityController::class,
        'testimoni'                => TestimoniController::class,
        'product-categories'     => ProductCategoryController::class,
        'units'                  => UnitController::class,
        'products'               => ProductController::class,
        'customers'              => CustomerController::class,
        'transactions'           => TransactionController::class,
        'transaction-items'      => TransactionItemController::class,
    ]);
    
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
});
