<?php

namespace Database\Seeders;

use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Shop::create([
            'name' => "Warehouse",
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'open_time' => Carbon::now('Asia/Yangon')->format('H:i'),
            'close_time' => Carbon::now('Asia/Yangon')->format('H:i'),
            'is_warehouse' => true
        ]);

        $names = ['teashop1', 'teashop2'];

        foreach ($names as $name) {
            Shop::create([
                'name' => $name,
                'address' => fake()->address(),
                'phone' => fake()->phoneNumber(),
                'open_time' => Carbon::now('Asia/Yangon')->format('H:i'),
                'close_time' => Carbon::now('Asia/Yangon')->format('H:i'),
                'is_warehouse' => false
            ]);
        }
        
    }
}
