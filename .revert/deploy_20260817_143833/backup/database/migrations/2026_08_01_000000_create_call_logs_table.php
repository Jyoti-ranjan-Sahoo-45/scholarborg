<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCallLogsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('call_logs')) {
            Schema::create('call_logs', function (Blueprint $table) {
                $table->id();
                $table->integer('caller_id')->unsigned();
                $table->integer('receiver_id')->unsigned();
                $table->integer('booking_id')->unsigned()->nullable();
                $table->string('channel_name');
                $table->enum('status', ['initiated', 'ringing', 'connected', 'completed', 'missed', 'rejected'])->default('initiated');
                $table->bigInteger('started_at')->nullable();
                $table->bigInteger('ended_at')->nullable();
                $table->integer('duration')->nullable(); // in seconds
                $table->string('agora_resource_id')->nullable();
                $table->string('agora_sid')->nullable();
                $table->string('recording_file')->nullable(); // OneDrive sharing URL
                $table->enum('audit_status', ['pending', 'reviewed', 'flagged'])->default('pending');
                $table->text('audit_notes')->nullable();
                $table->integer('audited_by')->unsigned()->nullable();
                $table->bigInteger('created_at');
                $table->bigInteger('updated_at')->nullable();

                $table->foreign('caller_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('booking_id')->references('id')->on('reserve_meetings')->onDelete('set null');
                $table->foreign('audited_by')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('call_logs');
    }
}
