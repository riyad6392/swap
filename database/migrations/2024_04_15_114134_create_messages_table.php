<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('receiver_id');
            $table->unsignedBigInteger('conversation_id');
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('swap_id');
            $table->enum('message_type', ['message', 'notification', 'image', 'video', 'audio']);
            $table->longText('message');
            $table->json('data')->nullable();
            $table->string('file_path')->nullable();
            $table->dateTime('read_at')->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->timestamps();

            $table->foreign('conversation_id')->references('id')->on('conversations');
            $table->foreign('sender_id')->references('id')->on('users');
            $table->foreign('receiver_id')->references('id')->on('users');
//            $table->foreign('swap_id')->references('id')->on('swaps');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
