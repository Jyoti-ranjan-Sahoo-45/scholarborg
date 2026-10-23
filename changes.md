# Scholarborg Change Log Registry

All codebase modifications, deployment steps, and upgrades are recorded here chronologically.

---

### [2026-10-21 16:10] Our Programmes Page Overhaul & Production Deployment
* **Page Architecture & Design ([Programmes.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/pages/Programmes.jsx) & [index.css](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/index.css)):**
  - **Hero Section:** Breadcrumb `Home > Our Programmes`, staggered GSAP 3 word-by-word reveal for `Our Programmes` and `Real Support. Real Opportunities.`, narrative explaining institutional separation, CTA buttons (`Explore Charitable Programmes` and `Paid Tutoring Services`), panoramic photography (`programmes_hero.jpg`), and cursive script `Real Support. Real Opportunities.` with continuous float micro-interaction.
  - **Institutional Separation Notice:** Prominent governance clarification stating Section 8 non-commercial status for foundation initiatives versus the separate paid tutoring marketplace.
  - **Core Programmes Grid:** 4 comprehensive visual cards featuring high-res imagery, badges, 4 verified checklist items, and direct action buttons:
    1. `Free Online Learning` (100% Free / Charitable)
    2. `Scholarships & Educational Assistance` (Merit & Need Based Aid)
    3. `Academic Support & Mentorship` (Community Mentoring)
    4. `Supplementary Tutoring Services` (Marketplace Service / Paid)
  - **"How Our Programmes Help" Pillars:** 4 impact pillars (Access to Learning, Confidence & Support, Stronger Communities, Long-Term Opportunity) with scroll-triggered stagger animation.
  - **Enrolment Roadmap & Final CTA:** 3-step streamlined access roadmap and donor/volunteer call to action.
  - **Full Mobile Responsiveness:** Stacks cleanly on mobile (`<= 900px`) and tablets, with dedicated mobile image hero container and touch-optimized card layout.
* **Production Deployment:**
  - Built bundle with `npm run build` and deployed to `scholarb.org` via `deploy_foundation.py`.
  - Verified live endpoint: `https://scholarb.org/programmes` (**HTTP 200 OK**).

---

### [2026-10-21 16:00] Fixed /our-story Route Resolution & Production Deployment
* **Root Cause of 404 on `/our-story`:**
  - The URL `/our-story` was missing from Laravel's `$foundationPaths` in `routes/web.php` on the remote server, causing requests to fall through to Rocket LMS's course/curriculum router, which returned the native LMS 404 page ("Page Not Found The page or curriculum section you are looking for has moved or...").
  - The frontend client router in [App.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/App.jsx) also did not have explicit entries for `/our-story` or `/who-we-are`.
* **Fix & Architecture ([App.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/App.jsx), [About.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/pages/About.jsx), [deploy_foundation.py](file:///c:/jyoti/scholarborg/scholarborg/deploy_foundation.py)):**
  - Registered `/our-story` and `/who-we-are` routes in React router [App.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/App.jsx) pointing to `About.jsx`.
  - Added `id="story"` and smooth anchor scrolling in [About.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/pages/About.jsx) so visiting `/our-story` or clicking "Our Story →" smoothly navigates to the story & milestones section.
  - Added smart incremental SFTP file caching in [deploy_foundation.py](file:///c:/jyoti/scholarborg/scholarborg/deploy_foundation.py) (skipping already-uploaded multi-megabyte assets to eliminate SSH timeouts).
  - Configured remote Laravel `routes/web.php` to include `/our-story` and `/who-we-are` in `$foundationPaths`.
  - Cleared Laravel route and view caches (`php artisan route:clear && php artisan view:clear`).
* **Live Verification:**
  - `https://scholarb.org/our-story`: **HTTP 200 OK**
  - `https://scholarb.org/about`: **HTTP 200 OK**
  - `https://scholarb.org/`: **HTTP 200 OK**

---

### [2026-10-21 12:45] MadeToScale Animation Implementations on Who We Are Page
* **GSAP 3 & Scroll-Triggered Reveal Implementations ([About.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/pages/About.jsx) & [index.css](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/index.css)):**
  - **MadeToScale 01 (Hero Reveal):** Word-by-word staggered reveal on main headline (`Who`, `We`, `Are`) and cyan sub-headline (`People.`, `Purpose.`, `Possibility.`) with initial blur removal and upward translation (`y: 35 -> 0`, `filter: blur(8px) -> 0`). Subtitle narrative fades up smoothly, CTA button springs into place (`back.out(1.4)`), hero image softly zooms in (`scale: 0.94 -> 1`), and the cursive script tag rotates in with a bounce before entering its continuous floating drift.
  - **Continuous Micro-Animations:**
    - Cursive script tag has gentle floating tilt animation (`anim-cursive-float`, `anim-mobile-cursive-float`).
    - Milestone nodes feature an infinite cyan beacon pulse (`anim-pulse-dot`) using `@keyframes beacon-pulse`.
    - Interactive card hover elevations with cyan border glows on purpose and leadership cards.
  - **IntersectionObserver Stagger Reveals:**
    - **Our Purpose:** 3 cards (`Our Mission`, `Our Vision`, `Our Values`) stagger in (`stagger: 0.14s`, `y: 35 -> 0`, `scale: 0.95 -> 1`).
    - **Our Identity:** 5 statutory badges cascade sequentially from left to right (`stagger: 0.09s`).
    - **Our Story:** Classroom image slides in from the left (`x: -30 -> 0`), story narrative fades up, and the milestone timeline vertical line draws down from top to bottom (`scaleY: 0 -> 1`), followed by each milestone item sliding in (`stagger: 0.16s`).
    - **Our Leadership:** 4 executive portraits and the "Join Our Team" card stagger into view with slight scale spring (`stagger: 0.1s`, `back.out(1.25)`).
* **Production Deployment:**
  - Built bundle with `npm run build` and deployed to `scholarb.org` via `deploy_foundation.py`.
  - Verified live endpoint: `https://scholarb.org/about` (**HTTP 200 OK**).

---

### [2026-10-21 12:20] Who We Are Page Mobile Responsive Architecture
* **Responsive Layout Upgrades ([About.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/pages/About.jsx) & [index.css](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/index.css)):**
  - **Hero Section:** On screens `<= 900px`, the desktop absolute background and top-right cursive script transition into a clean vertical stack. The hero photo renders in a dedicated mobile container (`about-hero-mobile-visual`) with rounded corners and subtle shadow, embedding the cursive script badge cleanly on top without text collision.
  - **Our Purpose Section:** Converted from rigid multi-column grid to responsive `about-purpose-grid` (`minmax(300px, 420px) 1fr` on desktop, stacking seamlessly to 1 column on screens `<= 900px`).
  - **Our Identity Section:** Eliminated horizontal overflow caused by `minmax(280px, 360px) 1fr`. Stacks to 1 column on mobile/tablet. Converted statutory badges container to a 2-column wrapping grid on mobile (`<= 640px`) and 1-column on narrow devices (`<= 420px`), with even borders and padding.
  - **Our Story Section:** Converted rigid 3-column grid (`minmax(280px, 340px) 1fr minmax(280px, 320px)`) into a responsive layout that stacks cleanly on screens `<= 1024px` (Photo &rarr; Narrative &rarr; Key Milestones timeline).
  - **Our Leadership Section:** Responsive grid (`about-leadership-grid`) displaying 5 columns on desktop, 3 columns on tablet (`<= 1024px`), 2 columns on mobile (`<= 900px`), and clean touch cards on small screens (`<= 420px`). The 5th "Join Our Team" card dynamically expands (`grid-column: span 2`) on 2-column mobile view.
* **Production Deployment:**
  - Built bundle with `npm run build` and deployed to `scholarb.org` via `deploy_foundation.py`.
  - Verified live endpoint: `https://scholarb.org/about` (**HTTP 200 OK**).

---

### [2026-10-21 12:05] Who We Are Page Redesign & Production Deployment
* **Mockup Alignment ([Scholarbee Foundation_ People, Purpose, Possibility.png](file:///c:/jyoti/scholarborg/Scholarbee%20Foundation_%20People,%20Purpose,%20Possibility.png)):**
  - **Hero Section:** Breadcrumb `Home > Who We Are`, bold heading `Who We Are`, cyan subheading `People. Purpose. Possibility.`, narrative, `Our Programmes →` CTA button, panoramic hero image of Indian school students smiling on school steps, and cursive script `Better Learners Brighter Futures` via Google Font `Caveat`.
  - **Our Purpose Section:** Left narrative `Education for a Brighter, More Inclusive Tomorrow` + Right 3-card grid (`Our Mission`, `Our Vision`, and `Our Values` checklist with cyan checkmarks).
  - **Our Identity Section:** Sleek Section 8 Nonprofit Organisation layout with 5 statutory badges (`Legal Status`, `CIN`, `Incorporation Date`, `Registered Office`, `Governing Members`).
  - **Our Story Section:** 3-column layout featuring classroom photo of young Indian girl writing at desk, narrative `A Journey Towards Greater Educational Opportunity`, and `KEY MILESTONES` timeline (202X milestones).
  - **Our Leadership Section:** Heading `Guided by Experience, Driven by Impact` + `View Full Leadership & Governance →` button + 5-card grid (4 leadership portraits with `To be confirmed` roles and 1 `Join Our Team` purple gradient card with `Work With Us →` button).
* **Asset Integration:**
  - Generated and placed 6 professional matching images in `frontend/src/assets/`:
    - `about_hero_students.jpg` (Hero panoramic students group)
    - `about_story_girl.jpg` (Classroom girl studying)
    - `about_leader_1.jpg`, `about_leader_2.jpg`, `about_leader_3.jpg`, `about_leader_4.jpg` (Executive portraits)
* **Production Deployment:**
  - Built bundle with `npm run build` and deployed to `scholarb.org` via `deploy_foundation.py`.
  - Verified live endpoint: `https://scholarb.org/about` (**HTTP 200 OK**).

---

### [2026-10-21 11:42] Mobile Navigation Drawer Display & Overflow Fix
* **Issue Diagnosed from User Screenshot:**
  - When opening the mobile menu on small screens, the drawer was getting squished into the 76px header height because the `<header>` element had `backdropFilter: blur(...)`, which in CSS creates a new containing block for `position: fixed` descendants.
  - As a result, the drawer did not cover the screen, causing the word "Home" to cut off and background content to bleed through.
* **Resolution in [Navbar.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/components/layout/Navbar.jsx):**
  - Moved the Mobile Drawer outside of `<header>` so that its containing block is the true viewport.
  - Set explicit `height: calc(100vh - var(--nav-height))` and solid `#050505` Jet Black background.
  - Added a body scroll lock hook (`document.body.style.overflow = 'hidden'`) while the menu is open, preventing background scroll bleed.
  - Added auto-close `onClick` handlers to all mobile drawer links.
* **Production Deployment:**
  - Built bundle and uploaded to `scholarb.org/public/foundation-assets/`.
  - Verified live bundle update on `https://scholarb.org/` (HTTP 200 OK).

---

### [2026-10-20 18:43] Production Deployment of Scholarbee Foundation & Tutoring React App to scholarb.org
* **Production Deployment Architecture:**
  - Configured Vite with `assetsDir: 'foundation-assets'` to prevent any collision with Rocket LMS's `/assets/` directory.
  - Built production bundle in `frontend/dist/` and uploaded via SFTP to `/home/u181357068/domains/scholarb.org/public_html/public/foundation-assets/`.
  - Deployed `index.html` as `public/foundation.html` (protecting Apache from DirectoryIndex priority conflicts).
  - Configured Laravel `routes/web.php` to serve `foundation.html` for root `/` and all Foundation / Tutoring paths (`/education-challenge`, `/about`, `/programmes`, `/impact`, `/governance`, `/donate`, `/volunteer`, `/careers`, `/contact`, `/tutoring`).
  - Rocket LMS native landing page preserved at `/lms-home`, Rocket LMS dashboard preserved at `/panel`, and login preserved at `/login`.
  - Cleared Laravel route and view caches via `php artisan route:clear && php artisan view:clear`.
* **Live Verification Results:**
  - `https://scholarb.org/`: **HTTP 200 OK** (Loads React Scholarbee Foundation Home)
  - `https://scholarb.org/education-challenge`: **HTTP 200 OK** (Loads The Education Challenge in India with 7 high-res images)
  - `https://scholarb.org/tutoring`: **HTTP 200 OK** (Loads Supplementary Tutoring Marketplace)
  - `https://scholarb.org/foundation-assets/edu_hero_girl-_p4y2fa8.png`: **HTTP 200 OK** (2.12 MB)
  - `https://scholarb.org/crm`: **HTTP 200 OK** (ERPGo CRM intact)
  - `https://scholarb.org/login`: **HTTP 200 OK** (Rocket LMS Auth intact)
  - `https://scholarb.org/api/development/courses`: **HTTP 200 OK** (Mobile REST API intact)

---

### [2026-10-20 18:21] Review Shuffle Continuous Infinite Loop Implementation
* **Issue Addressed:**
  - The Review Shuffle animation was halting after one card cycle due to state reconciliation and hover freeze.
* **Resolution in [ReviewShuffle.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/components/animations/ReviewShuffle.jsx):**
  - Re-architected the shuffle using a master GSAP Timeline with infinite looping (`repeat: -1`).
  - Sequenced all 3 cycles seamlessly: each top card throws off to the left with `-18°` rotation, smoothly resets underneath the stack at `zIndex: 1`, while the rear cards spring forward with `back.out(1.4)`.
  - Added an interactive "Next story &rarr;" manual fast-forward button for instant user control.
* **Build Verification:**
  - `npm.cmd run build` compiled in 256ms with 0 errors.

---

### [2026-10-20 18:15] MadeToScale Animation Implementations (GSAP & IntersectionObserver)
* **Guide Implementation (from MadeToScale — 12 Landing Page Animations Guide):**
  - **01 Hero Reveal:** Built GSAP 3 word-by-word staggered entrance (`opacity: 0 -> 1`, `y: 35px -> 0`, `blur: 8px -> 0`, `stagger: 0.08s`, `ease: power3.out`) followed by subtext, CTAs, and hero card scale-in on both [Home.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/pages/Home.jsx) and [EducationChallenge.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/pages/EducationChallenge.jsx).
  - **05 Count-up Stats:** Built [CountUpStats.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/components/animations/CountUpStats.jsx) utilizing IntersectionObserver and GSAP to count verified numbers (`1,200+` Learners, `5,000+` Sessions, `250+` Scholarships, `15+` Locations) with `toLocaleString` formatting. Runs once when entering viewport.
  - **04 Review Shuffle:** Built [ReviewShuffle.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/components/animations/ReviewShuffle.jsx) implementing a 3-card stacked deck. The top card throws off to the left with a `-18°` rotation, while background cards spring forward with `back.out(1.4)`. Includes hover-pause, verified customer badges, and 5 gold stars.
  - **08 Process Draw:** Built [ProcessDraw.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/components/animations/ProcessDraw.jsx) connecting the 5-step roadmap with an animated SVG path via `stroke-dashoffset`. Step nodes light up in electric cyan as the drawing line progresses.
  - **Reduced Motion Support:** All animations check `prefers-reduced-motion: reduce` and show final states instantly.
* **Build Verification:**
  - `npm.cmd run build` compiled in 257ms with 0 errors.

---

### [2026-10-20 18:02] Desktop Navigation Layout, Spacing & Text Wrap Fix
* **Issue Diagnosed from Screenshot:**
  - Nav links were colliding directly with the logo text ("Home" was overlapping the "Scholarbee Foundation" text).
  - Nav labels ("The Education Challenge", "Who We Are") were wrapping into two lines due to insufficient container width and competing dual CTA buttons.
* **Resolution & Enhancements in [Navbar.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/components/layout/Navbar.jsx):**
  - Expanded the navbar container max-width to `1440px` with responsive clamp padding (`0 clamp(18px, 3vw, 40px)`).
  - Refined logo sizing (`36px` icon, balanced `1.15rem` brand heading) with `flex-shrink: 0`.
  - Added strict `whiteSpace: 'nowrap'` to all navigation links to eliminate awkward line breaks.
  - Replaced the cluttered dual-button right action with the clean single pill CTA **"Support Education &rarr;"** matching the exact layout of `The Education Challenge in India.png`.
  - Adjusted responsive desktop breakpoint to `1040px` for seamless transition to mobile drawer without text crowding.
* **Build Verification:**
  - `npm.cmd run build` compiled in 228ms with 0 errors.

---

### [2026-10-20 17:55] Extracted & Integrated 7 High-Resolution Full-Size Generated Images
* **Asset Integration from Archive:**
  - Extracted `indian_school_classroom_images.zip` containing 7 original full-resolution AI-generated photos (>2MB each, uncropped).
  - Mapped each image to its respective section in [frontend/src/pages/EducationChallenge.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/pages/EducationChallenge.jsx):
    - `edu_hero_girl.png` (`dreamy_schoolgirl_in_golden_classroom_light.png` - 2.12 MB) -> Hero section
    - `edu_challenge_access.png` (`dreaming_by_the_classroom_window.png` - 2.09 MB) -> Card 1: Limited Access
    - `edu_challenge_affordability.png` (`focused_classroom_study.png` - 2.05 MB) -> Card 2: Affordability
    - `edu_challenge_support.png` (`thoughtful_student_in_fractions_class.png` - 2.01 MB) -> Card 3: Need for Additional Learning Support
    - `edu_challenge_digital.png` (`focused_learning_digital_skills_classroom.png` - 2.13 MB) -> Card 4: Digital Access
    - `edu_challenge_financial.png` (`wide_shallow_depth_of_field_photo_of_a_classroom.png` - 2.15 MB) -> Card 5: Financial Support for Continued Education
    - `edu_response_students.png` (`brighter_india_begins_in_the_classroom.png` - 2.13 MB) -> Section 4: From Challenges to Opportunities
* **Build Verification:**
  - `npm.cmd run build` compiled all 7 full-res PNG assets into `dist/` in 237ms with 0 errors.
  - Route `http://localhost:5173/education-challenge` verified healthy (HTTP 200 OK).

---

### [2026-10-20 17:15] The Education Challenge in India Page Visual Alignment & Integration
* **Visual Target Alignment:**
  - Analyzed `The Education Challenge in India.png` and rebuilt [frontend/src/pages/EducationChallenge.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/pages/EducationChallenge.jsx) matching all 4 sections, typography, and card structures.
  - Extracted and integrated 7 authentic pixel-perfect visuals into `frontend/src/assets/`:
    - `edu_hero_girl.jpg` (Hero panoramic photo of Indian schoolgirl holding pen with cursive overlay "Every Learner Deserves Opportunity")
    - `edu_challenge_access.jpg` (Card 1: Limited Access photo)
    - `edu_challenge_affordability.jpg` (Card 2: Affordability photo)
    - `edu_challenge_support.jpg` (Card 3: Need for Additional Learning Support photo)
    - `edu_challenge_digital.jpg` (Card 4: Digital Access photo)
    - `edu_challenge_financial.jpg` (Card 5: Financial Support for Continued Education photo)
    - `edu_response_students.jpg` (Bottom Response section: students smiling together over open books)
  - Updated [Navbar.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/components/layout/Navbar.jsx) with active cyan bottom-border indicator matching the reference screenshot.
* **Build & QA Verification:**
  - `npm.cmd run build` compiled in 225ms with 0 errors.
  - Route `http://localhost:5173/education-challenge` verified healthy (HTTP 200 OK).

---

### [2026-10-20 16:42] Home Page Visual Alignment & Custom Asset Integration
* **Visual Target Alignment:**
  - Analyzed `Scholarbee Foundation_ Brighter Futures Together.png` and aligned [frontend/src/pages/Home.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/pages/Home.jsx) to match every section and layout.
  - Generated and integrated 7 tailored photorealistic assets in `frontend/src/assets/`:
    - `home_hero_girl.jpg` (Hero section with glowing border & cursive overlay)
    - `challenge_boy.jpg` (Education challenge section)
    - `prog_online_learning.jpg` (Free Online Learning card)
    - `prog_scholarships.jpg` (Scholarships & Assistance card)
    - `prog_mentoring.jpg` (Academic Support & Mentoring card)
    - `prog_tutoring.jpg` (Supplementary Tutoring Services card)
    - `community_girls.jpg` (Community In Action section)
* **Build Verification:**
  - `npm.cmd run build` successfully compiled all assets into `dist/` in 230ms with 0 errors.
  - Local Vite preview server active on `http://localhost:5173/`.

---

### [2026-10-20 16:00] Complete Scholarbee Foundation & Tutoring React Frontend Implementation
* **Goal & Scope:**
  - Implemented the complete Scholarbee Foundation & Tutoring multi-page React application inside rontend/ based on the prompt pack specifications.
* **Architectural Separation:**
  - **Foundation Website:** /, /education-challenge, /about, /programmes, /programmes/free-online-learning, /programmes/scholarships, /programmes/academic-support, /impact, /governance, /donate, /volunteer, /careers, /contact, /privacy, /terms, /cookies.
  - **Supplementary Tutoring Product (/tutoring):** Interactive 3-way Role Switcher (Student, Parent, Teacher) directly below the main H1, API-powered teacher search, teacher cards, live 'Book Free Demo' modal, and HTML/CSS student dashboard preview.
* **Design System & Palette:**
  - Jet Black (#050505), Royal Amethyst (#3B0A6B), Electric Cyan (#00E5FF), and supporting dark surfaces (#08080C, #0D0D14, #11111A, #151520).
* **API Integration:**
  - Configured rontend/src/services/api.js to communicate with Rocket LMS mobile REST endpoints using x-api-key: scholarb_react_api_key_2026.
* **Build & QA Validation:**
  - 
pm run build completed with 0 errors (dist/ bundle compiled in ~345ms).
  - Preview server running on http://localhost:5173/.

---

### [2026-10-20 14:00] Complete ScholarB React Landing Page Implementation (15 Sections)
* **Goal & Scope:**
  - Implemented the complete, production-grade ScholarB public landing web application in React (Vite) consuming Rocket LMS Mobile APIs (/api/development/...), strictly styled with the brand palette (#050505 Jet Black, #00E5FF Electric Cyan, #3B0A6B Royal Amethyst).
* **Architecture & Components Built:**
  - [frontend/src/components/Navbar.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/components/Navbar.jsx): Sticky glassmorphism header with desktop links, login/demo CTAs, and mobile hamburger drawer.
  - [frontend/src/components/HeroSection.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/components/HeroSection.jsx): 2-column hero featuring in-hero role switcher directly under the headline with GSAP 3 entrance sequence.
  - [frontend/src/components/RoleSwitcher.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/components/RoleSwitcher.jsx): Sliding cyan pill switching Student, Parent, and Teacher narratives.
  - [frontend/src/components/TeacherSearch.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/components/TeacherSearch.jsx): Dynamic Class, Subject (API-connected), and Mode filter search bar.
  - [frontend/src/components/HeroVisual.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/components/HeroVisual.jsx): Dynamic portrait crossfade with micro-float benefit badges.
  - [frontend/src/components/TrustBenefits.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/components/TrustBenefits.jsx): Trust indicators strip with verified badges.
  - [frontend/src/components/StatsStrip.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/components/StatsStrip.jsx): Animated count-up statistics via IntersectionObserver.
  - [frontend/src/components/HowItWorks.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/components/HowItWorks.jsx): 4-step sequence connected by animated SVG path.
  - [frontend/src/components/SubjectGrid.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/components/SubjectGrid.jsx): Curated subjects grid with API hook and hover elevation.
  - [frontend/src/components/TeacherShowcase.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/components/TeacherShowcase.jsx): Top rated verified tutors showcase with mobile scroll-snap.
  - [frontend/src/components/WhyScholarB.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/components/WhyScholarB.jsx): Asymmetric Bento Grid highlighting personalized matching, parent visibility, and safety.
  - [frontend/src/components/ProductExperience.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/components/ProductExperience.jsx): Interactive live CSS student/parent dashboard tour.
  - [frontend/src/components/Testimonials.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/components/Testimonials.jsx): Review Shuffle animation with student, parent, and educator stories.
  - [frontend/src/components/TeacherCTA.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/components/TeacherCTA.jsx): Educator recruitment section with benefits.
  - [frontend/src/components/MobileExperience.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/components/MobileExperience.jsx): Responsive mobile app showcase with smartphone mockup.
  - [frontend/src/components/FAQSection.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/components/FAQSection.jsx): Expandable 8-question accordion.
  - [frontend/src/components/FinalCTA.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/components/FinalCTA.jsx): Conversion banner with dual CTAs.
  - [frontend/src/components/Footer.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/components/Footer.jsx): 5-column navigation footer with compliance and social links.
  - [frontend/src/components/DemoBookingModal.jsx](file:///c:/jyoti/scholarborg/scholarborg/frontend/src/components/DemoBookingModal.jsx): Request-aware booking modal connected to /api/development/demo-booking/book.
* **Build Verification:**
  - 
pm run build compiled in 221ms with 0 errors and 0 warnings.
  - Live preview running at http://localhost:5173/ (HTTP 200 OK).

---

### [2026-10-19 21:45] Cleaned 68 Obsolete Debug Scripts, Output Dumps & Old Deploy Scripts
* **Goal & Scope:**
  - Removed 68 obsolete single-use Python test scripts, SQL output dumps, debug text logs, and outdated deployment/rollback scripts from the root repository.
* **Preserved Essential Files:**
  - config.json, deploy.py, erify_ssh.py, show_crm_users.py, changes.md, README.md, lms/, and crm/.

---

### [2026-10-19 21:38] Local React Websites & Build Artifacts Deletion
* **Goal & Scope:**
  - Removed outdated and unused local React website projects and leftover compiled assets to keep the repository clean before starting the new Mobile-API-driven React architecture.
* **Deleted Local Folders & Files:**
  - scholarb-reference-ui/: Deleted previous Vite/React project folder.
  - scholrbnewproject/frontend/: Deleted marketplace React frontend directory.
  - mathclone/ & mathclone.zip: Deleted static clone folder and archive.
  - lms/public/: Deleted all remaining compiled React build assets (index-CVRRqoBu.css, index-DIYyqaHx.js, create_a_video-*.mp4, logo-CfuMhPsz.png, hero_illustration.jpg, avicon.svg, icons.svg).

---

### [2026-10-19 19:15] Removal of React Frontend & Restoration of Native Rocket LMS (Landing Builder)
* **Goal & Scope:**
  - Removed all React overrides and restored native Rocket LMS Blade frontend (Option A: Original Landing Page Builder).
* **Code & Template Modifications:**
  - [lms/routes/web.php](file:///c:/jyoti/scholarborg/scholarborg/lms/routes/web.php): Removed React routes override group at the end of the file that was redirecting web requests to index.html.
  - [lms/resources/views/design_1/web/home/index.blade.php](file:///c:/jyoti/scholarborg/scholarborg/lms/resources/views/design_1/web/home/index.blade.php): Restored @extends('landingBuilder.front.landing.index', ['landingItem' => ]).
  - [lms/public/index.html](file:///c:/jyoti/scholarborg/scholarborg/lms/public/index.html): Deleted local static React HTML file.
* **Remote Deployment & Cleanup:**
  - Connected via SSH (89.117.157.69:65002) and deleted remote /public/index.html and built React assets (index-DIYyqaHx.js, index-CVRRqoBu.css).
  - Synced modified routes/web.php and index.blade.php to production.
  - Cleared all Laravel caches: route:clear, cache:clear, config:clear, view:clear.
* **Verification:**
  - https://scholarb.org returning HTTP 200 OK with full native Rocket LMS Blade structure and meta tags.
  - https://scholarb.org/crm returning HTTP 200 OK.

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

---

### [2026-08-06 03:30] Integration of Scholarb Template Landing Page
* **Zip File Extraction:** Extracted the GCSE landing page template from `d:\scholarborg\mathclone.zip` containing `index.html` and `style.css`.
* **Static Homepage Integration:** Created a new static Blade view `scholarb_home.blade.php` and copied its accompanying stylesheet `scholarb.css`. Bypassed the default database-driven Landing Page Builder by directly including `scholarb_home` in the homepage index.
* **Modified/New Files:**
  - [scholarb_home.blade.php](file:///d:/scholarborg/lms/resources/views/design_1/web/home/scholarb_home.blade.php) [NEW]
  - [scholarb.css](file:///d:/scholarborg/lms/public/assets/default/css/scholarb.css) [NEW]
  - [index.blade.php](file:///d:/scholarborg/lms/resources/views/design_1/web/home/index.blade.php)

---

### [2026-08-06 03:46] Homepage Auth Link Fixes
* **Auth Routes Alignment:** Updated all static landing page references of `/auth/login` and `/auth/signup` to point to the correct Laravel routes `/login` and `/register` respectively.
* **Modified Files:**
  - [scholarb_home.blade.php](file:///d:/scholarborg/lms/resources/views/design_1/web/home/scholarb_home.blade.php)

---

### [2026-08-06 04:05] Desktop Layout Refinement & Spacing Optimization
* **Desktop Styling Polish:** Appended a `@media (min-width: 1024px)` block to `scholarb.css` implementing comprehensive desktop layout fixes:
  - Normalizing feature card, AI marking card, and testimonial card heights.
  - Setting standard card border radii to 20px, equal 32px padding, and aligned CTA buttons.
  - Standardizing section spacing to 100px.
  - Centering section headings and cap illustration.
  - Constraining paragraphs to 600px width and lesson previews to 760px width.
  - Formatting testimonial cards into a clean 3-column grid and circular avatars.
* **Modified Files:**
  - [scholarb.css](file:///d:/scholarborg/lms/public/assets/default/css/scholarb.css)
  - [scholarb_home.blade.php](file:///d:/scholarborg/lms/resources/views/design_1/web/home/scholarb_home.blade.php)

---

### [2026-08-06 04:26] Revert Desktop Layout Refinement & Spacing Optimization
* **Reversion of CSS Refinements:** Restored `scholarb.css` back to its original extracted version to discard the desktop spacing and alignment changes. Kept the custom Scholarb GCSE landing page active with dynamic parameters and the corrected auth links.
* **Modified Files:**
  - [scholarb.css](file:///d:/scholarborg/lms/public/assets/default/css/scholarb.css)

---

### [2026-08-08 20:59] Revert Scholarb Template Landing Page Deployment & Theme Settings
* **Homepage Reversion:** Reverted `index.blade.php` to its original design (`landingBuilder.front.landing.index`) to bypass the static GCSE homepage template.
* **Deleted Files Locally and Remotely:**
  - Removed custom template file `scholarb_home.blade.php` locally and from remote production.
  - Removed custom stylesheet file `scholarb.css` locally and from remote production.
* **Database Theme Update:** Set `enable = 0` on the custom `MathsGenie` theme (id: 13) in the remote database to fall back to the default `Lite (Included)` theme, restoring the original navigation bar structure.
* **Laravel Cache Cleared:** Executed artisan `cache:clear`, `config:clear`, `route:clear`, and `view:clear` on the remote server to propagate changes.
* **Modified/Deleted Files:**
  - [index.blade.php](file:///d:/scholarborg/lms/resources/views/design_1/web/home/index.blade.php) [MODIFY]
  - [scholarb_home.blade.php](file:///d:/scholarborg/lms/resources/views/design_1/web/home/scholarb_home.blade.php) [DELETE]
  - [scholarb.css](file:///d:/scholarborg/lms/public/assets/default/css/scholarb.css) [DELETE]

---

### [2026-08-08 22:06] Revert New UI Template Deployment
* **Layout Rollback:** Restored the homepage index to its default dynamic state, removing the custom 3-column landing page views, style sheets, and hero illustration images.
* **Laravel Cache Cleared:** Executed artisan view and application cache clears to restore the previous dynamic landing builder layout on the live site.
* **Deleted Files Locally and Remotely:**
  - [scholarb_home.blade.php](file:///d:/scholarborg/lms/resources/views/design_1/web/home/scholarb_home.blade.php) [DELETE]
  - [scholarb.css](file:///d:/scholarborg/lms/public/assets/default/css/scholarb.css) [DELETE]
  - `scholarb_student_hero.png` [DELETE]
  - `scholarb_parent_hero.png` [DELETE]
  - `scholarb_teacher_hero.png` [DELETE]

---

### [2026-08-08 22:41] Deploy Pixel-Perfect Scholarb Landing Page (v3)
* **New Template Deployed:** Built complete pixel-perfect `scholarb_home.blade.php` matching all 3 reference images (Student, Parent, Teacher states) exactly — with role-switching hero, interactive tabs, animations, and all sections.
* **Complete Design System:** Created `scholarb.css` with full CSS variables, premium Inter font, card styles, role tabs, step connectors, stats grid, CTA banner, FAQ accordion, footer, and auth modal.
* **Role-Based Interactions:** Single-page dynamic role switching (Students → Parents → Teachers) with fade transitions — changes H1, description, bullets, CTA, illustration, feature cards, How It Works steps, boards/subjects, testimonials, stats, CTA text, FAQs.
* **3 Hero Illustrations Generated:** `scholarb_student_hero.png`, `scholarb_parent_hero.png`, `scholarb_teacher_hero.png`.
* **Laravel Cache Cleared:** All artisan caches cleared on remote server.
* **Verified:** Both https://scholarb.org and https://scholarb.org/crm returned 200 OK.
* **Files Modified:**
  - [index.blade.php](file:///d:/scholarborg/lms/resources/views/design_1/web/home/index.blade.php) [MODIFY]
  - [scholarb_home.blade.php](file:///d:/scholarborg/lms/resources/views/design_1/web/home/scholarb_home.blade.php) [NEW]
  - [scholarb.css](file:///d:/scholarborg/lms/public/assets/default/css/scholarb.css) [NEW]
  - `scholarb_student_hero.png` [NEW]
  - `scholarb_parent_hero.png` [NEW]
  - `scholarb_teacher_hero.png` [NEW]





---

### [2026-08-08 23:15] Hero Section Pixel-Perfect Rebuild v4
- Rebuilt hero section ONLY matching 3 reference images (student/parent/teacher hero crops)
- sb-hero-wrap: lavender-white #F5F7FF bg
- Hero grid 42%/58% columns; badge SVG icon; H1 two spans (navy/purple)
- Trust row split into 2 rows matching reference
- Connector SVG: 3 horizontal dashed purple lines with dot endpoints
- Role tabs: CSS grid 3-equal-cols + SVG icons (no emoji)
- Illustration: lavender radial blob, 295px height, align flex-end
- Feature cards: featureIcons SVG map replacing emoji
- Card footer: first dot active, 26px wide active dot
- Verified: both https://scholarb.org and /crm returned 200 OK

---

### [2026-08-09 01:05] Hero UI Polish & Curved Dotted Connectors
- Nest connectors inside the card (sb-card-connectors-wrapper with right: 100%) so they anchor exactly on the left border of the card at any screen width
- Rebuild connector lines to use curved bezier paths (Path 1 top curves to tabs, Path 2 middle curves to illustration, Path 3 bottom curves to CTA level)
- Add relative position and overflow: visible to right card container so connectors can overflow to the left
- Update stylesheet caching tag to ?v=2.4
- Verify: both portal and crm return 200 OK


---

### [2026-08-09 01:20] Rebuild Dotted Connectors to Single-Origin
- Replaced three separate starting nodes with a single source node (cx=20, cy=455) aligned exactly with the Join Now CTA button.
- Three S-curve bezier paths branch out from this single node and connect to the card's tabs, illustration, and bottom levels.
- Updated wrapper CSS width to 180px to prevent visual clipping.
- Updated cache buster tag to ?v=2.5.
- Verified: portal and crm return 200 OK.


---

### [2026-08-09 01:23] Stepped L-Curve Dotted Connectors & CTA Alignment
- Re-aligned the single source node to y=530 to align vertically with the Join Now CTA button center on desktop.
- Swapped the diagonal bezier curves with stepped L-shape connectors (horizontal + vertical segments with rounded corner curves) to match the reference UI.
- Main vertical stem connects from y=530 up to y=280.
- Top line starts at y=140 (tabs), goes horizontally left, curves down, and meets the stem at y=280.
- Middle line starts at y=350, goes horizontally left, curves down, and meets the stem at y=440.
- Bottom line starts at y=490, goes horizontally left, curves down, and meets the stem at y=530.
- Updated stylesheet cache version to ?v=2.6.
- Verified: portal and crm return 200 OK.


---

### [2026-08-09 01:26] Adjust Connectors Vertical Shift for CTA Button
- Shifted source node vertically from y=530 to y=630 to align perfectly with the middle of the 'Join Now' CTA button.
- Shifted bottom L-curve path vertical segment down to meet y=630.
- Increased SVG viewBox height from 600px to 700px to accommodate the taller curve extension.
- Updated stylesheet cache version to ?v=2.7.
- Verified: portal and crm return 200 OK.


---

### [2026-08-09 01:30] Premium SaaS Role Card Redesign
- Container depth: Replaced simple shadow with ultra-soft, multi-layered depth shadow; added border border-left support.
- Segmented tabs: Changed role selector tabs to use #FAFBFD background and white active background with tab shadow to look like segmented controls.
- Feature cards: Removed solid grey borders, added #FAFBFD off-white background and animated border-left transition (purple strip) on hover.
- Lift & Slide animations: Card lifts up on hover, icon scales up 1.08x, and next button arrow SVG translates horizontally.
- Gradient icon circles: Icons in feature cards now sit inside soft linear-gradients (linear-gradient(135deg, rgba(91,75,219,0.05) 0%, rgba(91,75,219,0.12) 100%))
- Updated stylesheet cache version to ?v=2.8.
- Verified: portal and crm return 200 OK.


---

### [2026-08-09 02:18] Premium Hero Left Polish + How It Works 3-Step Redesign

#### Hero Left Column
- Badge: Added shimmer animation (badge-shimmer keyframe) + green pulse dot beside badge text
- H1 Line 2: Replaced solid purple color with gradient text (linear-gradient 5B4BDB -> 8B5CF6 + background-clip)
- Bullets: Changed from vertical list to wrapping pill-style chip row with hover lift, purple left border, gradient icon circles
- CTA: Added .sb-hero-cta-row wrapper; added secondary 'See how it works ->' link beside main button
- Stats Row: Added 3 quick stats (5,000+ Students | 4.9? Rating | 98% Satisfaction) above trust section
- Avatar Cluster: Added 4 colored initials avatars (AR, PK, SM, RI, +) with 'Join 5,000+ happy learners' text

#### How It Works Section
- Changed from JS-populated 5-step layout to static premium 3-step card layout
- Each step is a white card with multi-layered depth shadow and hover lift (translateY -6px)
- Step numbers: gradient badge squares (linear-gradient 5B4BDB -> 8B5CF6) with glowing shadow
- Icons: rounded-square containers with hover scale animation
- Connectors: SVG dashed arrows between step cards
- Section: gradient background + radial purple glow halo above
- Updated stylesheet cache version to ?v=2.9
- Verified: portal and crm return 200 OK


---

### [2026-08-09 02:24] Fix Blank Hero & Javascript Accidental Crashing
- Restored hiwTitle and hiwSteps container elements with their IDs so JavaScript can correctly render content dynamically without throwing TypeErrors.
- Updated JavaScript render functions to safely check for element existence before calling .textContent or .innerHTML, making the site robust against future layout modifications.
- Rebuilt the How It Works CSS grid to cleanly align 5 columns dynamically rendered by JavaScript, with font-size adjustments for emojis in step icons.
- Cleaned up extra closing section tags in the HTML template.
- Updated stylesheet cache version to ?v=3.0.
- Verified: portal and crm return 200 OK.


---

### [2026-08-09 02:36] Compact Role Card Styles on Mobile Viewports
- Reduced mobile padding of role card container (header: 16px 16px 12px; features: 12px 14px 14px; footer: 12px 16px 14px).
- Decreased card title font size to 17px and subtitle to 11.5px on mobile.
- Decreased active role tab padding to 10px 4px, font size to 12px, and tab icon size to 14px.
- Reduced illustration height on mobile to 140px and background blob size to 140px to save vertical space.
- Scaled down feature cards on mobile (padding: 8px 10px; gap: 10px; title: 12.5px; description: 11px; icon size: 30px; inner icon SVG: 13px).
- Compacted carousel controls on mobile (arrow button: 32px diameter; dots size: 6px).
- Updated stylesheet cache version to ?v=3.1.
- Verified: portal and crm return 200 OK.


---

### [2026-08-09 02:40] Compact Hero Left Column Text Sizes on Mobile Viewports
- Reduced badge text size to 11px and padding to 5px 12px on mobile.
- Reduced hero H1 font size to 26px, description to 14px, and bullets to 12px on mobile.
- Scaled down bullet checkmark icons to 18px diameter and checkmark svg size to 9px.
- Reduced CTA wrap spacing, button padding to 12px 20px, CTA font size to 14px, and secondary link to 13px.
- Shrank stats numbers to 18px and labels to 11px, reducing horizontal grid spacing to 14px on mobile.
- Scaled down avatar cluster circles to 28px diameter and social proof text to 12px.
- Reduced Trustpilot stars size to 14px and Trustpilot text to 12px.
- Updated stylesheet cache version to ?v=3.2.
- Verified: portal and crm return 200 OK.


---

### [2026-08-09 02:42] Sweet-Spot Medium Typography and Element Sizing on Mobile Viewports
- Adjusted mobile badge text size to 11.5px, padding to 6px 14px, and margin-bottom to 20px.
- Set mobile H1 size to 29px, description to 14.5px, and bullets to 13px with 10px spacing.
- Shrank mobile checkmark icons to 20px diameter with 10px SVG check graphics.
- Adjusted mobile CTA button padding to 13px 24px, text to 14.5px, and secondary link to 13.5px.
- Set mobile stats numbers to 20px, labels to 11.5px, and horizontal gaps to 16px.
- Set mobile avatar cluster circles to 30px diameter and social proof text to 12.5px.
- Adjusted mobile card header padding to 18px 18px 14px, card title to 18px, and card subtitle to 12.5px.
- Adjusted active role tab padding to 12px 6px, font size to 12.5px, and tab icon size to 15px.
- Adjusted mobile illustration height to 160px and backdrop blob to 160px.
- Set mobile feature card padding to 10px 12px, gap to 12px, icon to 34px, and title to 13px.
- Set mobile carousel footer padding to 14px 18px 16px, next button to 34px diameter, and dots to 7px.
- Updated stylesheet cache version to ?v=3.3.
- Verified: portal and crm return 200 OK.


---

### [2026-08-09 02:52] Implement Pixel-Perfect Mobile-Only Entry Layout
- Wrapped original desktop hero inside .sb-hero-desktop-only container.
- Added .sb-hero-mobile-only container containing the exact layout from the mobile screenshot.
- Replaced default announcement banner on mobile with a compact gift banner card containing a right chevron.
- Structured the middle section as a 2-column mobile-only layout (bullets column on the left and arched student photo card with overlay on the right).
- Designed the right arch card using 100px top border-radius and overlaying floating '5000+ Happy Learners' stats pill.
- Added a dedicated stats and trust card at the bottom containing 3 styled column items, an avatar cluster, and Trustpilot rating stars.
- Added circular chevron scroll indicator at the bottom.
- Styled all elements (.sb-mobile-announce, .sb-mobile-bullet-chip, .sb-mobile-arch-card, .sb-mobile-stats-card, .sb-mobile-scroll-down) in the stylesheet.
- Configured layout toggle inside @media (max-width: 950px) to switch desktop/mobile views.
- Updated stylesheet cache version to ?v=3.4.
- Verified: portal and crm return 200 OK.


---

### [2026-08-09 03:06] Reorder Header and Announcement Bar on Mobile Viewports
- Applied CSS Grid ordering to the sb-page container on mobile screens (max-width: 600px).
- Positioned the Navigation Bar (sb-nav) at grid-row: 1 so it renders at the absolute top of the viewport.
- Positioned the Announcement Bar (sb-announce) at grid-row: 2 so it sits cleanly below the navbar.
- Let all other page sections auto-place chronologically starting from grid-row: 3.
- Updated stylesheet cache version to ?v=3.4.
- Verified: portal and crm return 200 OK.


---

### [2026-08-09 02:45] Premium Dynamic Vector Alternating Timeline for 'How It Works'
- Redesigned How It Works section to render a custom vector-illustration timeline that scales dynamically.
- Vector Illustrations: Styled 5 inline SVGs representing target/goal, tutor profile, calendar booking, classroom laptop, and growth analytics.
- Desktop Layout (>1024px): Implemented a 3-row grid where cards alternate above (Row 1) and below (Row 3) a central horizontal connecting timeline track (Row 2). Added vertical dashed card-to-milestone connector lines.
- Tablet & Mobile Layout (<1024px): Switched automatically to a single vertical timeline with milestone number circles on the left, card segments on the right, and a continuous vertical dashed line behind the milestones.
- Built animation effects: keyframe float-illus for vector images, active scale-and-glow milestones, and smooth transitions.
- Cleaned up grid columns overrides at bottom of stylesheet.
- Updated cache buster version to ?v=3.4.
- Verified: portal and crm return 200 OK.


---

### [2026-08-09 02:44] Premium How It Works Section  Interactive Stepper & Live Preview Mockups
- Replaced steps grid in HTML with an interactive two-column layout: left column displays the stepper items, right column displays a dynamic preview frame.
- Built 5 unique interface mockups completely in CSS (Find Tutor search widget, calendar slot selector, classroom dashboard whiteboard, weekly scheduler planner, student performance progress charts).
- Integrated automatic active step state triggers and smooth CSS transition fades in JavaScript.
- Implemented dynamic lazy-loading in JavaScript to populate and render the mockup HTML inside mobile accordion panels only when active, preventing mobile CPU waste.
- Added tablet & mobile responsive collapse rules: under 900px, hides the desktop right-hand panel, collapses the stepper list into a single-column layout, and expands active accordion drawers vertically, rendering the compact CSS mockup card directly below the description.
- Updated stylesheet cache version to ?v=3.4.
- Verified: portal and crm return 200 OK.



## [2026-08-09 12:55:00] - How It Works Redesign & 18-Point UI/Accessibility Polish
### Files Modified:
- lms/resources/views/design_1/web/home/scholarb_home.blade.php
- lms/public/assets/default/css/scholarb.css

### Summary of Changes:
- Redesigned the 'How It Works' 5-card layout into a connected, interactive learning journey timeline.
- Added role-based dynamic layouts and lightweight SVG icons for Student, Parent, and Teacher steps.
- Solved all 18 UI/accessibility feedback points:
  1. Fixed state transitions so only one step is active at a time; past steps show a completed checkmark.
  2. Aligned vertical timeline track mathematically from Step 1 center to Step 5 center (zero stray stubs).
  3. Replaced infinite looping timer with a smooth one-shot scroll entrance animation.
  4. Fixed mobile responsive badge shifts by transforming cards instead of full step rows.
  5. Enhanced WCAG AA contrast ratio (> 7:1) for description text.
  6. Added list semantics (<ol> and <li>), 	abindex=0, keyboard navigation handlers (Enter/Space), and focus rings.
  7. Added prefers-reduced-motion CSS support.
  8. Fixed textspeak heading to 'What are you learning?' and replaced emoji icons in boards chips with modern inline SVGs.


## [2026-08-09 12:58:45] - Timeline Track Position Fix (Anchored inside hiwSteps container)
### Files Modified:
- lms/resources/views/design_1/web/home/scholarb_home.blade.php
- lms/public/assets/default/css/scholarb.css

### Summary of Changes:
- Moved timeline track inside hiwSteps container with position: relative anchor.
- Fixed line stretching into header area; line now starts at Step 1 badge center and ends at Step 5 badge center.


## [2026-08-09 13:04:45] - Boards & Exams Section High-End SaaS UI Overhaul
### Files Modified:
- lms/resources/views/design_1/web/home/scholarb_home.blade.php
- lms/public/assets/default/css/scholarb.css

### Summary of Changes:
- Replaced 14 squeezed single-row items on desktop with a responsive 7-column grid layout (2 balanced rows of 7 chips).
- Replaced mobile floating flex rows with a symmetric 3-column grid system.
- Added custom SVG icon containers inside each chip with soft purple background accents.
- Fixed textspeak heading to 'What are you learning?' across all role states.
- Enhanced active state with linear gradient background, white SVG icons, and box-shadow glow.
- Redesigned 'View All Boards & Exams' CTA into a modern pill button with hover hover animation.


## [2026-08-09 13:08:30] - Boards & Exams UI Redesign (Pill Cards & Responsive Grid)
### Files Modified:
- lms/resources/views/design_1/web/home/scholarb_home.blade.php
- lms/public/assets/default/css/scholarb.css

### Summary of Changes:
- Transformed the plain single-line stretched boxes into a 7-column multi-row desktop grid (3-column on tablet, 2-column on mobile).
- Designed horizontal pill-chips featuring a 36px rounded glass icon container with vibrant SVG icons, bold label text, and active checkmark badges.
- Enhanced active state with luminous purple border tint, glowing icon wrapper, and soft elevation shadows.


## [2026-08-17 14:30:00] - API Key Configuration for Decoupled React Frontend
### Files Modified:
- None (Database settings updated directly in setting_translations table)

### Summary of Changes:
- Configured a secure API Key (scholarb_react_api_key_2026) under the mobile_app_general_settings row in setting_translations table to unblock API routes.
- Cleared Laravel caches (cache:clear, view:clear, config:clear) on the production server.
- Verified categories and test endpoints are fully operational via API request with the x-api-key header.


## [2026-08-17 15:08:00] - Parent Portal API Integration
### Files Modified:
- lms/routes/api/user.php
- lms/app/Http/Controllers/Api/Panel/ParentChildrenController.php

### Summary of Changes:
- Added Parent Portal APIs in backend under the prefix /api/development/panel/parent.
- Implemented endpoints for listing children (GET /children), linking children (POST /children), unlinking children (DELETE /children/{id}), and viewing child progress metrics (GET /children/{id}/progress).
- Created ParentChildrenController.php inside the API Panel namespace to handle logic.
- Verified all endpoints successfully on production using python test scripts.


## [2026-08-17 17:25:00] - Tutor Profile Redesign & Mobile-Responsive Login Page UI
### Files Modified:
- scholarb-reference-ui/src/api.js
- scholarb-reference-ui/src/pages/TutorListing.jsx
- scholarb-reference-ui/src/pages/LandingPage.jsx
- scholarb-reference-ui/src/pages/TutorProfile.jsx
- scholarb-reference-ui/src/pages/Login.jsx

### Summary of Changes:
- Modified `TutorListing.jsx` and `LandingPage.jsx` to wrap the tutor cards in router `<Link>` elements, making the entire card clickable and fixing guest navigation.
- Redesigned `TutorProfile.jsx` to match the Arvind Sharma mockup page layout, featuring the square photo, online badge, watch intro play video trigger, subjects teach checklist, qualifications details list, and average ratings bars.
- Connected the tutor profile booking selector (class types, calendar dates, time slots) with guest redirect handlers to route non-login public users to the Login page.
- Rewrote `Login.jsx` to render the mockup-based mobile-responsive split-panel design (left-side steps panel connected by dashed lines, right-side social tabs, expandable email form fields, and bottom horizontal timeline/trust badges).
- Removed the custom redundant header in `Login.jsx` to prevent overlap with the global floating capsule `<Navbar />` header.


## [2026-08-17 17:30:00] - Login Timeline UI Polish
### Files Modified:
- scholarb-reference-ui/src/pages/Login.jsx

### Summary of Changes:
- Polished the step icons on the left panel to use distinct colors (green, blue, purple, orange) and added matching soft background tints to their circular badges.
- Adjusted the dotted timeline connector positioning to align perfectly down the centers of the step number circles.


## [2026-08-17 17:35:00] - Login Buttons & Tabs UI Polish
### Files Modified:
- scholarb-reference-ui/src/pages/Login.jsx

### Summary of Changes:
- Polished the active tab underline indicator so its width aligns exactly with the text of the selected tab ("Log In" or "Sign Up") instead of stretching across the button's padding.
- Enhanced the borders and shadows of the WhatsApp, Google, and Email buttons to match the mockup design exactly.
- Removed the top border line from the bottom "New to Scholarb? Join Now for Free" link block to keep it clean.


## [2026-08-17 17:40:00] - Timeline Bar & Trust Badges UI Polish
### Files Modified:
- scholarb-reference-ui/src/pages/Login.jsx

### Summary of Changes:
- Redesigned the "How It Works" timeline bar to render matching light background colors for the step icons (blue, emerald, purple, orange/yellow) and simplified arrows.
- Replaced the generic raw text emojis in the trust badges section (, , , ) with polished inline SVG Lucide icons (`ShieldCheck`, `Zap`, `AlertCircle`, `Star`) with corresponding brand colors.


## [2026-08-17 17:55:00] - Login Timeline & Footer Badges Graphic Polish
### Files Modified:
- scholarb-reference-ui/src/pages/Login.jsx

### Summary of Changes:
- Replaced basic unicode emojis in footer badges (, , , ) with polished colorful SVG icons using Lucide-react (Shield, Zap, Ban, Star).
- Redesigned the "How it works" bottom timeline container border-radius, background, and drop shadow.
- Styled step icons inside the timeline to render inside clean rounded square containers with their respective soft background tints (blue, green, purple, orange) and replaced arrow text with crisp Lucide ArrowRight SVGs.


## [2026-08-17 18:00:00] - Interactive Animations & 3D Ambient Glows on Login Page
### Files Modified:
- scholarb-reference-ui/src/pages/Login.jsx

### Summary of Changes:
- Integrated Framer Motion layoutId springs to dynamically slide the blue selected highlight under active tabs ("Log In" vs "Sign Up").
- Wrapped timeline step rows in reactive `motion.div` blocks to add smooth spring-based hover translations (shifts row slightly to the right, scales up step numbers and colorful badges, and glows title text).
- Replaced static buttons for WhatsApp, Google, and Email with `motion.button` instances featuring tactile hover scaling/y-axis translation and click press shrink transitions.
- Embedded soft blue and purple background radial blur gradient glow circles in the page wrapper to make the login card stand out with premium visual depth.


## [2026-08-17 18:05:00] - Dark Mode Button Spacing & Style Fixes
### Files Modified:
- scholarb-reference-ui/src/components/Navbar.jsx
- scholarb-reference-ui/src/pages/Login.jsx

### Summary of Changes:
- Modified the mobile menu overlay's "Join for Free" button in `Navbar.jsx` to render as a blue-indigo gradient button (`bg-gradient-to-r from-blue-600 to-indigo-600`) instead of solid white (`dark:bg-white`) in dark mode, matching the desktop navbar layout.
- Added explicit transparent/dark background utility styling (`bg-transparent dark:bg-slate-950/40`) to the "Back" button inside the email form in `Login.jsx` to prevent it from inheriting a browser default white background color in dark mode.
- Wrapped the "Back" and "Log In" submit buttons inside `Login.jsx` in `motion.button` wrappers with spring tactile scale states.


## [2026-08-17 18:10:00] - Login Slate-850 Invalid Class Fixes in Dark Mode
### Files Modified:
- scholarb-reference-ui/src/pages/Login.jsx

### Summary of Changes:
- Fixed the invalid class name `dark:hover:bg-slate-850` on the Google and Email buttons in `Login.jsx` by replacing it with the standard, supported `dark:hover:bg-slate-800`.
- Corrected the `border-slate-850` and `dark:border-slate-850` invalid classes by replacing them with the standard `border-slate-300` and `dark:border-slate-800`.
- This ensures that hovering over the social buttons in dark mode correctly dims the background to dark gray rather than falling back to light mode's solid white background color.


## [2026-08-17 18:10:00] - Default Theme to Dark Mode on Startup
### Files Modified:
- scholarb-reference-ui/src/components/Navbar.jsx

### Summary of Changes:
- Initialized `isDarkMode` state inside `Navbar.jsx` to default to `true` (dark mode) on initial mount, unless the user has explicitly saved a preference in `localStorage`.
- Updated the theme effect to write changes back to `localStorage` when toggled, ensuring persistent preferences.

## [2026-08-17 18:28:00] - Signup Page Redesign + Auth Context + Book Demo Guard
### Files Modified:
- scholarb-reference-ui/src/context/AuthContext.jsx (NEW)
- scholarb-reference-ui/src/api.js
- scholarb-reference-ui/src/App.jsx
- scholarb-reference-ui/src/pages/Signup.jsx
- scholarb-reference-ui/src/components/Navbar.jsx
- scholarb-reference-ui/src/pages/DemoBookingFlow.jsx

### Summary:
- Created AuthContext.jsx: global JWT token + user state with login/logout + localStorage persistence.
- Updated api.js: Added LMS_API_BASE and fetchLmsApi() helper for register/login/verification endpoints.
- Updated App.jsx: Wrapped app in AuthProvider for global auth state.
- Redesigned Signup.jsx: Premium 3-sub-step registration page integrated with backend POST /api/register/step/{1,2,3}. Step 1=email+password, Step 2=6-digit OTP verification, Step 3=full name. On completion, auto-logs in via AuthContext.
- Updated Navbar.jsx: Auth-aware header. Shows user avatar + dropdown (Book Demo, Logout) when logged in. Shows Login + Join Free buttons when logged out.
- Updated DemoBookingFlow.jsx: Added auth guard. Unauthenticated users see a friendly "Create Account First" screen with links to /signup and /login instead of the booking form.

---
## [2026-08-17 19:10] Full Backend Integration — Signup, DemoBooking, Zoho CRM

**Files Modified:**
- lms/routes/api/guest.php — Fixed critical PHP syntax error (duplicate unction () {) that broke all /api/ routing
- lms/app/Http/Controllers/Api/Web/OnboardingController.php — Added Registered event dispatch on new user creation (triggers SyncRegistrationToZoho listener automatically), passes WhatsApp number as mobile field, added isNewUser guard
- scholarb-reference-ui/src/api.js — Fixed LMS_API_BASE from /api/development to /api so /register/step/* and /demo-booking/* routes resolve correctly
- scholarb-reference-ui/src/App.jsx — Wrapped Router with <AuthProvider> for global auth state
- scholarb-reference-ui/src/pages/DemoBookingFlow.jsx — Full rewrite: 5-step wizard (Academic Profile > Learning Prefs > Availability > Your Details > OTP Verify), wired to POST /api/demo-booking/book and POST /api/demo-booking/verify, auto-login on success with JWT from backend
- scholarb-reference-ui/src/pages/Signup.jsx — Already integrated; confirmed correct (3 inline steps: credentials → OTP → name via POST /api/register/step/{1,2,3})

**Summary:**
- Fixed PHP routing syntax error that blocked all API calls
- Zoho CRM lead creation now fires automatically when a user books a demo (Registered event → SyncRegistrationToZoho listener → ZohoCrmService::createLead())
- WhatsApp number stored as mobile field on user model → Zoho gets phone data
- LMS_API_BASE corrected; all frontend API calls now hit correct Laravel routes
- AuthContext wraps entire app for persistent login state


## [2026-08-18 20:25:00] - React New Frontend Served at Root Domain
### Files Modified:
- lms/routes/web.php
- lms/routes/custom_admin.php
- lms/public/index.html (NEW)
- lms/public/logo.png (NEW)
- lms/public/hero_illustration.jpg (NEW)
- lms/public/icons.svg (NEW)
- lms/public/favicon.svg (NEW)
- lms/public/assets/index-CVRRqoBu.css (NEW)
- lms/public/assets/index-DIYyqaHx.js (NEW)
- lms/public/assets/logo-CfuMhPsz.png (NEW)
- lms/public/assets/create_a_video-CQHQs9c8.mp4 (NEW)
- deploy_react_only.py (NEW)
- deploy.py (OPTIMIZED)

### Summary of Changes:
- Built the React frontend (scholarb-reference-ui) using npm run build and copied the dist/ output into lms/public/ so assets are served directly by the Laravel public directory.
- Appended React Frontend Route Overrides at the very bottom of routes/web.php, overriding the default Laravel HomeController@index and Auth\LoginController routes so that GET /, /login, /signup, /tutors, /tutors/{id}, /book-demo, /for-students, /for-parents, /for-teachers, /resources all serve the React index.html.
- Fixed custom_admin.php: The AppointmentsController group had a double-namespace bug (namespace App\Http\Controllers\Admin inside a provider that already prefixes App\Http\Controllers\Admin), changed to empty namespace to prevent ReflectionException errors.
- Created deploy_react_only.py: A targeted deployment script that uploads only the React assets and route files (11 files) instead of running a full LMS sync. Use this for all future React frontend updates.
- Optimized deploy.py: Added dirs[:] pruning in os.walk to skip excluded folders (vendor, node_modules, etc.) in-place, reducing dry-run scan time from 20+ minutes to seconds.
- Verified: Both https://scholarb.org and https://scholarb.org/crm return 200 OK. React mount point (div id=root) confirmed present on / and /login.


## [2026-08-24 02:48:13] - Credentials Extraction & Backend Generation Prompt
### Files Modified:
- d:\scholarborg\scholrbnewproject\credentials\keys_and_ssh.json (NEW)
- d:\scholarborg\scholrbnewproject\credentials\credentials_summary.md (NEW)
- d:\scholarborg\scholrbnewproject\backend_prompt.md (NEW)

### Summary of Changes:
- Extracted workspace configuration credentials (SSH, databases) and API keys (Microsoft Teams OAuth, Agora RTCP, Zoho CRM, Firebase service account) into a secure JSON config and human-readable summary.
- Created `backend_prompt.md` in the new project directory containing the database migration blueprint, Spatie role definitions, API routes mapping, state machines transition logic, integration details, and LLM prompt wrappers for automated backend generation.


## [2026-08-24 03:05:59] - Backend Deploy & API Verification Test Suite
### Files Modified:
- d:\scholarborg\scholrbnewproject\backend\... (Laravel codebase)
- d:\scholarborg\scholrbnewproject\deploy_marketplace.py (NEW)
- d:\scholarborg\scholrbnewproject\test_apis.py (NEW)

### Summary of Changes:
- Initialized a new Laravel 11 project locally in `scholrbnewproject/backend`.
- Configured `.env` with remote CRM database (`u181357068_Serp1`) and credentials (SSH, Agora, Teams, Zoho).
- Created a unified migration file `2026_08_24_000000_create_marketplace_tables.php` defining all new Teacher Marketplace tables with the `tmp_` prefix to isolate them from ERPGo CRM tables.
- Implemented Eloquent Models (`TeacherProfile`, `Booking`, `Escrow`, `LedgerEntry`, etc.) and controllers (`AuthController`, `TeacherController`, `BookingController`, `ClassController`, `AdminController`).
- Registered Sanctum API routes in `backend/routes/api.php` under prefix `v1/marketplace`.
- Wrote `deploy_marketplace.py` to sync the directory via SFTP, run remote composer install, run migrations, and cache configs.
- Created a symbolic link in `/home/u181357068/domains/scholarb.org/public_html/marketplace` pointing to `/home/u181357068/domains/scholarb.org/marketplace/public` on the remote server to enable public web access to the APIs.
- Wrote `test_apis.py` and executed it locally, validating the entire customer journey (Registration -> Onboarding -> Availability Slots -> Booking Slot Lock -> Checkout/Escrow -> Teams URL fallback -> Escrow Release -> Ledger check). All tests passed successfully.


## [2026-08-24 03:10:59] - Marketplace, Parent, and Sales CRM API Complete Implementation
### Files Modified:
- d:\scholarborg\scholrbnewproject\backend\app\Http\Controllers\Api\MarketplaceController.php (NEW)
- d:\scholarborg\scholrbnewproject\backend\app\Http\Controllers\Api\ParentController.php (NEW)
- d:\scholarborg\scholrbnewproject\backend\app\Http\Controllers\Api\SalesController.php (NEW)
- d:\scholarborg\scholrbnewproject\backend\routes\api.php (MODIFIED)
- d:\scholarborg\scholrbnewproject\test_apis.py (MODIFIED)

### Summary of Changes:
- Programmed `MarketplaceController.php` providing guest browsing, filtering (subjects, rates, ratings), and subject groups listing.
- Programmed `ParentController.php` for child student linking, listing children, and listing child calendar timelines/attendances.
- Programmed `SalesController.php` integrating ERPGo CRM `leads` and `lead_activity_logs` tables. Designed dynamic lead intent scoring (+20 per booking, +50 per payment, +15 on onboarding) and Whatsapp/Email/Call follow-up logged actions.
- Registered endpoints in `routes/api.php` under v1/marketplace prefix.
- Deployed all changes successfully to `/home/u181357068/domains/scholarb.org/marketplace` and cleared remote configuration caches.
- Updated `test_apis.py` with integration assertions covering verified teacher listing, parent link child flow, SSH test lead insertion, lead intent scoring (Hot classification), and followup remark registration. Verified all endpoints returned success.


## [2026-08-24 03:27:49] - Created Frontend Product Requirements Document (PRD)
### Files Modified:
- d:\scholarborg\scholrbnewproject\Education_Teacher_Marketplace_Frontend_PRD.md (NEW)

### Summary of Changes:
- Created the comprehensive Frontend Product Requirements Document (PRD) specifying the technology stack (React + Vite, TailwindCSS, Agora RTC Web SDK), UI/UX HSL color theme design systems, micro-animations, role-based dashboard calendar widgets (Student, Teacher, Parent, and Admin/Sales), and a matrix of features for the full-screen dedicated calendar page.


## [2026-08-24 03:29:28] - Created React Frontend PRD
### Files Modified:
- d:\scholarborg\scholrbnewproject\Education_Teacher_Marketplace_Frontend_PRD.md (NEW)

### Summary of Changes:
- Wrote the complete Frontend Product Requirements Document (PRD) specifying the React Vite client architecture, route guarding configurations, visual layouts for role-based dashboard calendar widgets (Student, Teacher, Parent, Admin/Sales), dedicated Full-Screen Calendar panel components, virtual classroom layouts with Agora integrations, and the API endpoints mapping matrix.


## [2026-08-24 03:38:30] - Created React Frontend Prompt Blueprint
### Files Modified:
- d:\scholarborg\scholrbnewproject\frontend_prompt.md (NEW)

### Summary of Changes:
- Wrote `frontend_prompt.md` containing the implementation specification and developer generation prompt for the React Frontend.
- Specified the dynamic segmented toggle switcher for Student (Cyan/Blue cyberpunk gradient), Teacher (Emerald Green/Mint professional gradient), and Parent (Warm Indigo/Amber trusted gradient) landing modes.
- Defined isolated component-specific CSS directories layout rules, routes parameters, Full-Screen scheduler handlers, and Axios client HTTP request configs.


## [2026-08-24 04:01:41] - Complete React Frontend Codebase Build
### Files Created / Modified:
- `d:\scholarborg\scholrbnewproject\frontend` (NEW React Vite Project)
- `src/index.css`: Multi-theme design tokens (Student, Teacher, Parent) & typography
- `src/services/api.js`: Live Axios client connecting to Laravel backend
- `src/context/AuthContext.jsx`: Session provider with token management
- `src/components/Navbar/`: Responsive header & mobile drawer
- `src/components/ModeSwitcher/`: 3-audience segmented control
- `src/components/Hero/`: Mode-adaptive value propositions & search bar
- `src/components/CategoryGrid/`: Dynamic subject category cards
- `src/components/FeaturedTeachers/`: Live verified teacher cards
- `src/components/HowItWorks/`: 3-step timeline per persona
- `src/components/Testimonials/`: Persona-specific review cards
- `src/components/BookingModal/`: 10-minute hold slot lock with countdown timer & checkout
- `src/components/Footer/`: Multi-column sitemap & branding
- `src/pages/Landing/`: Master landing page
- `src/pages/Auth/`: Login & Register pages with role selectors
- `src/pages/TeacherProfile/`: Public teacher profile with slot picker calendar
- `src/pages/Dashboard/`: Student, Teacher (Profile Data View), Parent, and Admin Dashboards
- `src/pages/Calendar/`: Dedicated full-screen scheduler
- `src/pages/Classroom/`: Live Agora video room & Teams launcher
- `src/App.jsx`: Complete client routing

### Summary of Changes:
- Built the entire React frontend application following the strict component-specific CSS architecture.
- Implemented the 3-mode audience switcher with dynamic color themes (Cyan for Students, Emerald for Teachers, Royal Indigo for Parents).
- Created the dedicated Teacher Profile & Data View page with live slot bookings.
- Successfully verified with `npm run build` (0 compilation errors).


## [2026-08-24 04:11:26] - Frontend Mobile Responsiveness Overhaul & Logical Landing Page Expansion
### Files Created / Modified:
- `src/components/Comparison/` (NEW): Scholarb vs Traditional Platforms comparison matrix
- `src/components/EarningsCalculator/` (NEW): Dynamic interactive income calculator for teachers
- `src/components/SafetyShield/` (NEW): Trust, KYC, and Escrow safety standard card grid
- `src/components/FAQ/` (NEW): Persona-specific FAQ accordions
- `src/components/FeaturedTeachers/`: Integrated in-page subject filters, price slider, and mobile cards
- `src/components/Navbar/`: Mobile drawer touch optimization and backdrop blur
- `src/components/ModeSwitcher/`: Compact mobile pill container with horizontal scrolling
- `src/components/Hero/`: Fluid typography clamp and 2x2 mobile stats grid
- `src/pages/Landing/`: Integrated all new sections in a logical education-first sequence
- `src/index.css`: Mobile viewport guards, tap highlight resets, and responsive scrollbars

### Summary of Changes:
- Completed a full mobile responsiveness audit ensuring flawless rendering on screens from 320px up to 1440px.
- Expanded the landing page with high-conversion marketplace features (comparison table, ROI calculator, safety shield, and FAQs).
- Verified production build (`npm run build` passed in 487ms with 0 errors).


## [2026-08-24 04:17:40] - Persona-Specific Logical Landing Page Overhaul
### Files Created / Modified:
- `src/components/ClassroomPreview/` (NEW): Interactive Agora video sandbox with Python code editor & math whiteboard for students
- `src/components/TeacherOSShowcase/` (NEW): Availability engine, automated ledger payouts, and Teams showcase for teachers
- `src/components/ParentCalendarShowcase/` (NEW): Multi-child family calendar simulation with attendance verification for parents
- `src/components/LandingViews/StudentLandingView.jsx` (NEW): Dedicated student discovery and booking flow
- `src/components/LandingViews/TeacherLandingView.jsx` (NEW): Dedicated zero-lead-fee monetization and tools flow
- `src/components/LandingViews/ParentLandingView.jsx` (NEW): Dedicated multi-child governance and safety flow
- `src/components/LandingViews/LandingViews.css` (NEW): View wrapper styling
- `src/pages/Landing/Landing.jsx`: Refactored to dynamically mount dedicated persona views

### Summary of Changes:
- Completely specialized the landing page data, components, and workflows per audience (Student, Teacher, Parent).
- Added interactive virtual classroom preview for students, Teacher Operating System tools for educators, and multi-child family calendar overlays for parents.
- Successfully verified with `npm run build` (0 compilation errors).


## [2026-08-24 04:21:24] - Fixed Cross-Route Section Navigation
### Files Modified:
- `src/components/Navbar/Navbar.jsx`: Added smart cross-route section scroll handler (`handleSectionClick`)
- `src/components/Footer/Footer.jsx`: Added smart cross-route section scroll handler (`handleSectionClick`)

### Summary of Changes:
- Fixed an issue where clicking `#how-it-works`, `#testimonials`, or `#faq` while on sub-routes like `/calendar` or `/teachers/:id` failed to navigate back to the home page.
- Section links now automatically navigate to `/` and smoothly scroll directly to the target anchor.


## [2026-08-24 04:23:05] - Fixed Teacher Profile Page Data Extraction
### Files Modified:
- `src/pages/TeacherProfile/TeacherProfilePage.jsx`: Added support for direct `res.data` response and fallback parsing. Added `formatLanguages` helper for JSON array strings.

### Summary of Changes:
- Fixed an issue where visiting `/teachers/:id` displayed "Teacher Profile Not Found" due to `res.data.teacher` being undefined when the API returns the teacher object directly at root `res.data`.


## [2026-08-24 04:26:12] - Implemented Full Interactive Month / Week / Day Calendar View Switcher
### Files Modified:
- `src/pages/Calendar/CalendarPage.jsx`: Implemented 3 dedicated calendar view engines:
  1. **Month View**: 35-cell dynamic monthly calendar grid with colored event chips and click-to-day transition.
  2. **Week View**: 7-column header with detailed schedule cards and escrow badges.
  3. **Day View**: Focused 11-hour daily timeline (9:00 AM - 7:00 PM) showing scheduled sessions and open booking slots.
- `src/pages/Calendar/CalendarPage.css`: Added responsive styling for monthly day grids, event chips, and daily timeline rows.

### Summary of Changes:
- Fixed the issue where clicking the Month, Week, and Day toggle pills on the `/calendar` page did not change the calendar view.


## [2026-08-24 04:30:58] - Complete Zero-Hardcoding Dynamic Backend Integration
### Files Modified:
- `src/pages/Calendar/CalendarPage.jsx`: Dynamic live availability slots and session loading directly from backend API
- `src/pages/Dashboard/StudentDashboard.jsx`: Dynamic student schedule, classroom launchers, and live mentor connections
- `src/pages/Dashboard/TeacherDashboard.jsx`: Live slot creation and deletion, double-entry financial ledger sync, and profile API updates
- `src/pages/Dashboard/ParentDashboard.jsx`: Dynamic child profile fetching, attendance history log, and child linking
- `src/pages/Dashboard/AdminDashboard.jsx`: Live sales leads CRM with followup logging, KYC approvals, and escrow releases
- `src/pages/Classroom/ClassroomPage.jsx`: Live Agora RTC token generation and Microsoft Teams meeting link generator
- `src/components/CategoryGrid/CategoryGrid.jsx`: Direct `/subjects` API integration with grouped discipline counts

### Summary of Changes:
- Completely eliminated static hardcoded arrays across all dashboards, the master calendar, and marketplace discovery tools.
- Connected all interactive forms and buttons directly to live Laravel API endpoints.
- Successfully verified with `npm run build` (0 compilation errors).


## [2026-08-24 04:37:09] - Implemented Zero-Signup Microsoft Teams Meeting Engine
### Files Modified:
- `backend/app/Http/Controllers/Api/ClassController.php`: Upgraded `generateTeamsMeeting` method to use Microsoft Graph API with Azure Object ID resolution, `scope: everyone` lobby bypass, and `allowedPresenters: everyone`.

### Summary of Changes:
- Integrated the exact Microsoft Teams meeting provisioning architecture from LMS into the marketplace backend.
- Neither students nor teachers require a Microsoft account, login, license, or desktop app download.
- Successfully deployed to `/home/u181357068/domains/scholarb.org/marketplace/app/Http/Controllers/Api/ClassController.php` and verified with live API test (Status 200 OK with active `joinWebUrl`).


## [2026-08-24 04:58:54] - Implemented Full UrbanPro Feature-Wise Suite & Dynamic Backend
### Files Created & Modified:
- `backend/app/Models/LearningRequirement.php`: Eloquent model for posted student/parent requirements.
- `backend/app/Models/TutorProposal.php`: Eloquent model for custom tutor bids and proposals.
- `backend/app/Models/Message.php`: Eloquent model for in-app direct messaging.
- `backend/app/Models/AssignmentVault.php`: Eloquent model for homework, worksheets, and teacher grading.
- `backend/app/Models/CoursePackage.php`: Eloquent model for multi-class packages.
- `backend/app/Http/Controllers/Api/RequirementController.php`: RESTful controller for requirement publishing, feed viewing, and proposal acceptance.
- `backend/app/Http/Controllers/Api/MessageController.php`: Controller for conversations and message threads.
- `backend/app/Http/Controllers/Api/AssignmentController.php`: Controller for study materials, homework submissions, and grading.
- `backend/app/Http/Controllers/Api/PackageController.php`: Controller for packages and milestone escrow purchases.
- `backend/routes/api.php`: Registered all UrbanPro RESTful routes.
- `frontend/src/components/PostRequirementModal/`: Multi-step requirement poster wizard.
- `frontend/src/components/QuoteProposalModal/`: Zero-lead-fee custom quote generator.
- `frontend/src/components/MessagingInbox/`: In-app 1-on-1 direct messaging.
- `frontend/src/components/HomeworkVault/`: Homework and study materials repository.
- `frontend/src/components/PackageSelector/`: Multi-class discounted package selector.
- `frontend/src/layouts/DashboardLayout.jsx` & `DashboardLayout.css`: Collapsible enterprise sidebar layout.
- `frontend/src/pages/Dashboard/`: Completely refactored Student, Teacher, Parent, and Admin dashboards with live API synchronization.

### Summary of Changes:
- 100% dynamic, zero-hardcoded backend integration with 5 new database tables on `u181357068_Serp1`.
- Fully verified with live automated test suite (`test_urbanpro_apis.py`) passing all 9 workflows with 100% success.
- Production build compiled cleanly with `vite build` in 1.21s.
