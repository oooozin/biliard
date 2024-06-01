<?php

namespace Database\Seeders;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $today = Carbon::now('Asia/Yangon');

        Order::create([
            'table_number_id' => 1,
            'guest' => 4,
            'checkin' => $today,
        ]);
    }
}
