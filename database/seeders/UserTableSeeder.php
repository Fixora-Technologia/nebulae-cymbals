<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Administrator User
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('12345678'),
            ]
        );
        $adminRole = Role::where('name', 'ADMIN')->first();
        $adminUser->assignRole($adminRole);

        // Developer User
        $developerUser = User::firstOrCreate(
            ['email' => 'developer@gmail.com'],
            [
                'name' => 'Developer',
                'password' => bcrypt('12345678'),
            ]
        );
        $developerRole = Role::where('name', 'DEVELOPER')->first();
        $developerUser->assignRole($developerRole);

        // Member User
        $memberUser = User::firstOrCreate(
            ['email' => 'member@gmail.com'],
            [
                'name' => 'Member',
                'password' => bcrypt('12345678'),
            ]
        );
        $memberRole = Role::where('name', 'MEMBER')->first();
        $memberUser->assignRole($memberRole);
    }
}
