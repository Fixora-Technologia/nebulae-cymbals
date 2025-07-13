<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'ADMIN',
            'DEVELOPER',
            'MEMBER',
            'Super Admin',
            'Kepala Gudang',
            'Admin Gudang',
        ];

        foreach ($roles as $role) {
            Role::findOrCreate($role);
        }

        // Sync all permissions to ADMIN, DEVELOPER, and Super Admin
        $allPermissions = Permission::all(); // Retrieve all permissions

        $adminRole = Role::where('name', 'ADMIN')->first();
        $developerRole = Role::where('name', 'DEVELOPER')->first();
        $superAdminRole = Role::where('name', 'Super Admin')->first();

        if ($adminRole) {
            $adminRole->syncPermissions($allPermissions); // Assign all permissions to ADMIN
        }

        if ($developerRole) {
            $developerRole->syncPermissions($allPermissions); // Assign all permissions to DEVELOPER
        }
        
        if ($superAdminRole) {
            $superAdminRole->syncPermissions($allPermissions); // Assign all permissions to Super Admin
        }
        
        // Assign specific permissions to Kepala Gudang
        $kepalaGudangRole = Role::where('name', 'Kepala Gudang')->first();
        if ($kepalaGudangRole) {
            $kepalaGudangPermissions = [
                // Stock management
                'PRODUCT_LIST',
                // Incoming goods
                'TRANSACTION_LIST', 'TRANSACTION_ADD',
                // Outgoing goods
                'TRANSACTION_LIST', 'TRANSACTION_ADD',
                // Related permissions
                'CUSTOMER_LIST',
                'UNIT_LIST',
                'PRODUCT_CATEGORY_LIST',
            ];
            $kepalaGudangRole->syncPermissions($kepalaGudangPermissions);
        }
        
        // Assign specific permissions to Admin Gudang
        $adminGudangRole = Role::where('name', 'Admin Gudang')->first();
        if ($adminGudangRole) {
            $adminGudangPermissions = [
                // Stock management
                'PRODUCT_LIST', 'PRODUCT_ADD', 'PRODUCT_EDIT',
                // Incoming goods
                'TRANSACTION_LIST', 'TRANSACTION_ADD',
                // Outgoing goods
                'TRANSACTION_LIST', 'TRANSACTION_ADD',
                // Stock reports
                'TRANSACTION_LIST',
                // Related permissions
                'CUSTOMER_LIST', 'CUSTOMER_ADD', 'CUSTOMER_EDIT',
                'UNIT_LIST',
                'PRODUCT_CATEGORY_LIST',
            ];
            $adminGudangRole->syncPermissions($adminGudangPermissions);
        }
    }
}
