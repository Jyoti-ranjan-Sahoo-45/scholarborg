<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRescheduleRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('reschedule_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('reserve_meeting_id');
            $table->unsignedInteger('requested_by'); // user_id who made the request
            $table->string('new_day'); // e.g. 2026-08-01
            $table->bigInteger('new_start_at')->nullable();
            $table->bigInteger('new_end_at')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('note')->nullable(); // student's reason
            $table->text('admin_note')->nullable(); // instructor/admin response
            $table->bigInteger('created_at');
            $table->bigInteger('updated_at')->nullable();

            $table->foreign('reserve_meeting_id')->references('id')->on('reserve_meetings')->onDelete('cascade');
            $table->foreign('requested_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reschedule_requests');
    }
}
