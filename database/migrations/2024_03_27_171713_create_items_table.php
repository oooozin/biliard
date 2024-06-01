<?php

use App\Enums\GeneralStatusEnum;
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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image')->nullable();
            $table->unsignedInteger('price');
            $table->unsignedInteger('purchase_price');
            $table->unsignedInteger('barcode');
            $table->string('status')->default(GeneralStatusEnum::ACTIVE->value);
            $table->unsignedBigInteger('category_id');
            $table->auditColumns();

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
