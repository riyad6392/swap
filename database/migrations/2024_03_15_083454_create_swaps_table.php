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
            $table->string('uid');
            $table->integer('user_id');
            $table->integer('requested_user_id')->comment('User who requested the swap');
            $table->integer('exchanged_user_id')->comment('User who accepted the swap');

            $table->enum('exchange_user_status', ['pending', 'accepted','approved', 'completed', 'decline'])->default('pending');
            $table->enum('request_user_status', ['requested', 'accepted','approved', 'completed', 'rejected'])->default('requested');

//            $table->enum('status', ['requested', 'accepted', 'completed', 'decline'])->default('requested');

            $table->integer('requested_wholesale_amount')->nullable();
            $table->double('requested_total_commission',8, 2)->default(0);

            $table->integer('exchanged_wholesale_amount')->nullable();
            $table->double('exchanged_total_commission',8, 2)->default(0);

//            $table->boolean('is_approved_by_requester')->default(false);
//            $table->boolean('is_approved_by_exchanger')->default(false);

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
