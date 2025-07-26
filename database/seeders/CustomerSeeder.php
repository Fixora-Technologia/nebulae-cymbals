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
            ['name' => 'Seven Stereo Musik', 'contact' => '08123456789', 'phone' => '08123456789', 'email' => 'sevenstereomusik@gmail.com', 'address' => 'Bandung, Jawa Barat'],
            ['name' => 'Pop Music', 'contact' => '08123456789', 'phone' => '08123456789', 'email' => 'popmusic@gmail.com', 'address' => 'Bandung, Jawa Barat'],
            ['name' => 'Ferjul Drum Shop', 'contact' => '08123456789', 'phone' => '08123456789', 'email' => 'ferjuldrumshop@gmail.com', 'address' => 'Subang, Jawa Barat'],
            ['name' => 'Oemah Drum Creative (ODC)', 'contact' => '08123456789', 'phone' => '08123456789', 'email' => 'oemahdrumcreative@gmail.com', 'address' => 'Denpasar, Bali'],
            ['name' => 'Chic\'s Music', 'contact' => '08123456789', 'phone' => '08123456789', 'email' => 'chicsmusic@gmail.com', 'address' => 'Jakarta Timur'],
            ['name' => 'Dbeat Musik', 'contact' => '08123456789', 'phone' => '08123456789', 'email' => 'dbeatmusik@gmail.com', 'address' => 'Denpasar'],
            ['name' => 'Manado Musik Center', 'contact' => '08123456789', 'phone' => '08123456789', 'email' => 'manadomusikcenter@gmail.com', 'address' => 'Manado'],
            ['name' => 'Istana Musik Medan', 'contact' => '08123456789', 'phone' => '08123456789', 'email' => 'istanamusikmedan@gmail.com', 'address' => 'Kota Medan'],
            ['name' => 'Julang Marching Equipment', 'contact' => '08123456789', 'phone' => '08123456789', 'email' => 'julangmarchingequipment@gmail.com', 'address' => 'Yogyakarta'],
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate(
                $customer
            );
        }
    }
}
