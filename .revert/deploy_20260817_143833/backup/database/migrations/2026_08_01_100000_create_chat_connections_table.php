<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatConnectionsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('chat_connections')) {
            Schema::create('chat_connections', function (Blueprint $table) {
                $table->id();
                $table->integer('sender_id')->unsigned();
                $table->integer('receiver_id')->unsigned();
                $table->enum('status', ['pending', 'accepted', 'declined', 'blocked'])->default('pending');
                $table->bigInteger('created_at');
                $table->bigInteger('updated_at')->nullable();

                $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');

                // Prevent duplicate requests between two users
                $table->unique(['sender_id', 'receiver_id']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('chat_connections');
    }
}
