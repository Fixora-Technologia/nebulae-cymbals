<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class UpdateProductStockLevels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-product-stock-levels';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update stock levels for specific products to test low stock notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating product stock levels for testing...');

        // Update China 16"
        $product = Product::where('sku', 'NB-CHN-16')->first();
        if ($product) {
            $product->stock = 3; // At minimum threshold
            $product->save();
            $this->info("Updated {$product->name} stock to {$product->stock} (min: {$product->min_stock})");
        }

        // Update Splash 10"
        $product = Product::where('sku', 'NB-SPL-10')->first();
        if ($product) {
            $product->stock = 4; // Below minimum threshold
            $product->save();
            $this->info("Updated {$product->name} stock to {$product->stock} (min: {$product->min_stock})");
        }

        // Update Crash 20"
        $product = Product::where('sku', 'NB-CRS-20')->first();
        if ($product) {
            $product->stock = 2; // Below minimum threshold
            $product->save();
            $this->info("Updated {$product->name} stock to {$product->stock} (min: {$product->min_stock})");
        }

        $this->info('Product stock levels updated successfully!');
    }
}
