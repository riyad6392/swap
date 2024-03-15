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
        Schema::create('swaps', function (Blueprint $table) {
            $table->id();
            $table->integer('uid');
            $table->integer('user_id');
            $table->integer('requested_user_id');
            $table->integer('exchanged_user_id');
            $table->string('status');
            $table->integer('requested_wholesale_amount');
            $table->integer('exchanged_wholesale_amount');
            $table->double('requested_total_commission',8, 2);
            $table->double('exchanged_total_commission',8, 2);
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
        Schema::dropIfExists('swaps');
    }
};
