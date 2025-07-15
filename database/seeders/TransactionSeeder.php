<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing customers and users to reference
        $customers = Customer::all();
        $users = User::all();
        
        if ($customers->isEmpty() || $users->isEmpty()) {
            $this->command->info('Please run CustomerSeeder and UserTableSeeder first!');
            return;
        }

        // Sample transactions for the last 3 months
        $startDate = Carbon::now()->subMonths(3);
        $endDate = Carbon::now();
        
        // Create 20 incoming transactions (stock additions)
        for ($i = 0; $i < 20; $i++) {
            $date = Carbon::createFromTimestamp(
                rand($startDate->timestamp, $endDate->timestamp)
            );
            
            // For incoming transactions, customer can be null (stock from manufacturer)
            $totalValue = rand(50000, 500000) / 100; // Random value between $500 and $5000
            
            Transaction::create([
                'transaction_type' => 'in',
                'transaction_code' => 'IN-' . $date->format('Ymd') . '-' . strtoupper(Str::random(5)),
                'customer_id' => null, // Incoming stock typically doesn't have a customer
                'user_id' => $users->random()->id,
                'total_value' => $totalValue,
                'notes' => 'Stock addition from manufacturer',
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
        
        // Create 30 outgoing transactions (sales)
        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::createFromTimestamp(
                rand($startDate->timestamp, $endDate->timestamp)
            );
            
            $totalValue = rand(10000, 300000) / 100; // Random value between $100 and $3000
            
            Transaction::create([
                'transaction_type' => 'out',
                'transaction_code' => 'OUT-' . $date->format('Ymd') . '-' . strtoupper(Str::random(5)),
                'customer_id' => $customers->random()->id,
                'user_id' => $users->random()->id,
                'total_value' => $totalValue,
                'notes' => 'Customer order',
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }

        $this->command->info('Transaction seeder completed successfully!');
    }
}
