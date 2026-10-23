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
        // 1. tmp_teachers
        Schema::create('tmp_teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('qualification');
            $table->integer('experience_years');
            $table->text('bio');
            $table->text('teaching_methodology');
            $table->decimal('hourly_rate', 10, 2);
            $table->decimal('demo_rate', 10, 2)->default(0.00);
            $table->string('intro_video_url')->nullable();
            $table->enum('verification_status', ['pending', 'under_review', 'verified', 'rejected', 'suspended'])->default('pending');
            $table->float('rating_avg', 3, 2)->default(0.00);
            $table->integer('reviews_count')->default(0);
            $table->json('languages_spoken');
            $table->json('teaching_modes');
            $table->timestamps();
        });

        // 2. tmp_teacher_verifications
        Schema::create('tmp_teacher_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('tmp_teachers')->onDelete('cascade');
            $table->string('document_type');
            $table->string('document_path');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_comments')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 3. tmp_student_profiles
        Schema::create('tmp_student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('grade_level')->nullable();
            $table->string('education_board')->nullable();
            $table->string('timezone')->default('UTC');
            $table->text('learning_goals')->nullable();
            $table->timestamps();
        });

        // 4. tmp_subjects
        Schema::create('tmp_subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('category');
            $table->timestamps();
        });

        // 5. tmp_teacher_subjects
        Schema::create('tmp_teacher_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('tmp_teachers')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('tmp_subjects')->onDelete('cascade');
            $table->string('grade_range')->nullable();
            $table->timestamps();
        });

        // 6. tmp_availabilities
        Schema::create('tmp_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('tmp_teachers')->onDelete('cascade');
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])->nullable();
            $table->date('specific_date')->nullable();
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_booked')->default(false);
            $table->timestamps();
        });

        // 7. tmp_packages
        Schema::create('tmp_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('tmp_teachers')->onDelete('cascade');
            $table->integer('total_classes');
            $table->integer('used_classes')->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->date('expiry_date');
            $table->enum('status', ['active', 'paused', 'expired', 'cancelled'])->default('active');
            $table->timestamps();
        });

        // 8. tmp_bookings
        Schema::create('tmp_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_uuid')->unique();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('tmp_teachers')->onDelete('cascade');
            $table->foreignId('package_id')->nullable()->constrained('tmp_packages')->onDelete('set null');
            $table->enum('booking_type', ['demo', 'class'])->default('class');
            $table->timestamp('scheduled_start')->nullable();
            $table->timestamp('scheduled_end')->nullable();
            $table->timestamp('lock_expires_at')->nullable();
            $table->enum('status', ['slot_held', 'pending_payment', 'confirmed', 'completed', 'cancelled', 'no_show', 'disputed'])->default('slot_held');
            $table->timestamps();
        });

        // 9. tmp_classes
        Schema::create('tmp_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('tmp_bookings')->onDelete('cascade');
            $table->string('teams_meeting_id')->nullable();
            $table->text('teams_join_url')->nullable();
            $table->string('agora_channel_name')->nullable();
            $table->text('agora_token')->nullable();
            $table->timestamp('actual_start')->nullable();
            $table->timestamp('actual_end')->nullable();
            $table->timestamps();
        });

        // 10. tmp_attendances
        Schema::create('tmp_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('tmp_classes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('role', ['teacher', 'student']);
            $table->enum('status', ['present', 'absent', 'late', 'no_show'])->default('present');
            $table->timestamp('join_time')->nullable();
            $table->timestamps();
        });

        // 11. tmp_payments
        Schema::create('tmp_payments', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_reference')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('booking_id')->nullable()->constrained('tmp_bookings')->onDelete('set null');
            $table->foreignId('package_id')->nullable()->constrained('tmp_packages')->onDelete('set null');
            $table->decimal('amount', 10, 2);
            $table->string('gateway_name');
            $table->enum('status', ['created', 'pending', 'paid', 'failed', 'refunded'])->default('created');
            $table->timestamps();
        });

        // 12. tmp_ledger_entries
        Schema::create('tmp_ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->string('ledger_uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('type', ['credit', 'debit']);
            $table->string('description');
            $table->decimal('balance_before', 10, 2);
            $table->decimal('balance_after', 10, 2);
            $table->string('source_reference');
            $table->timestamps();
        });

        // 13. tmp_escrows
        Schema::create('tmp_escrows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('tmp_bookings')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('tmp_teachers')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->decimal('platform_commission', 10, 2);
            $table->enum('status', ['held', 'released', 'refunded', 'disputed'])->default('held');
            $table->timestamps();
        });

        // 14. tmp_payouts
        Schema::create('tmp_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('tmp_teachers')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->text('bank_account_details');
            $table->enum('status', ['requested', 'approved', 'processing', 'paid', 'rejected'])->default('requested');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });

        // 15. tmp_rules
        Schema::create('tmp_rules', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value');
            $table->string('description');
            $table->integer('version')->default(1);
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tmp_rules');
        Schema::dropIfExists('tmp_payouts');
        Schema::dropIfExists('tmp_escrows');
        Schema::dropIfExists('tmp_ledger_entries');
        Schema::dropIfExists('tmp_payments');
        Schema::dropIfExists('tmp_attendances');
        Schema::dropIfExists('tmp_classes');
        Schema::dropIfExists('tmp_bookings');
        Schema::dropIfExists('tmp_packages');
        Schema::dropIfExists('tmp_availabilities');
        Schema::dropIfExists('tmp_teacher_subjects');
        Schema::dropIfExists('tmp_subjects');
        Schema::dropIfExists('tmp_student_profiles');
        Schema::dropIfExists('tmp_teacher_verifications');
        Schema::dropIfExists('tmp_teachers');
    }
};
