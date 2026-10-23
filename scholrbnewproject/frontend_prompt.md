# Frontend Architecture Blueprint & Developer System Prompt
## Education Teacher Marketplace & Learning Platform - React SPA

This document serves as the implementation specification and generation prompt for the React Frontend codebase of the Education Teacher Marketplace.

---

## 1. Codebase Structure & Component-Specific CSS

The React codebase must enforce clean separation of concerns. Every component and page must reside in its own folder, containing its JSX logic and isolated CSS style sheet. Avoid global styling collisions.

```text
frontend/
├── public/
├── src/
│   ├── assets/
│   ├── components/
│   │   ├── Navbar/
│   │   │   ├── Navbar.jsx
│   │   │   └── Navbar.css
│   │   ├── Hero/
│   │   │   ├── Hero.jsx
│   │   │   └── Hero.css
│   │   ├── Testimonials/
│   │   │   ├── Testimonials.jsx
│   │   │   └── Testimonials.css
│   │   └── Footer/
│   │       ├── Footer.jsx
│   │       └── Footer.css
│   ├── pages/
│   │   ├── Landing/
│   │   │   ├── Landing.jsx
│   │   │   └── Landing.css
│   │   ├── Login/
│   │   │   ├── Login.jsx
│   │   │   └── Login.css
│   │   ├── Register/
│   │   │   ├── Register.jsx
│   │   │   └── Register.css
│   │   ├── Dashboard/
│   │   │   ├── StudentDashboard.jsx
│   │   │   ├── TeacherDashboard.jsx
│   │   │   ├── ParentDashboard.jsx
│   │   │   ├── AdminDashboard.jsx
│   │   │   └── Dashboard.css
│   │   ├── Calendar/
│   │   │   ├── CalendarPage.jsx
│   │   │   └── CalendarPage.css
│   │   └── Classroom/
│   │       ├── ClassroomPage.jsx
│   │       └── ClassroomPage.css
│   ├── services/
│   │   └── apiClient.js
│   ├── store/
│   │   └── useAuthStore.js
│   ├── App.jsx
│   ├── App.css
│   ├── main.jsx
│   └── index.css
```

---

## 2. Multi-Mode Landing Page Switcher

The public landing page (`/`) implements a dynamic mode switcher representing the three primary audiences of the platform. Activating a mode shifts the visual theme (via CSS class changes and CSS variable bindings), the copywriting, the search parameters, and the testimonials.

### 2.1 Switcher Interface Design
* **Header Selector:** A segmented toggle bar centered on the page: `[ For Students ] [ For Teachers ] [ For Parents ]`.
* **State Management:** React state variable `landingMode` (`'student' | 'teacher' | 'parent'`).

```text
+-----------------------------------------------------------+
| Segmented Toggle Selector                                 |
|                                                           |
|             [ Student ]   [ Teacher ]   [ Parent ]        |
|                                                           |
|             Active theme adjusts color tokens dynamically |
+-----------------------------------------------------------+
```

### 2.2 Mode Configurations & Styling Tokens

#### A. Student Mode (`student`)
* **Core Concept:** Active learning, matching, booking, and dynamic goals.
* **Color Styling:** Cyberpunk Blue & Bright Cyan theme.
  - Primary Accent: `#06B6D4` (Cyan 500)
  - Secondary Accent: `#3B82F6` (Blue 500)
  - Background Gradient: `linear-gradient(135deg, #0F172A 0%, #1E1B4B 100%)` (Sleek deep indigo/blue).
* **Hero Content:** "Match with the Perfect Teacher & Accelerate Your Learning."
* **Action Button:** "Find Your Tutor" (launches dynamic filters panel).

#### B. Teacher Mode (`teacher`)
* **Core Concept:** Work independence, scheduled sessions ownership, and transparent ledger earnings.
* **Color Styling:** Professional Emerald Green & Mint theme.
  - Primary Accent: `#10B981` (Emerald 500)
  - Secondary Accent: `#34D399` (Emerald 400)
  - Background Gradient: `linear-gradient(135deg, #022C22 0%, #0F172A 100%)` (Deep forest emerald/charcoal).
* **Hero Content:** "Build Your Teaching Business. No Lead Purchasing Required."
* **Action Button:** "Apply to Teach" (redirects to signup role page).

#### C. Parent Mode (`parent`)
* **Core Concept:** Child learning activity monitoring, safety, and invoice billing.
* **Color Styling:** Safe warm Indigo & Amber/Orange theme.
  - Primary Accent: `#4F46E5` (Indigo 600)
  - Secondary Accent: `#F59E0B` (Amber 500)
  - Background Gradient: `linear-gradient(135deg, #1E1B4B 0%, #180828 100%)` (Deep royal indigo/dark plum).
* **Hero Content:** "Track Your Child's Academic Journey and Attendance in Real-Time."
* **Action Button:** "Register Parent Account" (redirects to signup role page).

---

## 3. Component Visual Specifications

### 3.1 Global Navigation (`Navbar`)
* **Design:** Fixed glassmorphism header (`backdrop-filter: blur(12px)`), semi-transparent borders.
* **Responsive Layout:** Flexbox row layout collapsing into an slide-out drawer on mobile viewports.
* **Action Links:** Login, Register, Mode Quick-Toggle.

### 3.2 Dynamic Search & Directory (`Hero`)
* **Design:** Config-driven search input box. Contains dynamic filters:
  - Subject Selector (fetches `/api/v1/marketplace/subjects` to populate category lists).
  - Price Range Slider (bound to parent components to trigger query filters).
  - Search keyword input.

### 3.3 Dynamic Testimonials
* **Design:** Horizontal sliding cards carousel.
* **Mode Content:**
  - `student`: Positive feedback on trial lock bookings, Microsoft Teams integrations, and learning materials.
  - `teacher`: Feedback on automated escrow release payouts, flexible availability setups, and direct ledger withdrawals.
  - `parent`: Feedback on children calendar overlays, linked dashboard tools, and class attendance sheets.

---

## 4. Role-Based Dashboards & Calendar UI

Every secure dashboard includes a calendar widget. There is also a dedicated calendar viewpage (`/calendar`) to schedule all user actions.

### 4.1 Dashboard Calendar Layouts
* **Student Dashboard:** Displays this week's active lessons. Click to launch classroom. Includes a 10-minute slot lock holding timer countdown widget.
* **Teacher Onboarding Dashboard:** Step-by-step wizard capturing biography details, certificates, hourly pricing ranges, and scheduling configurations. Displays verified/pending status.
* **Parent Dashboard:** Dropdown picker to select child student user ID. Renders parent combined calendar sheets.
* **Admin Dashboard:** Displays global system rule configurations, leads CRM cards (Cold, Warm, Hot lists), and verifying action lists.

### 4.2 Dedicated Calendar Page (`/calendar`)
* Renders full-screen scheduler. 
* Double-clicking empty blocks opens the slot scheduler.
* **Drag-and-Drop:** Supported for rescheduling (runs validation boundaries checks before submitting reschedule request to `/bookings/checkout`).
* **Conflict Wizard:** Highlighting overlapping bookings or non-verified teacher blocks.

---

## 5. Live Virtual Classroom (`/classroom/{id}`)

* Split viewport layout:
  - Left panel: Renders the Agora streaming video frame with video mute, audio mute, and classroom screen-sharing buttons.
  - Right panel: Real-time text chat widget and blackboard/learning materials files library tab.
* **Teams Overlay Card:** Displays the join URL and fallback meeting details if Teams is selected.

---

## 6. HTTP API Client Integration Specifications

React utilizes `Axios` connecting to the Laravel REST APIs. Interceptors attach headers and capture error parameters:

```javascript
import axios from 'axios';

const apiClient = axios.create({
    baseURL: 'https://scholarb.org/marketplace/api/v1/marketplace',
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    }
});

// Attach Token Interceptor
apiClient.interceptors.request.use(config => {
    const token = localStorage.getItem('auth_token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// Response Handling Interceptor
apiClient.interceptors.response.use(
    response => response,
    error => {
        if (error.response && error.response.status === 401) {
            // Trigger logout / redirect to login page
            localStorage.removeItem('auth_token');
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);

export default apiClient;
```

### 6.1 Unified Client-to-Backend Routing Map

| Frontend Action | Method | Path | Request Body | Expected Success | Expected Errors |
| :--- | :---: | :--- | :--- | :---: | :---: |
| Account Register | `POST` | `/register` | `{name, email, password, type}` | `201 Created` | `422 (Duplicate Email)` |
| User Login | `POST` | `/login` | `{email, password}` | `200 OK` | `401 (Wrong Password)` |
| Load Teachers | `GET` | `/teachers` | URL Query Filters | `200 OK` | `500` |
| Lock Demo/Class Slot | `POST` | `/bookings/lock` | `{teacher_id, availability_slot_id, booking_type}` | `201 Created` | `422 (Slot missing), 400 (Locked)` |
| Payment checkout | `POST` | `/bookings/checkout` | `{booking_uuid, payment_method}` | `200 OK` | `400 (Lock expired)` |
| Onboard Profile | `POST` | `/teacher/onboarding` | `{qualification, experience_years, bio, hourly_rate}` | `200 OK` | `422 (Invalid values)` |
| Add Child account | `POST` | `/parent/children/add` | `{child_email}` | `200 OK` | `400 (Not student role)` |
| Fetch CRM Leads | `GET` | `/sales/leads` | Pagination/Query | `200 OK` | `403 (Unauthorized)` |
| Log Lead followup | `POST` | `/sales/leads/{id}/followup` | `{log_type, remark}` | `201 Created` | `422 (Missing fields)` |
| Manual Escrow Release | `POST` | `/admin/bookings/{id}/release` | `{}` | `200 OK` | `400 (Escrow already released)` |
