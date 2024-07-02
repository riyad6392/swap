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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->integer('swap_id');
            $table->string('requested_address')->nullable();
            $table->string('requested_tracking_number')->nullable();
            $table->string('requested_carrier_name')->nullable();
            $table->string('requested_carrier_contact')->nullable();
            $table->date('requested_expected_delivery_date')->nullable();

            $table->string('exchanged_address')->nullable();
            $table->string('exchanged_tracking_number')->nullable();
            $table->string('exchanged_carrier_name')->nullable();
            $table->string('exchanged_carrier_contact')->nullable();
            $table->date('exchanged_expected_delivery_date')->nullable();
            $table->enum('status', ['pending', 'shipped', 'delivered'])->default('pending');
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
        Schema::dropIfExists('shipments');
    }
};
