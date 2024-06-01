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
        Schema::create('transfer_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_shop');
            $table->unsignedBigInteger('to_shop');
            $table->unsignedBigInteger('item_id');
            $table->unsignedInteger('qty');
            $table->auditColumns();

            $table->foreign('from_shop')
                ->references('id')
                ->on('shops')
                ->onDelete('cascade');

            $table->foreign('to_shop')
                ->references('id')
                ->on('shops')
                ->onDelete('cascade');

            $table->foreign('item_id')
                ->references('id')
                ->on('items')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_items');
    }
};
