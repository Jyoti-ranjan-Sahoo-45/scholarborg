<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrialDemoBookingsTable extends Migration
{
    public function up()
    {
        Schema::create('trial_demo_bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable()->index();

            // Step 1: Academic Profile
            $table->string('country')->nullable();
            $table->string('pin_code')->nullable();
            $table->string('board_of_education')->nullable();
            $table->string('grade_class')->nullable();
            $table->string('subjects')->nullable();
            $table->text('topics')->nullable();

            // Step 2: Learning Preferences
            $table->enum('class_mode', ['online', 'offline', 'either'])->default('online');
            $table->string('preferred_language')->default('English');
            $table->text('learning_goals')->nullable();

            // Step 3: Demo Availability
            $table->string('demo_date')->nullable();
            $table->string('time_range')->nullable();
            $table->string('days_available')->nullable();
            $table->string('start_delay')->nullable();

            // Step 4: Your Details
            $table->string('student_name');
            $table->string('parent_name')->nullable();
            $table->string('email');
            $table->string('whatsapp_number')->nullable();

            // Status
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->timestamp('email_verified_at')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('trial_demo_bookings');
    }
}
