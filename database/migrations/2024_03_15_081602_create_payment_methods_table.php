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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('method_name');
            $table->string('user_id');
            $table->string('master_key')->nullable();
            $table->string('master_value')->nullable();
            $table->string('stripe_payment_method_id');
            $table->string('card_brand')->nullable();
            $table->string('card_display_brand')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('card_last_four')->nullable();
            $table->string('card_exp_month')->nullable();
            $table->string('card_exp_year')->nullable();
            $table->string('card_country')->nullable();
            $table->string('card_funding')->nullable();
            $table->boolean('is_active')->default(false);
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
        Schema::dropIfExists('payment_methods');
    }
};
