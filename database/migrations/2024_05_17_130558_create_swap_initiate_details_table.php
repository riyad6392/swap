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
        Schema::create('swap_initiate_details', function (Blueprint $table) {
            $table->id();
            $table->integer('swap_id');
            $table->string('uid');
            $table->integer('user_id');
            $table->integer('product_id');
            $table->integer('product_variation_id');
            $table->integer('quantity');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('swap_initiate_details');
    }
};
