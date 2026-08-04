<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->index();
            $table->string('phone')->nullable();
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            $table->string('zoho_lead_id')->nullable()->index();
            $table->enum('verification_status', ['pending', 'verified'])->default('pending');
            $table->string('otp_code')->nullable();
            $table->bigInteger('otp_expires_at')->nullable();
            $table->bigInteger('created_at');
            $table->bigInteger('updated_at')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'zoho_lead_id')) {
                $table->string('zoho_lead_id')->nullable()->index();
            }
            if (!Schema::hasColumn('users', 'zoho_contact_id')) {
                $table->string('zoho_contact_id')->nullable()->index();
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'zoho_lead_id')) {
                $table->dropColumn('zoho_lead_id');
            }
            if (Schema::hasColumn('users', 'zoho_contact_id')) {
                $table->dropColumn('zoho_contact_id');
            }
        });

        Schema::dropIfExists('leads');
    }
}
