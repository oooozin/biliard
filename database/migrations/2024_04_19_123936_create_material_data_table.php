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
        Schema::create('material_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('material_id');
            $table->unsignedBigInteger('shop_id');
            $table->unsignedInteger('qty')->nullable();
            $table->auditColumns();

            $table->foreign('material_id')
                ->references('id')
                ->on('materials')
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
        Schema::dropIfExists('material_data');
    }
};
