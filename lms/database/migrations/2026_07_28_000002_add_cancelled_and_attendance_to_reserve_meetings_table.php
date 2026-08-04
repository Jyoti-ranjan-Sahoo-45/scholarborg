<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddCancelledAndAttendanceToReserveMeetingsTable extends Migration
{
    public function up()
    {
        Schema::table('reserve_meetings', function (Blueprint $table) {
            $table->enum('cancelled_by', ['student', 'instructor', 'admin'])->nullable()->after('status');
            $table->bigInteger('cancelled_at')->nullable()->after('cancelled_by');
            $table->enum('attendance_status', ['present', 'absent', 'excused'])->nullable()->after('cancelled_at');
            $table->text('cancel_reason')->nullable()->after('attendance_status');
        });
    }

    public function down()
    {
        Schema::table('reserve_meetings', function (Blueprint $table) {
            $table->dropColumn(['cancelled_by', 'cancelled_at', 'attendance_status', 'cancel_reason']);
        });
    }
}
