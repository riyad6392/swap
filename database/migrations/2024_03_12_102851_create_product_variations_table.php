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
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->string('size');
            $table->string('color');
            $table->integer('price');
            $table->integer('stock');
            $table->integer('discount');
            $table->integer('quantity');
            $table->integer('discount_type');
            $table->integer('discount_start_date');
            $table->integer('discount_end_date');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variations');
    }
};
