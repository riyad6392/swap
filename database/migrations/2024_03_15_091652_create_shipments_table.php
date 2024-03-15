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
            $table->string('requested_address');
            $table->string('requested_tracking_number');
            $table->string('requested_carrier_name');
            $table->string('requested_carrier_contact');
            $table->date('requested_expected_delivery_date');

            $table->string('exchanged_address');
            $table->string('exchanged_tracking_number');
            $table->string('exchanged_carrier_name');
            $table->string('exchanged_carrier_contact');
            $table->date('exchanged_expected_delivery_date');
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
