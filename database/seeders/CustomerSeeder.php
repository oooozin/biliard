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

        for ($i = 0; $i < 3; $i++) {
            Customer::create([
                'name' => fake()->name(),
                'address' => fake()->address(),
                'phone' => fake()->phoneNumber(),
            ]);
        }
    }
}
