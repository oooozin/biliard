<?php

namespace Database\Seeders;

use App\Models\Invoice;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Invoice::create([
            'invoice_number' => Str::orderedUuid(),
            'customer_id' => 1,
            'order_id' => 1,
            'subtotal' => 5000,
            'tax' => 0,
            'discount' => 0,
            'total' => 5000,
            'payment' => 'cash',
            'charge' => 5000,
            'refund' => 0,
        ]);
    }
}
