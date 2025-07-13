<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class ProductCategoryPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'PRODUCT_CATEGORY_LIST',
            'PRODUCT_CATEGORY_ADD',
            'PRODUCT_CATEGORY_EDIT',
            'PRODUCT_CATEGORY_DELETE',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }
    }
}
