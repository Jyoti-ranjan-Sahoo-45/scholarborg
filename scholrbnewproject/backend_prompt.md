# System Generation Prompt & Backend Blueprint
## Project: Education Teacher Marketplace & Learning Platform Backend (Laravel)

This document is a comprehensive technical blueprint and code generation prompt for building the Laravel-based backend of the Education Teacher Marketplace. 

The backend runs on the existing CRM database (`u181357068_Serp1`) to leverage the existing infrastructure, while coexisting cleanly by prefixing all new tables with `tmp_`.

---

# 🚀 SYSTEM GENERATION PROMPT (LLM INSTRUCTION CAP)
> **INSTRUCTIONS FOR THE GENERATION AGENT:**
> You are an expert Laravel senior developer. Your task is to implement the backend codebase for the Education Teacher Marketplace & Learning Platform according to the technical blueprint below. 
> 
> Follow these implementation standards:
> 1. Use the existing database config (`u181357068_Serp1`) and leverage the existing `users` table for authentication, identifying roles via Spatie roles.
> 2. Ensure all new tables are prefixed with `tmp_` to prevent clashes with existing ERPGo CRM tables.
> 3. Implement full transactional safety with strict error handling, database transactions for checkout and payouts, and idempotent webhook endpoints.
> 4. Generate all code using strict PHP typing, Laravel 10/11 standards, and resource-controller patterns.
> 5. Create clear Eloquent relationships as defined in Section 2.
> 6. Integrate with Microsoft Teams (OAuth + Calendar), Agora API, and Zoho CRM using the extracted keys.

---

# 1. Database Schema & Laravel Migrations

The database tables are prefixed with `tmp_` to isolate them from ERPGo.

```
                           +----------------------+
                           |        users         |  (Existing CRM Table)
                           +----------+-----------+
                                      |
             +------------------------+------------------------+
             | 1:1                                             | 1:1
    +--------v----------+                             +--------v----------+
    |   tmp_teachers    |                             |tmp_student_profiles
    +--------+----------+                             +--------+----------+
             | 1:N                                             | 1:N
    +--------v----------+                             +--------v----------+
    |tmp_availabilities |                             | tmp_requirements  |
    +-------------------+                             +-------------------+
             |                                                 |
             +------------------------+------------------------+
                                      | 1:N
                             +--------v----------+
                             |   tmp_bookings    | (Demos or Paid Classes)
                             +--------+----------+
                                      | 1:N
                             +--------v----------+
                             |    tmp_classes    | (Scheduled live events)
                             +--------+----------+
                                      | 1:N
                             +--------v----------+
                             |  tmp_attendances  |
                             +-------------------+
```

### Migration: `tmp_teachers`
Stores teacher-specific profiles linked to the CRM `users` table.
```php
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
    $table->json('languages_spoken'); // array of language codes
    $table->json('teaching_modes'); // ['one-to-one', 'group']
    $table->timestamps();
});
```

### Migration: `tmp_teacher_verifications`
Tracks teacher documents and audit verification decisions.
```php
Schema::create('tmp_teacher_verifications', function (Blueprint $table) {
    $table->id();
    $table->foreignId('teacher_id')->constrained('tmp_teachers')->onDelete('cascade');
    $table->string('document_type'); // e.g., 'ID', 'Degree', 'Certificate'
    $table->string('document_path');
    $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
    $table->text('admin_comments')->nullable();
    $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamps();
});
```

### Migration: `tmp_student_profiles`
Stores parent/student profiles linked to the CRM `users` table.
```php
Schema::create('tmp_student_profiles', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('parent_id')->nullable()->constrained('users')->onDelete('set null'); // Self-referencing users table for parent link
    $table->string('grade_level')->nullable();
    $table->string('education_board')->nullable(); // e.g., CBSE, ICSE, GCSE
    $table->string('timezone').default('UTC');
    $table->text('learning_goals')->nullable();
    $table->timestamps();
});
```

### Migration: `tmp_subjects` & `tmp_teacher_subjects`
Pivot for matching teachers to subjects.
```php
Schema::create('tmp_subjects', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique();
    $table->string('category'); // e.g., 'Mathematics', 'Science', 'Language'
    $table->timestamps();
});

Schema::create('tmp_teacher_subjects', function (Blueprint $table) {
    $table->id();
    $table->foreignId('teacher_id')->constrained('tmp_teachers')->onDelete('cascade');
    $table->foreignId('subject_id')->constrained('tmp_subjects')->onDelete('cascade');
    $table->string('grade_range'); // JSON or string indicating applicable grades
    $table->timestamps();
});
```

### Migration: `tmp_availabilities`
Tracks teacher slots. Can be recurring or specific dates.
```php
Schema::create('tmp_availabilities', function (Blueprint $table) {
    $table->id();
    $table->foreignId('teacher_id')->constrained('tmp_teachers')->onDelete('cascade');
    $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])->nullable(); // For recurring slots
    $table->date('specific_date')->nullable(); // For one-off availability
    $table->time('start_time');
    $table->time('end_time');
    $table->boolean('is_booked')->default(false);
    $table->timestamps();
});
```

### Migration: `tmp_packages`
Lifecycle of class packages purchased by students.
```php
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
```

### Migration: `tmp_bookings`
Handles slot lock and booking creation for both demos and paid classes.
```php
Schema::create('tmp_bookings', function (Blueprint $table) {
    $table->id();
    $table->string('booking_uuid')->unique();
    $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('teacher_id')->constrained('tmp_teachers')->onDelete('cascade');
    $table->foreignId('package_id')->nullable()->constrained('tmp_packages')->onDelete('set null'); // null means demo booking
    $table->enum('booking_type', ['demo', 'class'])->default('class');
    $table->timestamp('scheduled_start');
    $table->timestamp('scheduled_end');
    $table->timestamp('lock_expires_at')->nullable(); // For temporary slot holding during checkout
    $table->enum('status', ['slot_held', 'pending_payment', 'confirmed', 'completed', 'cancelled', 'no_show', 'disputed'])->default('slot_held');
    $table->timestamps();
});
```

### Migration: `tmp_classes`
Connects scheduled bookings to Live Microsoft Teams or Agora sessions.
```php
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
```

### Migration: `tmp_attendances`
Tracks presence of both student and teacher.
```php
Schema::create('tmp_attendances', function (Blueprint $table) {
    $table->id();
    $table->foreignId('class_id')->constrained('tmp_classes')->onDelete('cascade');
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->enum('role', ['teacher', 'student']);
    $table->enum('status', ['present', 'absent', 'late', 'no_show'])->default('present');
    $table->timestamp('join_time')->nullable();
    $table->timestamps();
});
```

### Migration: `tmp_payments` & `tmp_ledger_entries`
Monetary state and accounting ledger.
```php
Schema::create('tmp_payments', function (Blueprint $table) {
    $table->id();
    $table->string('transaction_reference')->unique();
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('booking_id')->nullable()->constrained('tmp_bookings')->onDelete('set null');
    $table->foreignId('package_id')->nullable()->constrained('tmp_packages')->onDelete('set null');
    $table->decimal('amount', 10, 2);
    $table->string('gateway_name'); // e.g. 'stripe', 'paypal'
    $table->enum('status', ['created', 'pending', 'paid', 'failed', 'refunded'])->default('created');
    $table->timestamps();
});

Schema::create('tmp_ledger_entries', function (Blueprint $table) {
    $table->id();
    $table->string('ledger_uuid')->unique();
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // The affected ledger owner (e.g. Teacher)
    $table->decimal('amount', 10, 2);
    $table->enum('type', ['credit', 'debit']);
    $table->string('description');
    $table->decimal('balance_before', 10, 2);
    $table->decimal('balance_after', 10, 2);
    $table->string('source_reference'); // Payment ID, Booking ID, or Payout ID
    $table->timestamps();
});
```

### Migration: `tmp_escrows` & `tmp_payouts`
Holds funds in escrow until class completion, and manages teacher withdrawals.
```php
Schema::create('tmp_escrows', function (Blueprint $table) {
    $table->id();
    $table->foreignId('booking_id')->constrained('tmp_bookings')->onDelete('cascade');
    $table->foreignId('teacher_id')->constrained('tmp_teachers')->onDelete('cascade');
    $table->decimal('amount', 10, 2);
    $table->decimal('platform_commission', 10, 2);
    $table->enum('status', ['held', 'released', 'refunded', 'disputed'])->default('held');
    $table->timestamps();
});

Schema::create('tmp_payouts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('teacher_id')->constrained('tmp_teachers')->onDelete('cascade');
    $table->decimal('amount', 10, 2);
    $table->string('bank_account_details'); // JSON metadata
    $table->enum('status', ['requested', 'approved', 'processing', 'paid', 'rejected'])->default('requested');
    $table->text('admin_notes')->nullable();
    $table->timestamps();
});
```

### Migration: `tmp_rules`
Supports dynamic platform setup.
```php
Schema::create('tmp_rules', function (Blueprint $table) {
    $table->id();
    $table->string('key')->unique();
    $table->json('value');
    $table->string('description');
    $table->integer('version')->default(1);
    $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamps();
});
```

---

# 2. Domain Eloquent Relationships (Model Definitions)

### Model: `User`
Reuses CRM model. Integrate Spatie roles and connect to new domains.
```php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable {
    use HasRoles;

    public function teacherProfile() {
        return $this->hasOne(TeacherProfile::class, 'user_id');
    }

    public function studentProfile() {
        return $this->hasOne(StudentProfile::class, 'user_id');
    }

    public function parentChildren() {
        return $this->hasMany(StudentProfile::class, 'parent_id');
    }
}
```

### Model: `TeacherProfile` (`tmp_teachers`)
```php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TeacherProfile extends Model {
    protected $table = 'tmp_teachers';
    protected $casts = [
        'languages_spoken' => 'array',
        'teaching_modes' => 'array'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function verifications() {
        return $this->hasMany(TeacherVerification::class, 'teacher_id');
    }

    public function subjects() {
        return $this->belongsToMany(Subject::class, 'tmp_teacher_subjects', 'teacher_id', 'subject_id');
    }

    public function availabilities() {
        return $this->hasMany(Availability::class, 'teacher_id');
    }

    public function bookings() {
        return $this->hasMany(Booking::class, 'teacher_id');
    }
}
```

### Model: `Booking` (`tmp_bookings`)
```php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model {
    protected $table = 'tmp_bookings';
    protected $casts = [
        'scheduled_start' => 'datetime',
        'scheduled_end' => 'datetime'
    ];

    public function student() {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function teacher() {
        return $this->belongsTo(TeacherProfile::class, 'teacher_id');
    }

    public function class() {
        return $this->hasOne(ClassSession::class, 'booking_id');
    }

    public function escrow() {
        return $this->hasOne(Escrow::class, 'booking_id');
    }
}
```

---

# 3. State Machines & Transition Rules

All transactions must run through strict status transitions:

```
Teacher Profile:
[Draft] -> [Pending] -> [Under Review] -> [Verified] -> [Published]
                             |               |
                             v               v
                         [Rejected]     [Suspended]

Demo & Booking:
[Slot Held] -> [Pending Payment] -> [Confirmed] -> [Scheduled] -> [Live] -> [Completed]
                                         |
                                         +-> [Cancelled] / [No-Show] / [Disputed]

Payment:
[Created] -> [Pending] -> [Paid] -> [Verified] -> [Escrowed] -> [Released]
                            |
                            v
                        [Failed] / [Refunded]
```

### State Guards Implementation Guideline
Controllers must check prior states using Laravel state change verification:
```php
public function confirmBooking(Booking $booking) {
    if (!in_array($booking->status, ['slot_held', 'pending_payment'])) {
        throw new \Exception("Invalid status transition from " . $booking->status);
    }
    $booking->update(['status' => 'confirmed']);
}
```

---

# 4. API Endpoints Structure (`routes/api.php`)

All API routes must be organized and secure:

```php
Route::prefix('v1/marketplace')->group(function () {
    
    // GUEST ROUTES
    Route::get('/teachers', [MarketplaceController::class, 'index']);
    Route::get('/teachers/{id}', [MarketplaceController::class, 'show']);
    Route::get('/subjects', [MarketplaceController::class, 'subjects']);

    // AUTHENTICATED ROUTES
    Route::middleware('auth:sanctum')->group(function () {
        
        // Student endpoints
        Route::prefix('student')->middleware('role:student')->group(function () {
            Route::post('/requirements', [StudentController::class, 'updateRequirements']);
            Route::post('/bookings/lock', [BookingController::class, 'lockSlot']);
            Route::post('/bookings/confirm', [BookingController::class, 'checkout']);
            Route::get('/calendar', [StudentController::class, 'calendar']);
            Route::get('/packages', [StudentController::class, 'packages']);
        });

        // Teacher endpoints
        Route::prefix('teacher')->middleware('role:teacher')->group(function () {
            Route::post('/onboarding', [TeacherController::class, 'onboard']);
            Route::post('/availability', [TeacherController::class, 'updateAvailability']);
            Route::get('/earnings', [TeacherController::class, 'earnings']);
            Route::post('/payouts/request', [PayoutController::class, 'requestPayout']);
            Route::get('/calendar', [TeacherController::class, 'calendar']);
        });

        // Parent endpoints
        Route::prefix('parent')->middleware('role:parent')->group(function () {
            Route::get('/children', [ParentController::class, 'listChildren']);
            Route::post('/children/add', [ParentController::class, 'addChild']);
            Route::get('/children/{id}/activity', [ParentController::class, 'childActivity']);
        });

        // Sales endpoints
        Route::prefix('sales')->middleware('role:sales_executive|sales_manager')->group(function () {
            Route::get('/leads', [SalesController::class, 'leads']);
            Route::post('/leads/{id}/followup', [SalesController::class, 'addFollowup']);
            Route::post('/leads/{id}/score', [SalesController::class, 'updateScore']);
        });

        // Admin endpoints
        Route::prefix('admin')->middleware('role:admin|super_admin')->group(function () {
            Route::post('/teachers/{id}/verify', [AdminController::class, 'verifyTeacher']);
            Route::post('/escrows/{id}/release', [AdminController::class, 'releaseEscrow']);
            Route::post('/payouts/{id}/process', [AdminController::class, 'processPayout']);
            Route::post('/rules', [AdminController::class, 'updateRule']);
        });
    });
});
```

---

# 5. Third-Party API Integrations Blueprint

Implement these integrations using credentials inside `credentials/keys_and_ssh.json`:

### 5.1 Microsoft Teams OAuth & Meeting API
Authenticate and create live meetings programmatically.
- **Goal:** Authorize teacher Teams account, request refresh/access token, create online meeting using Microsoft Graph API client.
- **Endpoint:** `POST https://graph.microsoft.com/v1.0/me/onlineMeetings`
- **Payload template:**
  ```json
  {
    "startDateTime": "2026-08-24T14:00:00Z",
    "endDateTime": "2026-08-24T15:00:00Z",
    "subject": "Teacher Marketplace Class Session"
  }
  ```

### 5.2 Agora RTC Token Generator
Generate RTC stream join keys for custom web video lessons.
- **Logic:** Generate dynamic channel access tokens using `agora_app_id` and `agora_app_certificate` on class generation.
- **Implementation:** Implement `AgoraDynamicTokenBuilder` class using Agora's HMAC-SHA256 signature verification.

### 5.3 Zoho CRM Contact & Lead Push
Expose web hooks to sync leads.
- **Trigger:** On Student signup/lead scoring events, sync profile name, email, score, and preferred teachers to Zoho CRM contact list using Zoho REST API client.

---

# 6. Escrow & Ledger Integrity Protocol

To protect the financial state of the platform:
1. **Never trust local variable calculations:** Read actual configuration rules from the `tmp_rules` database before applying gateway and platform commission cuts.
2. **Database Transactions:** Payouts and bookings must always run inside DB transactions:
   ```php
   DB::transaction(function () use ($booking) {
       // Debit student, credit escrow, write source ledger entries
   });
   ```
3. **Immutability:** Financial ledger records (`tmp_ledger_entries`) must never be updated. To adjust an entry, create a matching debit/credit entry with a source description.
