<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            ['name' => 'Seven Stereo Musik', 'address' => 'Bandung, Jawa Barat'],
            ['name' => 'Pop Music', 'address' => 'Bandung, Jawa Barat'],
            ['name' => 'Ferjul Drum Shop', 'address' => 'Subang, Jawa Barat'],
            ['name' => 'Oemah Drum Creative (ODC)', 'address' => 'Denpasar, Bali'],
            ['name' => 'Chic\'s Music', 'address' => 'Jakarta Timur'],
            ['name' => 'Dbeat Musik', 'address' => 'Denpasar'],
            ['name' => 'Manado Musik Center', 'address' => 'Manado'],
            ['name' => 'Istana Musik Medan', 'address' => 'Kota Medan'],
            ['name' => 'Julang Marching Equipment', 'address' => 'Yogyakarta'],
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate(
                ['name' => $customer['name']],
                ['address' => $customer['address']]
            );
        }
    }
}
