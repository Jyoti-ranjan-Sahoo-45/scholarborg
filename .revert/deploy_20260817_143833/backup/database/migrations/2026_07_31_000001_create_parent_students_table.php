<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateParentStudentsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('parent_students')) {
            Schema::create('parent_students', function (Blueprint $table) {
                $table->id();
                $table->integer('parent_id')->unsigned();
                $table->integer('student_id')->unsigned();
                $table->string('access_code')->nullable();
                $table->enum('status', ['active', 'pending', 'rejected'])->default('active');
                $table->bigInteger('created_at');
                $table->bigInteger('updated_at')->nullable();

                $table->foreign('parent_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
                $table->unique(['parent_id', 'student_id']);
            });
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'student_code')) {
                $table->string('student_code')->nullable()->unique();
            }
        });

        // Ensure parent role exists in roles table
        $parentRole = DB::table('roles')->where('name', 'parent')->first();
        if (!$parentRole) {
            $roleId = DB::table('roles')->insertGetId([
                'name' => 'parent',
                'users_count' => 0,
                'is_admin' => 0,
                'created_at' => time()
            ]);

            if (Schema::hasTable('role_translations')) {
                DB::table('role_translations')->insert([
                    'role_id' => $roleId,
                    'locale' => 'en',
                    'caption' => 'Parent'
                ]);
            }
        }
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'student_code')) {
                $table->dropColumn('student_code');
            }
        });

        Schema::dropIfExists('parent_students');
    }
}
