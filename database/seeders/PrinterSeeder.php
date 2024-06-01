<?php

namespace Database\Seeders;

use App\Models\Printer;
use Illuminate\Database\Seeder;

class PrinterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Printer::create([
            'invoice_ip' => '192.168.1.100',
            'kitchen_ip' => '192.168.1.100',
            'bar_ip' => '192.168.1.100',
        ]);
    }
}
