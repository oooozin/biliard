<?php

namespace Database\Seeders;

use App\Models\Cashier;
use Illuminate\Database\Seeder;

class CashierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        for ($i = 0; $i < 3; $i++) {
            Cashier::create([
                'name' => fake()->name(),
                'phone' => fake()->phoneNumber(),
                'shop_id' => 1
            ]);
        }
    }
}
