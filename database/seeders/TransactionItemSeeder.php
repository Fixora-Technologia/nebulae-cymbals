<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Database\Seeder;

class TransactionItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing transactions and products to reference
        $transactions = Transaction::all();
        $products = Product::all();
        
        if ($transactions->isEmpty() || $products->isEmpty()) {
            $this->command->info('Please run TransactionSeeder and ProductSeeder first!');
            return;
        }

        // For each transaction, create 1-5 transaction items
        foreach ($transactions as $transaction) {
            // Determine how many items for this transaction (1-5)
            $itemCount = rand(1, 5);
            
            // Get random products for this transaction
            $transactionProducts = $products->random($itemCount);
            
            $totalValue = 0;
            
            foreach ($transactionProducts as $product) {
                // Random quantity between 1 and 5
                $quantity = rand(1, 5);
                
                // Use product price as unit price
                $unitPrice = $product->price;
                
                // Calculate subtotal
                $subtotal = $quantity * $unitPrice;
                
                // Add to transaction's total value
                $totalValue += $subtotal;
                
                // Create transaction item
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                    'created_at' => $transaction->created_at,
                    'updated_at' => $transaction->updated_at,
                ]);
            }
            
            // Update transaction's total value to match sum of items
            $transaction->update([
                'total_value' => $totalValue
            ]);
        }

        $this->command->info('Transaction Item seeder completed successfully!');
    }
}
