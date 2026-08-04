# Scholarborg Change Log Registry

All codebase modifications, deployment steps, and upgrades are recorded here chronologically.

---

### [2026-08-05 00:35] Git Repository Setup & Initial Commit
* **Setup & Configurations:**
  - Created root-level `.gitignore` to exclude the 135MB theme builder zip file from version control.
  - Initialized git remote pointing to `https://github.com/Jyoti-ranjan-Sahoo-45/scholarborg.git`.
  - Staged and committed all other project files (including `.env` files and `config.json` per request).
* **Modified & New Files:**
  - [.gitignore](file:///d:/scholarborg/.gitignore) [NEW]

---


### [2026-08-01 16:30] Chat Connection System (Facebook-Style Direct Chat & Call Gating)
* **Database & Role Migrations:**
  - Created migration `2026_08_01_100000_create_chat_connections_table.php` establishing `chat_connections` table (`sender_id`, `receiver_id`, `status` [pending, accepted, declined, blocked], `created_at`, `updated_at`).
  - Fixed a critical pre-existing malformed settings JSON entry in `setting_translations` (name='features') that had unquoted keys and caused all system feature settings (including Agora keys) to fail parsing. Replaced it with fully valid quoted JSON.
* **Backend Connection Gating:**
  - Created `ChatConnection.php` model (disabled automatic Carbon timestamps to prevent exceptions on bigInteger Unix columns).
  - Created `ChatConnectionController.php` implementing standard Facebook-style requests (send request, accept/decline request, remove connection, search users).
  - Modified `PublicChatController.php` to include `startDirect` route and gate conversation initialization on connection status (admins bypass).
  - Modified `AudioCallController.php` to replace the old UrbanPro reservation gating with connection status check (admins bypass).
* **Frontend Connections Page & Chat Integration:**
  - Created `connections/index.blade.php` page featuring a user search input, incoming pending requests section (Accept/Decline), outgoing pending requests, and connections grid with direct chat/call triggers.
  - Modified `public_chat/lists.blade.php` to add a "Find People" link to the chat sidebar.
  - Modified `public_chat/messages.blade.php` to show/hide the Call button dynamically based on connection status.
  - Modified `app/Mixins/Panel/SidebarItems.php` to add a permanent "Connections" item to the user panel sidebar.
* **Modified & New Files:**
  - [2026_08_01_100000_create_chat_connections_table.php](file:///d:/scholarborg/lms/database/migrations/2026_08_01_100000_create_chat_connections_table.php) [NEW]
  - [ChatConnection.php](file:///d:/scholarborg/lms/app/Models/ChatConnection.php) [NEW]
  - [ChatConnectionController.php](file:///d:/scholarborg/lms/app/Http/Controllers/Panel/ChatConnectionController.php) [NEW]
  - [PublicChatController.php](file:///d:/scholarborg/lms/app/Http/Controllers/Panel/PublicChatController.php)
  - [AudioCallController.php](file:///d:/scholarborg/lms/app/Http/Controllers/Panel/AudioCallController.php)
  - [SidebarItems.php](file:///d:/scholarborg/lms/app/Mixins/Panel/SidebarItems.php)
  - [panel.php](file:///d:/scholarborg/lms/routes/panel.php)
  - [lists.blade.php](file:///d:/scholarborg/lms/resources/views/design_1/panel/public_chat/lists.blade.php)
  - [messages.blade.php](file:///d:/scholarborg/lms/resources/views/design_1/panel/public_chat/messages.blade.php)
  - [index.blade.php (connections)](file:///d:/scholarborg/lms/resources/views/design_1/panel/connections/index.blade.php) [NEW]

---

### [2026-08-01 14:45] VoIP Audio Calling & OneDrive Auditing System (UrbanPro permission model)
* **Database & Role Migrations:**
  - Created migration `2026_08_01_000000_create_call_logs_table.php` establishing `call_logs` table (`caller_id`, `receiver_id`, `booking_id`, `channel_name`, `status`, `started_at`, `ended_at`, `duration`, `agora_resource_id`, `agora_sid`, `recording_file`, `audit_status`, `audit_notes`, `audited_by`).
* **Backend Calling Logic:**
  - Created `CallLog.php` model.
  - Created `AudioCallController.php` implementing UrbanPro permission filters (Timezone/Night Lock, scheduled meeting 30-minute buffer window, and student-teacher active course/booking relationship check).
* **Agora Recording & Artisan Background Upload:**
  - Configured Agora RTC token building for numerical UIDs.
  - Implemented composite audio recording orchestration (Acquiring resources, starting/stopping Cloud Recording, S3 compatibility target setup for Cloudflare R2).
  - Created `UploadCallRecording.php` command. Invoked asynchronously on hangup. Merges TS slices from Cloudflare R2, uploads to OneDrive, creates anonymous sharing link, and deletes temporary files from R2/local storage.
* **Frontend UI & scripts:**
  - Created `call_overlay.blade.php` and `audio-call.js` (Web Audio API ringtone synthesizer, Agora stream management, muting).
  - Included calling overlay globally in `panel.blade.php`.
  - Added "Audio Call" action button to student reservations and teacher requests.
* **Admin Auditing Panel:**
  - Created `CallAuditController.php` and `audit.blade.php` for call log listing, playing OneDrive recordings, and submitting reviews.
  - Integrated "Call Audits" link into the admin sidebar menu under the "Education" section.
* **Modified & New Files:**
  - [2026_08_01_000000_create_call_logs_table.php](file:///d:/scholarborg/lms/database/migrations/2026_08_01_000000_create_call_logs_table.php) [NEW]
  - [CallLog.php](file:///d:/scholarborg/lms/app/Models/CallLog.php) [NEW]
  - [UploadCallRecording.php](file:///d:/scholarborg/lms/app/Console/Commands/UploadCallRecording.php) [NEW]
  - [AudioCallController.php](file:///d:/scholarborg/lms/app/Http/Controllers/Panel/AudioCallController.php) [NEW]
  - [CallAuditController.php](file:///d:/scholarborg/lms/app/Http/Controllers/Admin/CallAuditController.php) [NEW]
  - [RouteServiceProvider.php](file:///d:/scholarborg/lms/app/Providers/RouteServiceProvider.php)
  - [panel.php](file:///d:/scholarborg/lms/routes/panel.php)
  - [call_overlay.blade.php](file:///d:/scholarborg/lms/resources/views/design_1/panel/includes/call_overlay.blade.php) [NEW]
  - [panel.blade.php](file:///d:/scholarborg/lms/resources/views/design_1/panel/layouts/panel.blade.php)
  - [audio-call.js](file:///d:/scholarborg/lms/public/assets/default/js/panel/audio-call.js) [NEW]
  - [audit.blade.php](file:///d:/scholarborg/lms/resources/views/admin/calls/audit.blade.php) [NEW]
  - [education.blade.php (admin sidebar)](file:///d:/scholarborg/lms/resources/views/admin/includes/sidebar/education.blade.php)
  - [table_items.blade.php (student reservation)](file:///d:/scholarborg/lms/resources/views/design_1/panel/meeting/reservation/table_items.blade.php)
  - [table_items.blade.php (teacher requests)](file:///d:/scholarborg/lms/resources/views/design_1/panel/meeting/requests/table_items.blade.php)

---

### [2026-07-31 02:30] Parent Dashboard & Student View Toggle Integration
* **Database & Role Migrations:**
  - Created migration `2026_07_31_000001_create_parent_students_table.php` establishing `parent_students` pivot table (`parent_id`, `student_id`, `access_code`, `status`, `created_at`) and added `student_code` unique column to `users` table.
  - Automatically seeded the `parent` role into `roles` and `role_translations` tables.
* **Models & Eloquent Relationships:**
  - Created `ParentStudent.php` model.
  - Updated `User.php` with `isParent()`, `children()`, `parents()`, `getStudentCode()` (generates unique `SCH-xxxxx` access codes), and `getActiveProxyStudent()`.
  - Updated `Role.php` model to include static `$parent = 'parent';` and `getParentRoleId()`.
* **Controllers & Business Logic:**
  - Created `ParentDashboardController.php` handling multi-child switching, course completion, quiz scores, assignment history, attendance metrics, and `toggleStudentView()` / `exitStudentView()`.
  - Created `ParentChildrenController.php` for linking children by Access Code or Email and unlinking. Automatically posts an activity note to the Parent's Zoho CRM lead record via `ZohoCrmService::addLeadNote()`.
  - Updated `DashboardController.php` to handle Parent role routing and Student View proxy mode.
* **Views & UI Components:**
  - Created `design_1/panel/parent/dashboard/index.blade.php` (Parent Dashboard).
  - Created `design_1/panel/parent/children/index.blade.php` (Child linking & Access Code management).
  - Created `design_1/panel/includes/student_view_banner.blade.php` (Sticky top banner when viewing panel as student).
  - Added Student Access Code badge to student headers in `header.blade.php`.
  - Updated `SidebarItems.php` to include Parent Dashboard & My Children links in the sidebar menu.
* **Modified & New Files:**
  - [2026_07_31_000001_create_parent_students_table.php](file:///d:/scholarborg/lms/database/migrations/2026_07_31_000001_create_parent_students_table.php) [NEW]
  - [ParentStudent.php](file:///d:/scholarborg/lms/app/Models/ParentStudent.php) [NEW]
  - [ParentDashboardController.php](file:///d:/scholarborg/lms/app/Http/Controllers/Panel/ParentDashboardController.php) [NEW]
  - [ParentChildrenController.php](file:///d:/scholarborg/lms/app/Http/Controllers/Panel/ParentChildrenController.php) [NEW]
  - [User.php](file:///d:/scholarborg/lms/app/User.php)
  - [Role.php](file:///d:/scholarborg/lms/app/Models/Role.php)
  - [DashboardController.php](file:///d:/scholarborg/lms/app/Http/Controllers/Panel/DashboardController.php)
  - [panel.php](file:///d:/scholarborg/lms/routes/panel.php)
  - [SidebarItems.php](file:///d:/scholarborg/lms/app/Mixins/Panel/SidebarItems.php)
  - [parent/dashboard/index.blade.php](file:///d:/scholarborg/lms/resources/views/design_1/panel/parent/dashboard/index.blade.php) [NEW]
  - [parent/children/index.blade.php](file:///d:/scholarborg/lms/resources/views/design_1/panel/parent/children/index.blade.php) [NEW]
  - [student_view_banner.blade.php](file:///d:/scholarborg/lms/resources/views/design_1/panel/includes/student_view_banner.blade.php) [NEW]
  - [header.blade.php](file:///d:/scholarborg/lms/resources/views/design_1/panel/includes/header.blade.php)

---

### [2026-07-31 01:50] Payment Gateway Integration: Razorpay
* **Database Updates:** Set `currencies = '["USD","EUR","INR"]'` for Razorpay in the `payment_channels` table to support INR domestic payments alongside USD/EUR.
* **Payment Gateway Driver:** Refactored the `Channel.php` driver for Razorpay to:
  - Implement a fallback inline self-submitting HTML payment request script.
  - Implement backend verification matching payment currency and payment amount (paise conversion) against the local order to prevent amount-tampering and payment bypass vulnerabilities.
  - Implement a safe payment capture verification flow. It only calls Razorpay's `capture()` API if the status is `'authorized'`. If the status is already `'captured'`, it directly proceeds as paid, preventing duplicate capture exceptions.
  - Query the order by ID without restricting to `user_id` to prevent session mismatch errors.
* **Payment Channel Settings:** Appended `'Razorpay'` to the `$gatewayIgnoreRedirect` array in `PaymentChannel.php` to prevent standard redirect handler execution and support the custom self-submitting script properly.
* **Modified Files:**
  - [PaymentChannel.php](file:///d:/scholarborg/lms/app/Models/PaymentChannel.php)
  - [Channel.php (Razorpay Driver)](file:///d:/scholarborg/lms/app/PaymentChannels/Drivers/Razorpay/Channel.php)

---

### [2026-07-29 18:38] Deploy Scholarb Logo and Favicon
* **Settings Updated:** Modified the `general` setting row in `setting_translations` to set `logo`, `dark_mode_logo` to `/assets/default/img/scholarblogo.png` and `fav_icon` to `/assets/default/img/favicon.png` on locale `en`.
* **Assets Uploaded:**
  - `public/assets/default/img/scholarblogo.png` (originally using `scholarbrectangularversionlogo.png`, later smart-cropped to remove white padding, improving the aspect ratio from 1.5:1 to 2.69:1 and reducing size from 2MB to 607KB to display larger in the header)
  - `public/assets/default/img/favicon.png` (using `scholarblogo.png`)
* **Laravel Cache Cleared:** Executed artisan `cache:clear`, `view:clear`, and `config:clear` to ensure the logo setting modification propagates immediately.
* **Modified/New Files:**
  - [lms/public/assets/default/img/scholarblogo.png](file:///d:/scholarborg/lms/public/assets/default/img/scholarblogo.png) [NEW]
  - [lms/public/assets/default/img/favicon.png](file:///d:/scholarborg/lms/public/assets/default/img/favicon.png) [NEW]
  - [update_logo.py](file:///d:/scholarborg/update_logo.py) [NEW]

### [2026-07-25] Initial Workspace Deployment & Setup

#### 🚀 Deployments & Relocations
* **Rocket LMS deployed on Root Domain:** Replaced the legacy CI-based Academy LMS at `/home/u181357068/domains/scholarb.org/public_html/` with the customized Rocket LMS version.
* **CRM Preserved:** Kept ERPGo CRM running completely untouched under `/home/u181357068/domains/scholarb.org/erpgo/` (accessible at `https://scholarb.org/crm`).
* **Route Exemption (.htaccess):** Patched the Laravel root `.htaccess` to bypass `/crm` requests so they route directly to the CRM directory instead of the LMS public directory.

#### 🗄️ Database Migrations
* Cleaned `u181357068_Eduction2` database by dropping all 58 legacy tables.
* Exported the working Rocket LMS database (`u181357068_Lms1`) and imported all 326 tables into `u181357068_Eduction2`.
* Reset `activated_domain` values to `NULL` in the CRM `software_licenses` table.

#### 🔑 License Keys Generation
* Registered active 36-character license keys in the CRM for clean activation:
  - Rocket LMS Plugins Bundle: `11111111-1111-1111-1111-111111111111`
  - Rocket LMS Theme Builder: `22222222-2222-2222-2222-222222222222`
  - Rocket LMS Mobile App: `33333333-3333-3333-3333-333333333333`
  - Rocket LMS Main Script: `44444444-4444-4444-4444-444444444444`

#### 🛠️ Local Workspace Automation
* Created the local `scholarborg/` folder with codebases for both `lms/` and `crm/` downloaded from production.
* Added `config.json` defining all DB & SSH credentials.
* Added `deploy.py` to sync local code changes to production using SFTP.
* Added `.agents/AGENTS.md` to define behavior rules for AI coding assistants.

---

### [2026-07-25 18:40] Automated Microsoft Teams Integration with Auto-Recording
* **Feature Added:** Integrated Microsoft Teams for automated live class creation using the Microsoft Graph API.
* **Database & Models:** Registered `microsoft_teams` in `Session::$sessionApis`.
* **Azure Credentials Settings:** Added inputs for `Client ID`, `Client Secret`, `Tenant ID`, and `Fallback Email` to the Admin Panel (Features settings).
* **Automated Meeting Creation:** Implemented the `MicrosoftTeams` helper class, retrieving OAuth tokens in the background (Client Credentials flow) and calling MS Graph API (`POST /users/{email}/onlineMeetings`) to dynamically create meetings with `"recordAutomatically": true` (auto-recording enabled).
* **UI & Validation:** Patched panel and admin session controllers to validate and invoke Teams API, hiding manual password/secret fields appropriately via JS and Blade templates.
* **Modified Files:**
  - [Session.php](file:///d:/scholarborg/lms/app/Models/Session.php)
  - [update.php](file:///d:/scholarborg/lms/lang/en/update.php)
  - [features.blade.php](file:///d:/scholarborg/lms/resources/views/admin/settings/general/features.blade.php)
  - [MicrosoftTeams.php](file:///d:/scholarborg/lms/app/Sessions/MicrosoftTeams.php) [NEW]
  - [SessionController.php (Panel)](file:///d:/scholarborg/lms/app/Http/Controllers/Panel/SessionController.php)
  - [SessionController.php (Admin)](file:///d:/scholarborg/lms/app/Http/Controllers/Admin/SessionController.php)
  - [session.blade.php (Panel view)](file:///d:/scholarborg/lms/resources/views/design_1/panel/webinars/create/includes/accordions/session.blade.php)
  - [session.blade.php (Admin view)](file:///d:/scholarborg/lms/resources/views/admin/webinars/create_includes/accordions/session.blade.php)
  - [create_webinar.js](file:///d:/scholarborg/lms/resources/js/design_1/panel/create_webinar.js)
  - [create_webinar.min.js](file:///d:/scholarborg/lms/public/assets/design_1/js/panel/create_webinar.min.js)
  - [webinar.js](file:///d:/scholarborg/lms/resources/js/admin/parts/webinar.js)
  - [webinar.min.js](file:///d:/scholarborg/lms/public/assets/admin/js/parts/webinar.min.js)

---

### [2026-07-25 20:50] Fix SMTP Email crash & Webinars Panel 404 Route redirect
* **SMTP Port Type Cast:** Cast `MAIL_PORT` value to an integer in [mail.php](file:///d:/scholarborg/lms/config/mail.php) to prevent PHP `TypeError` during SMTP client transport construction in PHP 8.x.
* **Email Credentials Configured:** Set active Google SMTP configurations directly inside the production server `.env` file and cleared configuration cache.
* **Webinar 404 Route Redirect:** Appended fallback redirect pattern for `/panel/webinars/{any?}` to `/panel/courses/{any?}` in [panel.php](file:///d:/scholarborg/lms/routes/panel.php) to route legacy/static links without throwing 404 errors.

---

### [2026-07-25 23:05] Prevent 1970 Epoch Start Date Display
* **Start Date Fix:** Added `!empty($course->start_date)` condition check inside [course_specifications.blade.php](file:///d:/scholarborg/lms/resources/views/design_1/web/courses/show/includes/rightSide/course_specifications.blade.php) under Course Specifications, preventing formatting of `null`/`0` timestamps which would print timezone-offset Unix epoch date (`1 Jan 1970 | 05:30`).

---

### [2026-07-26 14:25] Robust Microsoft Teams Integration Fixes (Guest Lobby & Error Handling)
* **Always Use Fallback Email**: Skips the instructor's personal email (since instructors are platform-only users and do not exist in Azure AD) and always schedules meetings under the configured `fallback_email` (`admin@scholarb.org`).
* **Lobby Bypass & Guest Presenters**: Configured Microsoft Graph API payload with `'allowedPresenters' => 'everyone'` and `'lobbyBypassSettings' => ['scope' => 'everyone', 'isPlayExitNotification' => false]`. This allows instructors using personal emails (e.g. Gmail/Yahoo) and students to bypass the lobby automatically and share screens.
* **Detailed Exception Handling**: Refactored `MicrosoftTeams::makeMeeting()` to throw exceptions with descriptive OAuth and Graph API error details.
* **No Lost Form Inputs**: Modified Panel and Admin `SessionController` to catch detailed exceptions in a `try-catch` block and return them as `zoom_error_msg` with a `422` validation error. This prevents silent database failures and preserves all user inputs in the creation modal.
* **Modified Files:**
  - [MicrosoftTeams.php](file:///d:/scholarborg/lms/app/Sessions/MicrosoftTeams.php)
  - [SessionController.php (Panel)](file:///d:/scholarborg/lms/app/Http/Controllers/Panel/SessionController.php)
  - [SessionController.php (Admin)](file:///d:/scholarborg/lms/app/Http/Controllers/Admin/SessionController.php)

---

### [2026-07-27] Book a Demo Feature & Admin Meeting Management

#### 🎯 Feature: Book a Demo
* **DB Schema**: Added `allow_demo TINYINT(1) DEFAULT 0` to `meetings` table. Added `is_demo TINYINT(1) DEFAULT 0` to `reserve_meetings` table. Applied directly on production via SSH.
* **Admin Settings**: Added "Maximum Demo Bookings Limit per User" (`demo_booking_max_limit`) numeric input to `Settings > General > Features`. Admin can set the limit globally (default: 5).
* **Instructor Toggle**: Added "Enable Demo Booking" switch to instructor's Meeting Settings (`basic_settings.blade.php`). When ON, saves `allow_demo=1` on the `meetings` row.
* **Tutor Profile**: Added a prominent green **"Book a Free Demo"** button on the tutor public profile sidebar, only visible when the instructor has `allow_demo=1`.
* **Booking Flow**: The booking form (`step_2.blade.php`) now passes `is_demo=1` as a hidden input. The Web `MeetingController::reserve()` method:
  - Reads `demo_booking_max_limit` from admin features settings.
  - Validates that the student has not already booked the maximum number of demos.
  - If limit is exceeded, returns a clear error message.
  - On success, marks the reservation as `is_demo=1` in `reserve_meetings`.

#### 🛠️ Admin Meeting Management
* **Routes**: Added 5 custom admin routes in `custom_admin.php`: create, store, edit, update, and get-times (AJAX).
* **Controller Methods**: Added `create()`, `store()`, `edit()`, `update()`, `getTimes()` to `AppointmentsController.php`. Admins can:
  - **Manually book** a meeting (select student, instructor, date, time slot, meeting type, status, join link, and toggle demo).
  - **Reschedule / Edit** any existing reservation (change teacher, student, date, slot, meeting type, status, join link, demo toggle).
  - **Filter** appointments by "Demo Bookings Only" via the new sort option.
* **Admin Appointments List**: Added Demo/Regular badge column, "🎯 Demo Bookings Only" filter option, "Edit / Reschedule" action per row, and "Create Reservation" button.
* **Views Created**: `admin/appointments/create.blade.php` and `admin/appointments/edit.blade.php` with AJAX instructor-to-time-slot loading.

#### 📦 Modified Files (all deployed 2026-07-27 ~13:56 UTC)
  - [AppointmentsController.php (Admin)](file:///d:/scholarborg/lms/app/Http/Controllers/Admin/AppointmentsController.php)
  - [MeetingController.php (Panel)](file:///d:/scholarborg/lms/app/Http/Controllers/Panel/MeetingController.php)
  - [MeetingController.php (Web)](file:///d:/scholarborg/lms/app/Http/Controllers/Web/MeetingController.php)
  - [features.blade.php](file:///d:/scholarborg/lms/resources/views/admin/settings/general/features.blade.php)
  - [basic_settings.blade.php](file:///d:/scholarborg/lms/resources/views/design_1/panel/meeting/settings/tabs/basic_settings.blade.php)
  - [step_2.blade.php](file:///d:/scholarborg/lms/resources/views/design_1/web/users/meeting/steps/step_2.blade.php)
  - [left_side.blade.php](file:///d:/scholarborg/lms/resources/views/design_1/web/users/profile/includes/left_side.blade.php)
  - [lists.blade.php (Admin Appointments)](file:///d:/scholarborg/lms/resources/views/admin/appointments/lists.blade.php)
  - [create.blade.php (NEW)](file:///d:/scholarborg/lms/resources/views/admin/appointments/create.blade.php)
  - [edit.blade.php (NEW)](file:///d:/scholarborg/lms/resources/views/admin/appointments/edit.blade.php)
  - [custom_admin.php](file:///d:/scholarborg/lms/routes/custom_admin.php)

---

### [2026-07-27] Tutor Finder — "Offers Demo Booking" Filter Toggle

* **Filter Toggle UI**: Added a new "🎯 Offers Demo Booking" toggle switch in the Tutor Finder filter bar (`top_filters.blade.php`), matching the exact same style as the existing "Available for Meetings", "Free Meetings", and "Discount" toggles.
* **Backend Filter Logic**: Added `$hasDemoMeetings` read from request inside `handleFilters()` in `InstructorFinderController.php`. Added private method `handleHasDemoMeetings()` that filters the instructors query to only include users whose `meetings.allow_demo = 1` and `disabled = 0`.
* **Deployed**: 2026-07-27 ~14:08 UTC

#### 📦 Modified Files
  - [InstructorFinderController.php](file:///d:/scholarborg/lms/app/Http/Controllers/Web/InstructorFinderController.php)
  - [top_filters.blade.php](file:///d:/scholarborg/lms/resources/views/design_1/web/instructor_finder/lists/top_filters.blade.php)

---

### [2026-07-27] Hotfix — Basic Settings Tab Image 404
* **Fix**: Changed the file extension of the illustration image in `basic_settings.blade.php` from `.svg` to `.png` to resolve the `404 Not Found` error.
* **Deployed**: 2026-07-27 ~19:33 UTC

---

### [2026-07-27] Hotfix — Allow Demo Checkbox Persistence
* **Fix**: Updated `app/Http/Controllers/Panel/MeetingController.php` to use `!empty($data['allow_demo'])` and `!empty($data['enable_meeting_packages'])` checks for boolean values instead of checking against `'on'`. This resolves the issue where checkboxes were not saving properly on form submission.
* **Deployed**: 2026-07-27 ~19:38 UTC

---

### [2026-07-27] Database Schema Migration Executed
* **Fix**: Verified and ran the direct `ALTER TABLE` commands to add `meetings.allow_demo` and `reserve_meetings.is_demo` directly to the production database, resolving the issue where the settings failed to save due to the missing database columns on the production server.
* **Executed**: 2026-07-27 ~19:43 UTC

---

### [2026-07-27] Hotfix — Free Demo Reservation Payment Bypass
* **Fix**: Updated `app/Http/Controllers/Web/MeetingController.php` to bypass paid reservation logic (adding to cart/redirecting to checkout) if the request is a demo booking (`is_demo = 1`). Also modified `getMeetingAmount()` to calculate and display the charge as `$0.00 / Free` on the frontend for demo bookings.
* **Deployed**: 2026-07-27 ~19:56 UTC

---

### [2026-07-27] Hotfix — Preserve is_demo Query Parameter across Booking Steps
* **Fix**: Added a hidden `is_demo` parameter to the form in `step_1.blade.php`. Because step 1 uses a GET form, the browser discarded the `is_demo` query parameter during step-to-step redirection. This hidden input ensures the parameter is passed to step 2. Also updated the `Back` button in `step_2.blade.php` to preserve the `is_demo` parameter when navigating back.
* **Deployed**: 2026-07-27 ~20:01 UTC

---

### [2026-07-27] Hotfix — Javascript Path Parameter Preservation
* **Fix**: Updated `profile_reserve_meeting.js` and the compiled `profile_reserve_meeting.min.js` file. The Javascript handler for `.js-proceed-to-booking-btn` (which processes transition from Step 1 to Step 2) was manually constructing the redirect query string using only `date` and `time` parameters. Modified it to read and append `is_demo` from the form payload, ensuring the flag is correctly passed on in the redirect path.
* **Deployed**: 2026-07-27 ~20:05 UTC

---

### [2026-07-27] Feature — Microsoft Teams Automatic Meeting Creation for Reservations
* **Feature**: Added a "Microsoft Teams" option to the "Create Session" modal. Tutors no longer have to manually paste external join links. Choosing Teams creates a meeting via Microsoft Graph API and saves the link to the reservation.
* **Deployed**: 2026-07-27 ~20:33 UTC

---

### [2026-07-27] Feature — Reservation and Start Meeting Email Notifications
* **Meeting Reservation Notification**: Added triggers to `handleFreeMeetingReservation()` in `MeetingController.php` (Web). When a student registers a free demo/meeting booking, the controller now sends the standard `new_appointment` email notifications and templates to both the student and the instructor.
* **Join/Start Meeting Notification**: Updated `join()` in `ReserveMeetingController.php` (Panel). When an instructor joins/starts a live meeting, an email is automatically sent to the student notifying them that the tutor has joined and started the session, providing a direct link to join the meeting. Implemented a 15-minute cache throttle to prevent repeat email alerts on page refreshes.
* **Deployed**: 2026-07-27 ~20:45 UTC

---

### [2026-07-28] Feature — Power Calendar (FullCalendar.js Integration)

* **FullCalendar.js Integration**: Replaced the legacy Persian Date Picker with a complete, modern FullCalendar.js implementation for a fully interactive monthly/weekly/daily schedule layout.
* **Role-Based Views**:
  - **Instructors**: View student bookings, view multiple sessions per day without overwrites, mark attendance, reschedule sessions directly, approve/reject student rescheduling requests, and manually schedule a class (recurring/individual) for any student.
  - **Students**: View active bookings, click to join active Teams meetings, request a reschedule by sending date/time/note parameters directly to the instructor.
  - **Admins**: Mighty calendar visibility with all-mighty override permissions (cancel, reschedule, observe, and manage all schedules).
* **Database Updates**: Created the `reschedule_requests` table and added `cancelled_by`, `cancelled_at`, `attendance_status`, and `cancel_reason` columns to the `reserve_meetings` table.
* **API Endpoints**: Added endpoints for JSON feed, student autocomplete search, scheduling class, detail fetching, canceling, rescheduling, requesting reschedule, approving/rejecting reschedule, marking attendance, and resending invitations.
* **Deployed**: 2026-07-28 ~03:33 Local (03:33 UTC)

#### 📦 Modified/New Files
  - [EventsCalendarController.php](file:///d:/scholarborg/lms/app/Http/Controllers/Panel/EventsCalendarController.php)
  - [RescheduleRequest.php (NEW)](file:///d:/scholarborg/lms/app/Models/RescheduleRequest.php)
  - [2026_07_28_000001_create_reschedule_requests_table.php (NEW)](file:///d:/scholarborg/lms/database/migrations/2026_07_28_000001_create_reschedule_requests_table.php)
  - [2026_07_28_000002_add_cancelled_and_attendance_to_reserve_meetings_table.php (NEW)](file:///d:/scholarborg/lms/database/migrations/2026_07_28_000002_add_cancelled_and_attendance_to_reserve_meetings_table.php)
  - [panel.php](file:///d:/scholarborg/lms/routes/panel.php)
  - [index.blade.php](file:///d:/scholarborg/lms/resources/views/design_1/panel/events_calender/index.blade.php)
  - [power_calendar.js (NEW)](file:///d:/scholarborg/lms/public/assets/design_1/js/panel/power_calendar.js)

---

### [2026-07-28] Feature — Power Calendar Multi-Student Support

* **Multi-Student Class Scheduling**: Updated the "Schedule a Class" modal to support selecting multiple students simultaneously.
* **Student Chips (UI)**: Selected students are displayed as color-coded tags/chips under the search input with a red "✕" remove icon.
* **Shared Teams Meeting Link (Backend)**: When scheduling a class for multiple students:
  - Generates a single Microsoft Teams meeting link for the group session.
  - Creates individual `reserve_meetings` records for each student linked to that shared Teams meeting.
  - Sends immediate Microsoft Teams invitations and email notifications to all added students.
* **Deployed**: 2026-07-28 ~03:44 Local (03:44 UTC)

#### 📦 Modified Files
  - [EventsCalendarController.php](file:///d:/scholarborg/lms/app/Http/Controllers/Panel/EventsCalendarController.php)
  - [index.blade.php](file:///d:/scholarborg/lms/resources/views/design_1/panel/events_calender/index.blade.php)
  - [power_calendar.js](file:///d:/scholarborg/lms/public/assets/design_1/js/panel/power_calendar.js)

---

### [2026-07-28] Security — Restricted Student Calendar Search

* **Restricted Lookup Scope**: Instructors can no longer search or schedule classes for random platform students.
* **Relationship Filtering**: The search query restricts results to only:
  - Students who bought any courses/webinars created by the instructor.
  - Students who bought any bundles created by the instructor.
  - Students who have previously booked/reserved any meeting slots with the instructor.
* **All-Mighty Admin Bypass**: Platform administrators retain full capability to search and schedule classes for any student across the entire platform.
* **Deployed**: 2026-07-28 ~03:46 Local (03:46 UTC)

#### 📦 Modified Files
  - [EventsCalendarController.php](file:///d:/scholarborg/lms/app/Http/Controllers/Panel/EventsCalendarController.php)

---

### [2026-07-28] Hotfix — Modal Close Button (X) Styling Override

* **Design Fix**: Removed the default bordered styling from the modal-header close buttons which was causing them to look like broken rectangular boxes.
* **Layout styling**: Aligned the '✕' character cleanly to the top-right of the modal box with a subtle grey color `#9ca3af` and hover effect.
* **Deployed**: 2026-07-28 ~03:49 Local (03:49 UTC)

#### 📦 Modified Files
  - [index.blade.php](file:///d:/scholarborg/lms/resources/views/design_1/panel/events_calender/index.blade.php)

---

### [2026-07-28 17:27] Competitor Analysis Report & Flow Diagrams Created
* **Analysis Report:** Created a comprehensive competitor analysis and strategic road map comparing Scholarb LMS vs. UrbanPro.
* **Flow Diagrams:** Integrated Mermaid flowcharts for student acquisition/lead routing comparisons and classroom/meeting automation lifecycles.
* **Modified Files:**
  - [report.md](file:///d:/scholarborg/report.md) [NEW]

---

### [2026-07-29 19:42] UX/UI Landing Page Revamp & SaveMyExams Reference Matching
* **Homepage Layout Revamp:** Completely redesigned the LMS homepage (`savemyexams_home.blade.php`) to match the premium, clean flexgrid UX/UI design of *SaveMyExams*. Implemented the exact Light Mode default aesthetic (light-grey background, white cards, dark slate typography) with automatic dark-mode support overrides. Added structured Hero, Trustpilot rating badge, "Why It Works" section with inline SVGs/subtitles/emerald quote badges, Subject category pills, Featured Webinar listings (via `FeatureWebinar`), "Expert Tutors" list, and Testimonial grids.
* **Database & Query Fixes:**
  - Patched `HomeController.php` to fetch categories using `Category::getCategories()` instead of `whereNull('parent_id')` to avoid SQL execution errors.
  - Refactored webinars query to fetch from the `FeatureWebinar` model with a fallback to active webinars, since the `webinars` table does not have an `is_featured` column.
* **Header and Contrast Adjustments:** Added explicit CSS flexbox positioning and layout overrides under a cache-busted stylesheet link (`savemyexams.css?v={{ time() }}`). Fixed text and icon color variables to use `var(--sme-text-light)` and `var(--sme-text-muted)` to ensure high contrast/legibility in both light and dark modes.
* **Targeted Hot-Deployment Script:** Created `force_deploy_revamp.py` to target and upload only the modified files, preventing Hostinger SFTP connection drops caused by scanning 18,000+ framework files.
* **Modified/New Files:**
  - [HomeController.php](file:///d:/scholarborg/lms/app/Http/Controllers/Web/HomeController.php)
  - [savemyexams_home.blade.php](file:///d:/scholarborg/lms/resources/views/design_1/web/home/savemyexams_home.blade.php) [NEW]
  - [savemyexams.css](file:///d:/scholarborg/lms/public/assets/default/css/savemyexams.css) [NEW]
  - [force_deploy_revamp.py](file:///C:/Users/plmpl/.gemini/antigravity-ide/brain/cb5110bb-3a7b-4ebf-8ea6-56995d8f00a3/scratch/force_deploy_revamp.py) [NEW]

---

### [2026-07-29 20:20] Revert Homepage UX/UI Revamp
* **Homepage Reversion:** Reverted the home view layout back to the original layout (`design_1.web.home.index`) at the request of the user.
* **Deleted Custom Files:**
  - Removed custom template file `savemyexams_home.blade.php` locally and from remote production.
  - Removed custom stylesheet file `savemyexams.css` locally and from remote production.
* **Restored Controllers:** Restored `HomeController.php` from local backup checkpoint `deploy_20260729_191833` to return the original legacy landing page variables and view.
* **Laravel Cache Cleared:** Executed artisan `cache:clear`, `view:clear`, `route:clear`, and `config:clear` on the remote server to propagate changes.
* **Modified/Deleted Files:**
  - [HomeController.php](file:///d:/scholarborg/lms/app/Http/Controllers/Web/HomeController.php)
  - [savemyexams_home.blade.php](file:///d:/scholarborg/lms/resources/views/design_1/web/home/savemyexams_home.blade.php) [DELETE]
  - [savemyexams.css](file:///d:/scholarborg/lms/public/assets/default/css/savemyexams.css) [DELETE]

---

### [2026-07-30 01:15] Zoho CRM & Zoho Forms Integration (Free Email OTP Flow)
* **Webhook & Lead Capture:** Created `/api/zoho/forms-webhook` to receive incoming Zoho Forms submissions, save the lead requirements locally in the `leads` database, generate a 6-digit OTP code, and email it to the user.
* **OTP Verification & Redirection:** Created `/zoho/verify-lead` GET/POST endpoints and a custom view (`verify_lead.blade.php`) to collect the verification code. Upon successful verification, the lead status is marked `verified` locally and sent to Zoho CRM via API.
* **Zoho CRM API Client:** Built `ZohoCrmService.php` to handle OAuth 2.0 token refresh requests, search leads in Zoho CRM by email/phone, and update their verification status.
* **Admin OAuth Connection:** Added `/admin/settings/zoho/connect` and `/admin/settings/zoho/callback` routes and settings handlers inside `SettingsController.php` to automate refresh token retrieval.
* **Database Updates:** Created and ran migration `2026_07_30_000001_create_leads_table` adding the leads table and user Zoho ID fields.
* **Targeted Deployment Scripts:** Created and ran `deploy_migration.py` and `deploy_leads_files.py` to deploy specific files without SFTP timeouts.
* **Modified/New Files:**
  - [2026_07_30_000001_create_leads_table.php](file:///d:/scholarborg/lms/database/migrations/2026_07_30_000001_create_leads_table.php) [NEW]
  - [Lead.php](file:///d:/scholarborg/lms/app/Models/Lead.php) [NEW]
  - [ZohoCrmService.php](file:///d:/scholarborg/lms/app/Services/ZohoCrmService.php) [NEW]
  - [ZohoWebhookController.php](file:///d:/scholarborg/lms/app/Http/Controllers/Web/ZohoWebhookController.php) [NEW]
  - [ZohoVerificationController.php](file:///d:/scholarborg/lms/app/Http/Controllers/Web/ZohoVerificationController.php) [NEW]
  - [verify_lead.blade.php](file:///d:/scholarborg/lms/resources/views/design_1/web/zoho/verify_lead.blade.php) [NEW]
  - [VerifyCsrfToken.php](file:///d:/scholarborg/lms/app/Http/Middleware/VerifyCsrfToken.php)
  - [web.php](file:///d:/scholarborg/lms/routes/web.php)
  - [SettingsController.php](file:///d:/scholarborg/lms/app/Http/Controllers/Admin/SettingsController.php)

---

### [2026-07-30 01:40] Zoho CRM Silent Registration Sync & User Behavior Tracker
* **Registration capturing**: Implemented a Laravel event listener `SyncRegistrationToZoho` mapped to the default `Registered` event. When a student or teacher/instructor registers on the LMS, they are automatically registered silently as a Lead in Zoho CRM, and their local profile is updated with their `zoho_lead_id`.
* **Silent User Behavior tracking**: Implemented a global middleware `TrackUserActivity` that logs search queries and catalog page views (courses, bundles, products, blogs).
* **Device Brand & Geolocation Lookup**: Integrated the pre-installed `jenssegers/agent` package to detect specific mobile brand details (e.g. *iPhone*, *Samsung*) and a cached geolocation lookup of client IP addresses via `ip-api.com` to resolve their City, State, and Country.
* **Zoho CRM Sync (Notes Timeline)**: Real-time user activity is synced to Zoho CRM as Notes on their Lead profile. For guest users, their pre-registration session activity is retroactively synced as Notes once they complete registration or verify their Zoho Forms inquiry.
* **Database Updates**: Created and executed migration `2026_07_30_000002_create_user_activities_table.php` to define the user activity logging table.
* **Modified/New Files**:
  - [2026_07_30_000002_create_user_activities_table.php](file:///d:/scholarborg/lms/database/migrations/2026_07_30_000002_create_user_activities_table.php) [NEW]
  - [SyncRegistrationToZoho.php](file:///d:/scholarborg/lms/app/Listeners/SyncRegistrationToZoho.php) [NEW]
  - [TrackUserActivity.php](file:///d:/scholarborg/lms/app/Http/Middleware/TrackUserActivity.php) [NEW]
  - [EventServiceProvider.php](file:///d:/scholarborg/lms/app/Providers/EventServiceProvider.php)
  - [Kernel.php](file:///d:/scholarborg/lms/app/Http/Kernel.php)
  - [ZohoCrmService.php](file:///d:/scholarborg/lms/app/Services/ZohoCrmService.php)
  - [ZohoVerificationController.php](file:///d:/scholarborg/lms/app/Http/Controllers/Web/ZohoVerificationController.php)

---

### [2026-07-30 03:20] Theme & Landing Page Builder Addon Deployment
* **Theme Builder ZIP Extraction:** Extracted code files from `Installation/theme_builder.zip` locally and deployed them remotely.
* **Theme Builder Assets Extraction:** Extracted assets from `Demo Data/assets.zip` (127 MB) locally and remotely under `public/store/landing_builder`.
* **Database Updates:** Reset 9 theme/landing builder tables remotely and imported `Demo_Themes_database.sql` to populate pre-built theme templates.
* **License Provider Registration:** Modified and deployed `config/app.php` to uncomment and load `ThemeBuilderLicenseServiceProvider`. Registered the active license key `22222222-2222-2222-2222-222222222222` under `scholarb.org` domain in the CRM database.
* **Modified/New Files:**
  - [app/Enums/LandingBuilderComponentsNames.php](file:///d:/scholarborg/lms/app/Enums/LandingBuilderComponentsNames.php) [MODIFY]
  - [app/Http/Controllers/LandingBuilder/LandingBuilderComponentController.php](file:///d:/scholarborg/lms/app/Http/Controllers/LandingBuilder/LandingBuilderComponentController.php) [MODIFY]
  - [app/Models/ThemeHeaderFooter.php](file:///d:/scholarborg/lms/app/Models/ThemeHeaderFooter.php) [MODIFY]
  - [config/app.php](file:///d:/scholarborg/lms/config/app.php) [MODIFY]
  - Views under `resources/views/admin/landing_builder/` [MODIFY]
  - Views under `resources/views/admin/theme/` [MODIFY]
  - Views under `resources/views/landingBuilder/` [NEW]
  - Assets under `public/store/landing_builder/` [NEW]
