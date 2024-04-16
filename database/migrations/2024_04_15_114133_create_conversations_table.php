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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('channel_name');
            $table->unsignedBigInteger('user_id')->comment('owner of the conversation');
            $table->enum('conversation_type', ['private', 'group'])->default('private');
            $table->string('composite_id')->comment('sender_id:receiver_id');
            $table->string('reverse_composite_id')->comment('receiver_id:sender_id');
            $table->integer('last_message_id')->nullable();
            $table->longText('last_message')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
