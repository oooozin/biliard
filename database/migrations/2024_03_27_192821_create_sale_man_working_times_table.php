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
        Schema::create('sale_man_working_times', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cashier_id');
            $table->unsignedBigInteger('table_id');
            $table->string('total_time');
            $table->auditColumns();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_man_working_times');
    }
};
