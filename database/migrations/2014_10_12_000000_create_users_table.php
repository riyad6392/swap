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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->boolean('subscription_is_active')->default(0);
            $table->string('stripe_customer_id')->nullable();
            $table->boolean('is_approved_by_admin')->default(0);
            $table->boolean('is_super_swapper')->default(0);
            $table->string('business_name')->nullable();
            $table->string('business_address')->nullable();
            $table->string('online_store_url')->nullable();
            $table->string('ein')->nullable();
            $table->string('resale_license')->nullable();
            $table->string('photo_of_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
