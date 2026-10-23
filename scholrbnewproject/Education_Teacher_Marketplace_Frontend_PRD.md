# Product Requirements Document (PRD)
## Education Teacher Marketplace & Learning Platform - React Frontend

**Document Type:** Frontend Product Requirements Document (PRD)  
**Target Framework:** React (Vite-powered)  
**Status:** Approved for Scaffolding  
**Version:** 1.0  

---

## 1. System Architecture & Tech Stack

The frontend is a single-page application (SPA) built using React. It connects asynchronously to the Laravel REST API backend.

```text
+-----------------------------------------------------------+
|                   React Frontend SPA                      |
|                                                           |
|  [ Router ] ----> [ Public Pages (Visitor/Marketplace) ]  |
|     |                                                     |
|     +-----------> [ Protected Dashboards ]                |
|                      ├── Student Dashboard & Calendar     |
|                      ├── Teacher Dashboard & Calendar     |
|                      ├── Parent Dashboard & Calendar      |
|                      └── Sales & Admin Dashboard          |
|                                                           |
|  [ API Client (Axios/Zustand) ] <===> [ Laravel API ]    |
+-----------------------------------------------------------+
```

### 1.1 Technical Stack Specifications
* **Core:** React 18+ (Vite builder for fast compilation and optimized asset bundling).
* **Routing:** `react-router-dom` v6 for layout routing, guest protection guards, and role-based redirect dashboards.
* **State Management:** `Zustand` or `React Context` for global authorization tokens, active user profiles, and shopping cart states.
* **HTTP Client:** `Axios` with global request/response interceptors to attach Sanctum Bearer tokens and handle 401/403/422 responses dynamically.
* **Styling:** TailwindCSS + Vanilla CSS for premium, clean styling tokens.
* **Component Library:** HeadlessUI or Radix UI for accessible modal overlays, dropdowns, and form selections.
* **Calendar Core:** `FullCalendar` (React Wrapper) or custom interactive calendar schedules designed with HSL color parameters.
* **Media/Realtime Streaming:** Agora Web SDK integration for active classroom viewports.

---

## 2. Authentication & Route Guards

Access to endpoints is restricted by authorization middleware on the client side:

### 2.1 Router Guiding Matrix
* **Guest Routes (Public):**
  * `/login` - Redirects to corresponding active dashboard if already logged in.
  * `/register` - Support selection of Student, Teacher, and Parent accounts.
  * `/` - Guest marketplace search directory.
  * `/teachers/{id}` - Public teacher profile view.
* **Protected Routes (Requires Sanctum Token):**
  * `/student/*` - Dashboard, Calendar, Bookings, Teams/Agora meeting launcher.
  * `/teacher/*` - Dashboard, Availability schedule editor, Calendar, Earnings ledger.
  * `/parent/*` - Dashboard, Linked children selection, Attendance metrics, Calendar.
  * `/admin/*` - Leads scoring, Teacher KYC verifications, Escrow release overrides, Global system rules.

---

## 3. Universal Calendar Design & dedicated Pages

Every dashboard features a calendar widget, and a dedicated full-screen page (`/calendar`) coordinates scheduling.

```text
+-----------------------------------------------------------+
| DEDICATED CALENDAR PANEL (/calendar)                      |
|                                                           |
|  [Month/Week/Day Selector]                   [Actions]   |
|  <<  August 2026  >>                          + Add Slot  |
|                                                           |
|  +-----+-----+-----+-----+-----+-----+-----+              |
|  | Mon | Tue | Wed | Thu | Fri | Sat | Sun |              |
|  +-----+-----+-----+-----+-----+-----+-----+              |
|  | 24  | 25  | 26  | 27  | 28  | 29  | 30  |              |
|  |     |     |     |     |     |     |Demo |              |
|  +-----+-----+-----+-----+-----+-----+-----+              |
+-----------------------------------------------------------+
```

### 3.1 Role-Specific Dedicated Calendar Functions

#### A. Teacher Calendar (`/teacher/calendar`)
* **Slot Builder Tool:** Highlight time blocks to save slots as "Available" or "Repeat Weekly".
* **Buffer Enforcement:** Configuration settings to add 5, 10, or 15-minute buffers between bookings.
* **Color Schemes:**
  * **Blue:** Confirmed standard class slot.
  * **Green:** Confirmed demo slot.
  * **Yellow:** Held/Locked slot (10-minute hold pending payment).
  * **Grey:** Expired or completed class slot.

#### B. Student Calendar (`/student/calendar`)
* **Class Booking Helper:** Overlays chosen teacher availability schedules.
* **One-Click Launcher:** Action button becomes active on the current confirmed class card 10 minutes before scheduling starts, launching the class stream.
* **Rescheduling terms:** Click a class to request rescheduling. Displays policy constraints (e.g. free prior to 24 hours).

#### C. Parent Calendar (`/parent/calendar`)
* **Multi-Child Filtering:** Selectable toggles for parents with multiple linked children. Shows combined overlay calendar with individual child names.
* **Attendance Badging:** Color dots indicating class status: *Present* (Green), *Absent* (Red), *Canceled* (Grey).

#### D. Sales CRM & Admin Calendar (`/admin/calendar`)
* **Global Grid View:** Administrators can view schedules of multiple teachers simultaneously.
* **Conflict Resolvers:** Force reschedule, unlock slots, and release locks.
* **Sales Reminders:** CRM follow-ups logged for Hot Leads are overlaid on the calendar grid.

---

## 4. Dashboard Visual Specifications

### 4.1 Student Portal
* **Main Dashboard Panel:**
  * Quick Stats: Completed lessons count, active bookings count.
  * Upcoming Class card with a live countdown timer.
  * Compact Calendar widget showing this week's scheduled classes.
* **Dedicated Pages:**
  * `/student/teachers` - Public search directory.
  * `/student/bookings` - List of active bookings, invoices, and lock states.

### 4.2 Teacher Portal
* **Main Dashboard Panel:**
  * Quick Stats: Available Balance, Total Earned, Verification Status (`verified`, `pending`, `rejected`).
  * Onboarding form wizard (if status is `pending` or draft).
  * Weekly calendar slot manager.
* **Dedicated Pages:**
  * `/teacher/earnings` - Dynamic table displaying transaction ledger logs (`type`, `credit`, `debit`, `balance_after`, `description`) with a payout request form.
  * `/teacher/students` - Directory of students who have booked classes with this teacher.

### 4.3 Parent Portal
* **Main Dashboard Panel:**
  * Quick Stats: Total children linked, attendance metrics tracker.
  * Multi-Child calendar grid.
* **Dedicated Pages:**
  * `/parent/children` - Child linker page. Allows typing a child's email address to add them (uses `POST /parent/children/add`). Shows linked children list.
  * `/parent/billing` - Invoices and transaction payment logs for child bookings.

### 4.4 Sales CRM & Admin Portal
* **Main Dashboard Panel:**
  * Quick Stats: Hot Leads (Score $\ge 70$), Conversion Rate, Total Escrow Held, Pending Verifications.
  * Dynamic CRM Lead board (Columns: `Cold`, `Warm`, `Hot`).
* **Dedicated Pages:**
  * `/admin/leads` - List of leads synced from CRM. Clicking a lead opens details showing their dynamic scoring logs and an add follow-up form (WhatsApp, call notes).
  * `/admin/teachers` - Teachers list with verification controls (`Approve` / `Reject` buttons).

---

## 5. Live Virtual Classroom View (`/classroom/{class_id}`)

When joining a class session, the layout transitions into a split learning screen:

```text
+-----------------------------------------------------------+
| Live Classroom View                                       |
|                                                           |
|  [ Agora Stream Frame ]                  [ Chat & Board ] |
|  +-------------------------------+       +--------------+ |
|  |                               |       | Chat:        | |
|  |         Video Feed            |       | Student: Hi! | |
|  |                               |       |              | |
|  +-------------------------------+       +--------------+ |
|  [ Mic: On ] [ Camera: On ]                  [ Leave ]    |
+-----------------------------------------------------------+
```

* **Agora Component:** Renders the remote teacher/student video, mute controls, screen share button, and network signal strength.
* **Teams meeting overlay:** If the class uses Microsoft Teams, presents a clean "Open in Microsoft Teams" action card with meeting credentials.
* **Meeting Completion trigger:** Admin completes the class, updating status and triggering automatic escrow payout releases.

---

## 6. API Client Mapping Specs

The React client communicates with the Laravel endpoints using the following map:

| UI Event | HTTP Method | API Path | Authorization |
| :--- | :---: | :--- | :--- |
| Login Form Submission | `POST` | `/api/v1/marketplace/login` | Guest |
| Registration | `POST` | `/api/v1/marketplace/register` | Guest |
| Search Teachers Directory | `GET` | `/api/v1/marketplace/teachers` | Guest |
| Get Teacher Profile & Slots | `GET` | `/api/v1/marketplace/teachers/{id}` | Guest |
| Submit Onboarding | `POST` | `/api/v1/marketplace/teacher/onboarding` | Teacher Token |
| Save Availability Slots | `POST` | `/api/v1/marketplace/teacher/availability` | Teacher Token |
| Request Slot Lock | `POST` | `/api/v1/marketplace/bookings/lock` | Student Token |
| Complete Slot Checkout | `POST` | `/api/v1/marketplace/bookings/checkout` | Student Token |
| Generate Agora Token | `POST` | `/api/v1/marketplace/classes/{classId}/agora` | Auth Token |
| Link Parent Child | `POST` | `/api/v1/marketplace/parent/children/add` | Parent Token |
| Fetch CRM Leads | `GET` | `/api/v1/marketplace/sales/leads` | Admin/Sales Token |
| Log Lead Follow-up | `POST` | `/api/v1/marketplace/sales/leads/{id}/followup` | Admin/Sales Token |
| Release Escrow Payout | `POST` | `/api/v1/marketplace/admin/bookings/{id}/release` | Admin Token |
