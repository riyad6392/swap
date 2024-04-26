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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('uid')->unique();
            $table->string('stripe_price_id')->nullable();
            $table->string('description');
            $table->string('short_description');
            $table->double('amount', 8, 2);
            $table->string('currency');
            $table->enum('interval', ['month', 'year']);
            $table->string('interval_duration');
            $table->string('is_super_swapper')->nullable();
            $table->boolean('is_active')->default(0);
            $table->enum('plan_type',['basic','premium']);
            $table->string('created_by');
            $table->string('updated_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
