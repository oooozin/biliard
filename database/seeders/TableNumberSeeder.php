<?php

namespace Database\Seeders;

use App\Models\TableNumber;
use Illuminate\Database\Seeder;

class TableNumberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tableNumbers = ['table 1', 'table 2', 'table 3', 'table 4'];

        foreach ($tableNumbers as $tableNumber) {
            TableNumber::create([
                'name' => $tableNumber,
                'cashier_id' => 1
            ]);
        }
    }
}
