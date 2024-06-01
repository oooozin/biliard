<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('shop_id');
            $table->json('items');
            $table->unsignedInteger('subtotal');
            $table->unsignedInteger('tax')->nullable();
            $table->unsignedInteger('discount')->nullable();
            $table->unsignedInteger('total');
            $table->string('payment');
            $table->unsignedInteger('charge');
            $table->unsignedInteger('refund');
            $table->auditColumns();

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onDelete('cascade');

            $table->foreign('shop_id')
                ->references('id')
                ->on('shops')
                ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
