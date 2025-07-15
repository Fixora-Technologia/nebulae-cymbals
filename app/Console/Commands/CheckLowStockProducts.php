<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckLowStockProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-low-stock-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for products that are below their minimum stock threshold and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for low stock products...');
        
        // Get all products where stock is at or below min_stock threshold
        $lowStockProducts = Product::where('stock', '<=', DB::raw('min_stock'))->get();
        
        if ($lowStockProducts->isEmpty()) {
            $this->info('No products are below their minimum stock threshold.');
            return 0;
        }
        
        $this->info("Found {$lowStockProducts->count()} products below minimum stock threshold.");
        
        // Get admin users to notify (you can customize this based on roles/permissions)
        $adminUsers = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['Super Admin', 'Kepala Gudang']);
        })->get();
        
        if ($adminUsers->isEmpty()) {
            $this->error('No admin users found to notify!');
            Log::warning('Low stock notification failed: No admin users found to notify');
            return 1;
        }
        
        // Send notifications for each low stock product
        foreach ($lowStockProducts as $product) {
            $this->info("Sending notification for {$product->name} (Current stock: {$product->stock}, Min stock: {$product->min_stock})");
            
            foreach ($adminUsers as $admin) {
                $admin->notify(new LowStockNotification($product));
            }
        }
        
        $this->info('Low stock notifications sent successfully!');
        return 0;
    }
}
