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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('uid');
            $table->string('user_id');
            $table->string('plan_id');
            $table->string('status');
            $table->string('start_date');
            $table->string('end_date');
            $table->double('amount', 8, 2);
            $table->string('payment_method_id');
            $table->string('stripe_subscription_id');
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
        Schema::dropIfExists('subscriptions');
    }
};
