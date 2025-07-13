<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Crash',
            'Ride',
            'Hi Hat',
            'Chinnese',
            'Splash',
            'Cymbal Effect',
            'Custom Cymbal',
            'Bag',
        ];

        foreach ($categories as $category) {
            ProductCategory::firstOrCreate([
                'name' => $category,
            ]);
        }
    }
}
