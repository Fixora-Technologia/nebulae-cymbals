<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing categories and units to reference
        $categories = ProductCategory::all();
        $units = Unit::all();
        
        if ($categories->isEmpty() || $units->isEmpty()) {
            $this->command->info('Please run ProductCategorySeeder and UnitSeeder first!');
            return;
        }

        // Sample cymbal products
        $products = [
            [
                'name' => 'Nebulae Splash 8"',
                'sku' => 'NB-SPL-08',
                'description' => 'Bright and fast 8-inch splash cymbal with quick decay',
                'price' => 89.99,
                'stock' => 15,
                'min_stock' => 5,
                'category_id' => $categories->where('name', 'Splash')->first()->id ?? $categories->first()->id,
                'unit_id' => $units->where('name', 'Piece')->first()->id ?? $units->first()->id,
            ],
            [
                'name' => 'Nebulae Crash 16"',
                'sku' => 'NB-CRS-16',
                'description' => 'Medium-thin crash with explosive attack and medium sustain',
                'price' => 149.99,
                'stock' => 20,
                'min_stock' => 8,
                'category_id' => $categories->where('name', 'Crash')->first()->id ?? $categories->first()->id,
                'unit_id' => $units->where('name', 'Piece')->first()->id ?? $units->first()->id,
            ],
            [
                'name' => 'Nebulae Crash 18"',
                'sku' => 'NB-CRS-18',
                'description' => 'Powerful crash with dark undertones and long sustain',
                'price' => 179.99,
                'stock' => 18,
                'min_stock' => 7,
                'category_id' => $categories->where('name', 'Crash')->first()->id ?? $categories->first()->id,
                'unit_id' => $units->where('name', 'Piece')->first()->id ?? $units->first()->id,
            ],
            [
                'name' => 'Nebulae Ride 20"',
                'sku' => 'NB-RID-20',
                'description' => 'Medium-weight ride with clear stick definition and balanced wash',
                'price' => 219.99,
                'stock' => 12,
                'min_stock' => 5,
                'category_id' => $categories->where('name', 'Ride')->first()->id ?? $categories->first()->id,
                'unit_id' => $units->where('name', 'Piece')->first()->id ?? $units->first()->id,
            ],
            [
                'name' => 'Nebulae Ride 22"',
                'sku' => 'NB-RID-22',
                'description' => 'Heavy ride with pronounced bell and minimal wash',
                'price' => 249.99,
                'stock' => 10,
                'min_stock' => 4,
                'category_id' => $categories->where('name', 'Ride')->first()->id ?? $categories->first()->id,
                'unit_id' => $units->where('name', 'Piece')->first()->id ?? $units->first()->id,
            ],
            [
                'name' => 'Nebulae Hi-Hat 14"',
                'sku' => 'NB-HHT-14',
                'description' => 'Crisp and responsive hi-hat pair with clean chick sound',
                'price' => 199.99,
                'stock' => 15,
                'min_stock' => 6,
                'category_id' => $categories->where('name', 'Hi-Hat')->first()->id ?? $categories->first()->id,
                'unit_id' => $units->where('name', 'Pair')->first()->id ?? $units->first()->id,
            ],
            [
                'name' => 'Nebulae China 16"',
                'sku' => 'NB-CHN-16',
                'description' => 'Trashy and explosive china with quick decay',
                'price' => 159.99,
                'stock' => 3, // At minimum threshold
                'min_stock' => 3,
                'category_id' => $categories->where('name', 'China')->first()->id ?? $categories->first()->id,
                'unit_id' => $units->where('name', 'Piece')->first()->id ?? $units->first()->id,
            ],
            [
                'name' => 'Nebulae Splash 10"',
                'sku' => 'NB-SPL-10',
                'description' => 'Fast and bright splash with medium sustain',
                'price' => 99.99,
                'stock' => 4, // Below minimum threshold
                'min_stock' => 5,
                'category_id' => $categories->where('name', 'Splash')->first()->id ?? $categories->first()->id,
                'unit_id' => $units->where('name', 'Piece')->first()->id ?? $units->first()->id,
            ],
            [
                'name' => 'Nebulae Hi-Hat 13"',
                'sku' => 'NB-HHT-13',
                'description' => 'Quick and articulate hi-hat pair for jazz and fusion',
                'price' => 189.99,
                'stock' => 10,
                'min_stock' => 4,
                'category_id' => $categories->where('name', 'Hi-Hat')->first()->id ?? $categories->first()->id,
                'unit_id' => $units->where('name', 'Pair')->first()->id ?? $units->first()->id,
            ],
            [
                'name' => 'Nebulae Crash 20"',
                'sku' => 'NB-CRS-20',
                'description' => 'Heavy crash with long sustain and dark character',
                'price' => 199.99,
                'stock' => 2, // Below minimum threshold
                'min_stock' => 3,
                'category_id' => $categories->where('name', 'Crash')->first()->id ?? $categories->first()->id,
                'unit_id' => $units->where('name', 'Piece')->first()->id ?? $units->first()->id,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        $this->command->info('Product seeder completed successfully!');
    }
}
