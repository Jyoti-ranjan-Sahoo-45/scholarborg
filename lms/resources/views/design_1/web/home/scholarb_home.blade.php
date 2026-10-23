@extends('design_1.web.layouts.app')

@php $appHeader = true; $appFooter = true; @endphp

@push('styles_top')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/default/css/scholarb.css?v={{ time() }}">
@endpush

@section('content')
<div class="sb-page">

{{-- ============ ANNOUNCEMENT BAR ============ --}}
<div class="sb-announce" id="sbAnnounce">
  <span class="sb-announce-badge">NEW</span>
  <span>Book a FREE demo class today and experience personalized learning with expert tutors.</span>
  <button class="sb-announce-close" onclick="document.getElementById('sbAnnounce').style.display='none'" aria-label="Close">✕</button>
</div>

{{-- ============ NAVBAR ============ --}}
<nav class="sb-nav" role="navigation" aria-label="Main navigation">
  <a href="/" class="sb-nav-logo" aria-label="Scholarb Home">
    <div class="sb-nav-logo-icon">🎓</div>
    <span class="sb-nav-logo-text">SCHOLARB</span>
  </a>

  <a href="/instructor-finder" class="sb-nav-btn-tutors">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
    Find Tutors
  </a>
  <a href="/classes" class="sb-nav-btn-courses">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
    Find Courses
  </a>

  <div class="sb-nav-search">
    <form action="/classes" method="GET">
      <input type="text" name="search" placeholder="Search by subject, topic or exam..." aria-label="Search">
      <span class="sb-nav-search-icon">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
      </span>
    </form>
  </div>

  <div class="sb-nav-right">
    <a href="/pages/features" class="sb-nav-link sb-nav-features-link">
      Features
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
    </a>
    @if(auth()->check())
      <a href="/panel" class="sb-nav-link">Dashboard</a>
      <a href="/logout" class="sb-nav-link">Logout</a>
    @else
      <button class="sb-nav-link" onclick="openModal('login')" style="border:none;background:none;">Login</button>
      <button class="sb-nav-cta" onclick="openModal('signup')">
        <span class="sb-hide-mobile">Join Now for Free</span>
        <span class="sb-show-mobile">Join Free</span>
      </button>
    @endif
  </div>
</nav>

{{-- ============ HERO SECTION ============ --}}
<div class="sb-hero-wrap">
<section class="sb-hero" id="hero">

  {{-- === LEFT COLUMN === --}}
  <div class="sb-hero-left">
    <div class="sb-hero-badge">
      <span class="sb-badge-live-dot"></span>
      <svg class="sb-tab-icon-svg" viewBox="0 0 24 24" style="width:14px;height:14px;stroke:var(--purple)">
        <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
      </svg>
      #1 Platform for Personalized Learning
    </div>

    <h1 class="sb-hero-h1" id="heroH1">
      <span class="h1-line1" id="heroH1Line1">Find the Right Tutor.</span>
      <span class="h1-line2" id="heroH1Line2">Achieve Your Goals.</span>
    </h1>

    <p class="sb-hero-desc" id="heroDesc">Connect with verified expert tutors for 1-to-1 or group classes. Personalized learning that helps you excel.</p>

    <ul class="sb-hero-bullets" id="heroBullets">
      {{-- Populated by JS --}}
    </ul>

    <div class="sb-hero-cta-wrap">
      <div class="sb-hero-cta-row">
        <button class="sb-hero-cta-btn" id="heroCta" onclick="openModal('signup')">
          <span id="heroCtaText">Join Now for Free</span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </button>
        <a href="#hiw" class="sb-hero-cta-secondary">
          See how it works
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        </a>
      </div>
      <p class="sb-hero-free-text">It's free to join and explore. No credit card required.</p>
    </div>

    <!-- Stats row -->
    <div class="sb-hero-stats">
      <div class="sb-hero-stat">
        <div class="sb-hero-stat-num">5,000+</div>
        <div class="sb-hero-stat-label">Students</div>
      </div>
      <div class="sb-hero-stat">
        <div class="sb-hero-stat-num">4.9★</div>
        <div class="sb-hero-stat-label">Avg Rating</div>
      </div>
      <div class="sb-hero-stat">
        <div class="sb-hero-stat-num">98%</div>
        <div class="sb-hero-stat-label">Satisfaction</div>
      </div>
    </div>

    <div class="sb-hero-trust">
      <!-- Avatar cluster -->
      <div class="sb-trust-avatars">
        <div class="sb-avatar-stack">
          <div class="sb-avatar sb-avatar-a">AR</div>
          <div class="sb-avatar sb-avatar-b">PK</div>
          <div class="sb-avatar sb-avatar-c">SM</div>
          <div class="sb-avatar sb-avatar-d">RI</div>
          <div class="sb-avatar sb-avatar-more">+</div>
        </div>
        <div class="sb-avatar-text">Join <strong>5,000+</strong> <span>happy learners</span></div>
      </div>
      <!-- Trustpilot row -->
      <div class="sb-trust-row1">
        <div class="sb-trust-stars" aria-label="4.9 out of 5 stars">
          <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
        </div>
        <span class="sb-trust-excellent">Excellent</span>
        <span class="sb-trust-score">4.9 out of 5</span>
        <div class="sb-trust-logo">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="#00B67A"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
          Trustpilot
        </div>
      </div>
    </div>
  </div>
  {{-- === RIGHT COLUMN (Role Card) === --}}
  <div class="sb-hero-right-card">
    {{-- Curved connector lines anchored to card border (stepped L-shapes) --}}
    <div class="sb-card-connectors-wrapper">
      <svg class="sb-card-connectors" viewBox="0 0 180 700" fill="none" xmlns="http://www.w3.org/2000/svg">
        <!-- Main vertical stem from CTA button node up to top line connection -->
        <path d="M 20,630 L 20,280" stroke="#5B4BDB" stroke-width="1.5" stroke-dasharray="5 4" opacity="0.35"/>

        <!-- Single source node at x=20, y=630 (aligned with CTA button) -->
        <circle cx="20" cy="630" r="5" fill="#5B4BDB" opacity="0.8"/>

        <!-- Top L-curve (from card at y=140 to vertical line at y=280) -->
        <path d="M 180,140 L 140,140 C 125,140 125,155 125,155 L 125,265 C 125,280 110,280 110,280 L 20,280" stroke="#5B4BDB" stroke-width="1.5" stroke-dasharray="5 4" opacity="0.35"/>
        <circle cx="180" cy="140" r="4.5" fill="#5B4BDB" opacity="0.8"/>

        <!-- Middle L-curve (from card at y=350 to vertical line at y=440) -->
        <path d="M 180,350 L 140,350 C 125,350 125,365 125,365 L 125,425 C 125,440 110,440 110,440 L 20,440" stroke="#5B4BDB" stroke-width="1.5" stroke-dasharray="5 4" opacity="0.35"/>
        <circle cx="180" cy="350" r="4.5" fill="#5B4BDB" opacity="0.8"/>

        <!-- Bottom L-curve (from card at y=490 to vertical line at y=630) -->
        <path d="M 180,490 L 140,490 C 125,490 125,505 125,505 L 125,615 C 125,630 110,630 110,630 L 20,630" stroke="#5B4BDB" stroke-width="1.5" stroke-dasharray="5 4" opacity="0.35"/>
        <circle cx="180" cy="490" r="4.5" fill="#5B4BDB" opacity="0.8"/>
      </svg>
    </div>
    {{-- Card Header --}}
    <div class="sb-hero-card-header">
      <div class="sb-hero-card-title">Choose Your Role &amp; Get Started</div>
      <div class="sb-hero-card-sub">Get a personalized Scholarb experience designed for your learning or teaching journey</div>
    </div>

    {{-- Role Tabs with SVG icons --}}
    <div class="sb-role-tabs" role="tablist" aria-label="User role selector">
      <button class="sb-role-tab active" id="tab-students" role="tab" aria-selected="true" onclick="switchRole('students')">
        {{-- Student person icon --}}
        <svg class="sb-tab-icon-svg" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
        Students
      </button>
      <button class="sb-role-tab" id="tab-parents" role="tab" aria-selected="false" onclick="switchRole('parents')">
        {{-- Parent/family icon --}}
        <svg class="sb-tab-icon-svg" viewBox="0 0 24 24"><circle cx="9" cy="7" r="3"/><path d="M3 18c0-3.3 2.7-6 6-6"/><circle cx="16" cy="9" r="2.5"/><path d="M12.5 18c0-2.8 1.6-5 3.5-5s3.5 2.2 3.5 5"/></svg>
        Parents
      </button>
      <button class="sb-role-tab" id="tab-teachers" role="tab" aria-selected="false" onclick="switchRole('teachers')">
        {{-- Graduation cap icon --}}
        <svg class="sb-tab-icon-svg" viewBox="0 0 24 24"><path d="M22 10.5 12 5 2 10.5l10 5.5 10-5.5Z"/><path d="M6 12.5v4c1.4 1 3.7 2 6 2s4.6-1 6-2v-4"/><line x1="22" y1="10.5" x2="22" y2="16"/></svg>
        Teachers
      </button>
    </div>

    {{-- Card Body: illustration + feature cards --}}
    <div class="sb-hero-card-body">
      {{-- Illustration Column --}}
      <div class="sb-hero-illus-col">
        <div class="sb-hero-illus-blob"></div>
        <img id="roleIllus" src="/assets/default/img/scholarb_student_hero.png" alt="Student illustration" loading="eager">
      </div>

      {{-- Feature Cards Column --}}
      <div class="sb-hero-features-col" id="roleFeatureCards">
        {{-- Populated by JS --}}
      </div>
    </div>

    {{-- Card Footer: dots + arrow --}}
    <div class="sb-hero-card-footer">
      <div class="sb-carousel-dots" id="carouselDots" aria-label="Carousel position">
        <div class="sb-dot active"></div>
        <div class="sb-dot"></div>
        <div class="sb-dot"></div>
      </div>
      <button class="sb-carousel-arrow" onclick="rotateRole()" aria-label="Next role">
        <svg class="sb-arrow-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;transition:transform 0.25s ease;"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
      </button>
    </div>
  </div>

</section>
</div>{{-- .sb-hero-wrap --}}


{{-- ============ HOW IT WORKS ============ --}}
<section class="sb-hiw" id="hiw" aria-labelledby="hiwTitle">
  <div class="sb-hiw-inner">
    <p class="sb-sec-pre">HOW IT WORKS</p>
    <h2 class="sb-sec-title" id="hiwTitle">Your Learning Journey in 5 Simple Steps</h2>
    <p class="sb-sec-sub" id="hiwSubtitle">Getting the right tutor takes minutes. Here's how Scholarb works.</p>
    <div class="sb-hiw-steps-container">
      <ol class="sb-hiw-steps" id="hiwSteps" role="list">
        {{-- Populated by JS --}}
      </ol>
    </div>
  </div>
</section>

{{-- ============ BOARDS / EXAMS ============ --}}
<section class="sb-boards" id="boardsSection">
  <div class="sb-boards-inner">
    <h2 class="sb-boards-title" id="boardsTitle">What are you learning?</h2>
    <p class="sb-boards-sub" id="boardsSub">Choose your board or exam category to get started.</p>
    <div class="sb-boards-grid" id="boardsGrid">
      {{-- Populated by JS --}}
    </div>
    <button class="sb-boards-view-btn" id="boardsViewBtn">
      View All Boards &amp; Exams
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
    </button>
  </div>
</section>

{{-- ============ FIND YOUR PERFECT TUTOR ============ --}}
<section class="sb-tutors-sec" id="tutorsSection">
  <div class="sb-tutors-inner">
    <div class="sb-tutors-header">
      <span class="sb-tutors-badge">EXPERT INSTRUCTORS</span>
      <h2 class="sb-tutors-title">Find Your Perfect Tutor</h2>
      <p class="sb-tutors-sub">Browse top-rated expert tutors, check verified credentials, and book your personalized live demo session today.</p>
    </div>

    {{-- Grid / Carousel Container --}}
    <div class="sb-tutors-grid" id="tutorsGrid">
      {{-- Loading Skeleton initially --}}
      <div class="sb-tutor-card sb-tutor-skeleton">
        <div class="sb-tutor-sk-avatar"></div>
        <div class="sb-tutor-sk-line short"></div>
        <div class="sb-tutor-sk-line medium"></div>
        <div class="sb-tutor-sk-line long"></div>
      </div>
      <div class="sb-tutor-card sb-tutor-skeleton">
        <div class="sb-tutor-sk-avatar"></div>
        <div class="sb-tutor-sk-line short"></div>
        <div class="sb-tutor-sk-line medium"></div>
        <div class="sb-tutor-sk-line long"></div>
      </div>
      <div class="sb-tutor-card sb-tutor-skeleton">
        <div class="sb-tutor-sk-avatar"></div>
        <div class="sb-tutor-sk-line short"></div>
        <div class="sb-tutor-sk-line medium"></div>
        <div class="sb-tutor-sk-line long"></div>
      </div>
      <div class="sb-tutor-card sb-tutor-skeleton">
        <div class="sb-tutor-sk-avatar"></div>
        <div class="sb-tutor-sk-line short"></div>
        <div class="sb-tutor-sk-line medium"></div>
        <div class="sb-tutor-sk-line long"></div>
      </div>
    </div>

    <div class="sb-tutors-footer">
      <a href="/instructor-finder" class="sb-tutors-view-btn">
        <span>View All Tutors</span>
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </a>
    </div>
  </div>
</section>

{{-- ============ PRODUCT SHOWCASE / DASHBOARD PREVIEW ============ --}}
<section class="sb-showcase-sec" id="showcaseSection">
  <div class="sb-showcase-inner">
    <div class="sb-showcase-header">
      <span class="sb-showcase-badge">PRODUCT SHOWCASE</span>
      <h2 class="sb-showcase-title">Everything You Need to Learn in One Place</h2>
      <p class="sb-showcase-sub">Experience Scholarb's interactive student portal — track your learning progress, join live 1-on-1 sessions, manage assignments, and message tutors in real time.</p>
    </div>

    <div class="sb-showcase-grid">
      {{-- LEFT: Authentic & Pixel-Perfect SaaS Student Dashboard UI Window --}}
      <div class="sb-dash-preview-wrap">
        {{-- Floating Badges --}}
        <div class="sb-float-badge sb-float-badge-top">
          <span class="sb-float-icon">★</span>
          <div class="sb-float-text">
            <strong>4.9/5 Rating</strong>
            <small>Over 10k+ Live 1-on-1 Sessions</small>
          </div>
        </div>

        <div class="sb-float-badge sb-float-badge-bottom">
          <span class="sb-float-icon">🔒</span>
          <div class="sb-float-text">
            <strong>End-to-End Encrypted</strong>
            <small>Direct &amp; Secure Learning Portal</small>
          </div>
        </div>

        {{-- Main Dashboard Application Window --}}
        <div class="sb-dash-card">
          {{-- Mac OS / SaaS App Header Bar --}}
          <div class="sb-dash-bar">
            <div class="sb-dash-dots">
              <span class="sb-dot-red"></span>
              <span class="sb-dot-yellow"></span>
              <span class="sb-dot-green"></span>
            </div>
            <div class="sb-dash-path">Scholarb Workspace — Student Portal v2.4</div>
            <div class="sb-dash-status"><span class="sb-pulse-dot"></span> Live Sync</div>
          </div>

          {{-- SaaS 2-Column Application Shell --}}
          <div class="sb-app-shell">
            {{-- Left Navigation Sidebar --}}
            <aside class="sb-app-sidebar">
              <div class="sb-sidebar-brand">
                <span class="sb-brand-logo">🎓</span>
                <span class="sb-brand-name">Scholarb</span>
              </div>
              
              <nav class="sb-sidebar-menu">
                <a href="javascript:void(0)" class="sb-nav-item active" data-view="dashboard" onclick="sbSwitchView('dashboard',this)">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                  <span>Dashboard</span>
                </a>
                <a href="javascript:void(0)" class="sb-nav-item" data-view="courses" onclick="sbSwitchView('courses',this)">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                  <span>My Courses</span>
                </a>
                <a href="javascript:void(0)" class="sb-nav-item" data-view="tutors" onclick="sbSwitchView('tutors',this)">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                  <span>Find Tutors</span>
                </a>
                <a href="javascript:void(0)" class="sb-nav-item" data-view="live" onclick="sbSwitchView('live',this)">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
                  <span>Live Sessions</span>
                  <span class="sb-nav-pill live">2 LIVE</span>
                </a>
                <a href="javascript:void(0)" class="sb-nav-item" data-view="assignments" onclick="sbSwitchView('assignments',this)">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg>
                  <span>Assignments</span>
                </a>
                <a href="javascript:void(0)" class="sb-nav-item" data-view="messages" onclick="sbSwitchView('messages',this)">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                  <span>Messages</span>
                  <span class="sb-nav-pill" id="sbMsgPill">3 NEW</span>
                </a>
                <a href="javascript:void(0)" class="sb-nav-item" data-view="analytics" onclick="sbSwitchView('analytics',this)">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                  <span>Analytics</span>
                </a>
              </nav>

              <div class="sb-sidebar-user">
                <div class="sb-sb-avatar">👨‍🎓</div>
                <div class="sb-sb-info">
                  <strong>Alex Rivera</strong>
                  <small>Premium Student</small>
                </div>
              </div>
            </aside>

            {{-- Main Application Workspace --}}
            <main class="sb-app-main">
              {{-- Top Header Bar --}}
              {{-- Dynamic View Label --}}
              <div class="sb-dash-topbar">
                <div class="sb-dash-search">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                  <span>Search subjects, assignments, tutors...</span>
                </div>
                <div class="sb-topbar-actions">
                  <span class="sb-tb-icon" title="Notifications">🔔<span class="sb-tb-badge">3</span></span>
                  <span class="sb-tb-icon" title="Messages">💬</span>
                </div>
              </div>

              {{-- 4 Metric Cards Row --}}
              <div class="sb-metrics-grid">
                <div class="sb-metric-card">
                  <div class="sb-metric-top">
                    <span class="sb-metric-icon purple">📚</span>
                    <span class="sb-metric-trend pos">+4 this mo</span>
                  </div>
                  <h4 class="sb-metric-val" id="dashCoursesCount">31</h4>
                  <p class="sb-metric-label">Enrolled Courses</p>
                </div>

                <div class="sb-metric-card">
                  <div class="sb-metric-top">
                    <span class="sb-metric-icon blue">⏱️</span>
                    <span class="sb-metric-trend pos">+12%</span>
                  </div>
                  <h4 class="sb-metric-val">38 hrs</h4>
                  <p class="sb-metric-label">Hours Learned</p>
                </div>

                <div class="sb-metric-card">
                  <div class="sb-metric-top">
                    <span class="sb-metric-icon green">🎥</span>
                    <span class="sb-metric-trend live">Live Today</span>
                  </div>
                  <h4 class="sb-metric-val">2 Sessions</h4>
                  <p class="sb-metric-label">Next in 25 mins</p>
                </div>

                <div class="sb-metric-card">
                  <div class="sb-metric-top">
                    <span class="sb-metric-icon gold">🏆</span>
                    <span class="sb-metric-trend pos">Top 5%</span>
                  </div>
                  <h4 class="sb-metric-val">94%</h4>
                  <p class="sb-metric-label">Avg. Test Score</p>
                </div>
              </div>

              {{-- Main Split View: Left Column (Live Session & Progress) + Right Column (Schedule & Messages) --}}
              <div class="sb-workspace-split">
                <div class="sb-ws-col-left">
                  {{-- Hero Upcoming Live Session Banner --}}
                  <div class="sb-dash-live-card">
                    <div class="sb-dash-live-header">
                      <span class="sb-live-tag">UPCOMING LIVE SESSION</span>
                      <span class="sb-live-time" id="dashLiveTime">Today, 5:00 PM (In 25m)</span>
                    </div>
                    <div class="sb-dash-live-body">
                      <div>
                        <h5 class="sb-dash-live-title" id="dashLiveTitle">Advanced Physics &amp; Quantum Mechanics</h5>
                        <p class="sb-dash-live-tutor" id="dashLiveTutor">Instructor: Dr. Jessica Wray</p>
                      </div>
                      <button class="sb-dash-join-btn">
                        <span>Join Classroom</span>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                      </button>
                    </div>
                  </div>

                  {{-- Course Progress Breakdown --}}
                  <div class="sb-dash-progress-sec">
                    <h5 class="sb-dash-subhead">Active Subject Completion</h5>
                    
                    <div class="sb-dash-prog-item">
                      <div class="sb-prog-label">
                        <span>Mathematics &amp; Calculus</span>
                        <strong style="color: #5C4EE8;">92% (14/15 Lessons)</strong>
                      </div>
                      <div class="sb-prog-track">
                        <div class="sb-prog-bar" style="width: 92%; background: linear-gradient(90deg, #5C4EE8, #8B5CF6);"></div>
                      </div>
                    </div>

                    <div class="sb-dash-prog-item">
                      <div class="sb-prog-label">
                        <span>Organic Chemistry</span>
                        <strong style="color: #10B981;">78% (11/14 Lessons)</strong>
                      </div>
                      <div class="sb-prog-track">
                        <div class="sb-prog-bar" style="width: 78%; background: linear-gradient(90deg, #10B981, #34D399);"></div>
                      </div>
                    </div>
                  </div>
                </div>

                {{-- Right Column: Schedule & Messages --}}
                <div class="sb-ws-col-right">
                  <div class="sb-side-widget">
                    <h5 class="sb-dash-subhead">Today's Schedule</h5>
                    <div class="sb-timeline-list">
                      <div class="sb-tl-item active">
                        <span class="sb-tl-time">17:00</span>
                        <div class="sb-tl-content">
                          <strong>Physics Live Session</strong>
                          <small>Dr. Jessica Wray</small>
                        </div>
                      </div>
                      <div class="sb-tl-item">
                        <span class="sb-tl-time">19:00</span>
                        <div class="sb-tl-content">
                          <strong>Calculus Quiz #4 Due</strong>
                          <small>Online Assessment</small>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="sb-side-widget margin-top">
                    <h5 class="sb-dash-subhead">Recent Messages</h5>
                    <div class="sb-msg-list">
                      <div class="sb-msg-item">
                        <span class="sb-msg-avatar">👩‍🏫</span>
                        <div class="sb-msg-info">
                          <strong>Dr. Jessica Wray</strong>
                          <small>"Great job on Chapter 4 problem set!"</small>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </main>
          </div>
        </div>
      </div>

      {{-- HIDDEN DYNAMIC VIEW TEMPLATES (rendered by JS) --}}
      <div id="sbViewTemplates" style="display:none!important;" aria-hidden="true">

        {{-- MY COURSES VIEW --}}
        <div id="tpl-courses">
          <div class="sb-view-header">
            <h3 class="sb-view-title">📚 My Courses <span class="sb-view-count">31 Enrolled</span></h3>
          </div>
          <div class="sb-courses-grid">
            <div class="sb-course-card">
              <div class="sb-course-subj" style="background:#F3F0FF;color:#5C4EE8;">Physics</div>
              <h4 class="sb-course-name">Advanced Physics &amp; Quantum Mechanics</h4>
              <p class="sb-course-instructor">👩‍🏫 Dr. Jessica Wray</p>
              <div class="sb-prog-label" style="font-size:10px;"><span>Progress</span><strong style="color:#5C4EE8;">92%</strong></div>
              <div class="sb-prog-track"><div class="sb-prog-bar" style="width:92%;background:linear-gradient(90deg,#5C4EE8,#8B5CF6);"></div></div>
              <div class="sb-course-meta">14/15 Lessons</div>
              <button class="sb-course-btn purple">Continue Learning →</button>
            </div>
            <div class="sb-course-card">
              <div class="sb-course-subj" style="background:#ECFDF5;color:#10B981;">Mathematics</div>
              <h4 class="sb-course-name">Calculus III &amp; Linear Algebra</h4>
              <p class="sb-course-instructor">👨‍🏫 Prof. John Davis</p>
              <div class="sb-prog-label" style="font-size:10px;"><span>Progress</span><strong style="color:#10B981;">78%</strong></div>
              <div class="sb-prog-track"><div class="sb-prog-bar" style="width:78%;background:linear-gradient(90deg,#10B981,#34D399);"></div></div>
              <div class="sb-course-meta">11/14 Lessons</div>
              <button class="sb-course-btn green">Continue Learning →</button>
            </div>
            <div class="sb-course-card">
              <div class="sb-course-subj" style="background:#FEF3C7;color:#F59E0B;">Chemistry</div>
              <h4 class="sb-course-name">Organic Chemistry &amp; Molecular Biology</h4>
              <p class="sb-course-instructor">👩‍🏫 Dr. Sarah Lee</p>
              <div class="sb-prog-label" style="font-size:10px;"><span>Progress</span><strong style="color:#F59E0B;">65%</strong></div>
              <div class="sb-prog-track"><div class="sb-prog-bar" style="width:65%;background:linear-gradient(90deg,#F59E0B,#FCD34D);"></div></div>
              <div class="sb-course-meta">9/14 Lessons</div>
              <button class="sb-course-btn gold">Continue Learning →</button>
            </div>
            <div class="sb-course-card">
              <div class="sb-course-subj" style="background:#EFF6FF;color:#3B82F6;">Computer Science</div>
              <h4 class="sb-course-name">Data Structures &amp; Algorithms</h4>
              <p class="sb-course-instructor">👨‍🏫 Prof. Mark Taylor</p>
              <div class="sb-prog-label" style="font-size:10px;"><span>Progress</span><strong style="color:#3B82F6;">45%</strong></div>
              <div class="sb-prog-track"><div class="sb-prog-bar" style="width:45%;background:linear-gradient(90deg,#3B82F6,#60A5FA);"></div></div>
              <div class="sb-course-meta">6/13 Lessons</div>
              <button class="sb-course-btn blue">Continue Learning →</button>
            </div>
          </div>
        </div>

        {{-- FIND TUTORS VIEW --}}
        <div id="tpl-tutors">
          <div class="sb-view-header">
            <h3 class="sb-view-title">👨‍🏫 Find Tutors <span class="sb-view-count">8 Available Now</span></h3>
          </div>
          <div class="sb-tutors-mini-grid">
            <div class="sb-tutor-mini-card">
              <div class="sb-tutor-mini-top">
                <span class="sb-tutor-mini-avatar">👩‍🏫</span>
                <div>
                  <strong>Dr. Jessica Wray</strong>
                  <small>Advanced Physics</small>
                </div>
                <span class="sb-tutor-mini-rate">$45/hr</span>
              </div>
              <div class="sb-tutor-mini-rating">★★★★★ <span>4.9</span> (128 reviews)</div>
              <button class="sb-tutor-mini-btn">Book Session →</button>
            </div>
            <div class="sb-tutor-mini-card">
              <div class="sb-tutor-mini-top">
                <span class="sb-tutor-mini-avatar">👨‍🏫</span>
                <div>
                  <strong>Prof. John Davis</strong>
                  <small>Calculus &amp; Algebra</small>
                </div>
                <span class="sb-tutor-mini-rate">$35/hr</span>
              </div>
              <div class="sb-tutor-mini-rating">★★★★★ <span>4.8</span> (94 reviews)</div>
              <button class="sb-tutor-mini-btn">Book Session →</button>
            </div>
            <div class="sb-tutor-mini-card">
              <div class="sb-tutor-mini-top">
                <span class="sb-tutor-mini-avatar">👩‍🏫</span>
                <div>
                  <strong>Dr. Sarah Lee</strong>
                  <small>Organic Chemistry</small>
                </div>
                <span class="sb-tutor-mini-rate">$40/hr</span>
              </div>
              <div class="sb-tutor-mini-rating">★★★★½ <span>4.7</span> (76 reviews)</div>
              <button class="sb-tutor-mini-btn">Book Session →</button>
            </div>
            <div class="sb-tutor-mini-card">
              <div class="sb-tutor-mini-top">
                <span class="sb-tutor-mini-avatar">👨‍🏫</span>
                <div>
                  <strong>Prof. Mark Taylor</strong>
                  <small>CS &amp; Algorithms</small>
                </div>
                <span class="sb-tutor-mini-rate">$50/hr</span>
              </div>
              <div class="sb-tutor-mini-rating">★★★★★ <span>5.0</span> (42 reviews)</div>
              <button class="sb-tutor-mini-btn">Book Session →</button>
            </div>
          </div>
        </div>

        {{-- LIVE SESSIONS VIEW --}}
        <div id="tpl-live">
          <div class="sb-view-header">
            <h3 class="sb-view-title">🎥 Live Sessions <span class="sb-view-count live">2 LIVE NOW</span></h3>
          </div>
          <div class="sb-live-list">
            <div class="sb-live-item active-now">
              <div class="sb-live-badge-dot">🔴 LIVE NOW</div>
              <div class="sb-live-item-body">
                <h4>Advanced Physics &amp; Quantum Mechanics</h4>
                <p>Dr. Jessica Wray • 60 min session • 12 students online</p>
              </div>
              <button class="sb-live-enter-btn">Enter Live Room →</button>
            </div>
            <div class="sb-live-item upcoming">
              <div class="sb-live-badge-dot upcoming">⏳ In 25 mins</div>
              <div class="sb-live-item-body">
                <h4>Calculus III — Integration Techniques</h4>
                <p>Prof. John Davis • 45 min session • Starting soon</p>
              </div>
              <button class="sb-live-enter-btn outline">Set Reminder →</button>
            </div>
            <div class="sb-live-item scheduled">
              <div class="sb-live-badge-dot scheduled">📅 Tomorrow 10:00 AM</div>
              <div class="sb-live-item-body">
                <h4>Organic Chemistry — Molecular Bonding</h4>
                <p>Dr. Sarah Lee • 60 min session</p>
              </div>
              <button class="sb-live-enter-btn outline">Add to Calendar →</button>
            </div>
          </div>
        </div>

        {{-- ASSIGNMENTS VIEW --}}
        <div id="tpl-assignments">
          <div class="sb-view-header">
            <h3 class="sb-view-title">📝 Assignments <span class="sb-view-count">2 Pending</span></h3>
          </div>
          <div class="sb-assign-list">
            <div class="sb-assign-item pending">
              <span class="sb-assign-status pending">🟡 Due Tomorrow</span>
              <div class="sb-assign-body">
                <h4>Calculus Problem Set 4</h4>
                <p>Prof. John Davis • 5 problems remaining • Est. 45 mins</p>
              </div>
              <button class="sb-assign-btn">Open Assignment →</button>
            </div>
            <div class="sb-assign-item pending">
              <span class="sb-assign-status pending">🟡 Due in 3 Days</span>
              <div class="sb-assign-body">
                <h4>Physics Lab Report — Wave Functions</h4>
                <p>Dr. Jessica Wray • Written report • 2000 words min</p>
              </div>
              <button class="sb-assign-btn">Open Assignment →</button>
            </div>
            <div class="sb-assign-item done">
              <span class="sb-assign-status done">🟢 Graded: 98/100</span>
              <div class="sb-assign-body">
                <h4>Algebra Problem Set 3</h4>
                <p>Prof. John Davis • Submitted 2 days ago • Excellent work!</p>
              </div>
              <button class="sb-assign-btn outline">View Feedback →</button>
            </div>
            <div class="sb-assign-item done">
              <span class="sb-assign-status done">🟢 Passed: 94%</span>
              <div class="sb-assign-body">
                <h4>Organic Chemistry Midterm</h4>
                <p>Dr. Sarah Lee • Top 10% in class • Excellent performance</p>
              </div>
              <button class="sb-assign-btn outline">View Report →</button>
            </div>
          </div>
        </div>

        {{-- MESSAGES VIEW --}}
        <div id="tpl-messages">
          <div class="sb-view-header">
            <h3 class="sb-view-title">💬 Messages <span class="sb-view-count">3 Unread</span></h3>
          </div>
          <div class="sb-chat-layout">
            <div class="sb-chat-inbox">
              <div class="sb-chat-thread active">
                <span class="sb-chat-avatar">👩‍🏫</span>
                <div class="sb-chat-thread-info">
                  <strong>Dr. Jessica Wray</strong>
                  <small>"Great job on Chapter 4 problem set!"</small>
                </div>
                <span class="sb-chat-unread">2</span>
              </div>
              <div class="sb-chat-thread">
                <span class="sb-chat-avatar">👨‍🏫</span>
                <div class="sb-chat-thread-info">
                  <strong>Prof. John Davis</strong>
                  <small>"Reminder: Demo class is confirmed."</small>
                </div>
                <span class="sb-chat-unread">1</span>
              </div>
              <div class="sb-chat-thread">
                <span class="sb-chat-avatar">👩‍🏫</span>
                <div class="sb-chat-thread-info">
                  <strong>Dr. Sarah Lee</strong>
                  <small>"Midterm results are ready!"</small>
                </div>
                <span class="sb-chat-unread">0</span>
              </div>
            </div>
            <div class="sb-chat-window">
              <div class="sb-chat-bubble received">"Great job on Chapter 4 problem set! You are in the top 5% of my students this semester 🌟"</div>
              <div class="sb-chat-bubble sent">"Thank you Dr. Wray! Really appreciate the feedback."</div>
              <div class="sb-chat-bubble received">"Keep it up, see you in today's live session at 5 PM!"</div>
              <div class="sb-chat-input"><span>Type a reply...</span><button>Send 📤</button></div>
            </div>
          </div>
        </div>

        {{-- ANALYTICS VIEW --}}
        <div id="tpl-analytics">
          <div class="sb-view-header">
            <h3 class="sb-view-title">📊 Performance &amp; Analytics</h3>
          </div>
          <div class="sb-analytics-grid">
            <div class="sb-analytics-chart-card">
              <h5 class="sb-dash-subhead">Weekly Study Hours</h5>
              <div class="sb-bar-chart">
                <div class="sb-bar-col"><div class="sb-bar" style="height:55%;background:#5C4EE8;"></div><span>Mon</span><small>4h</small></div>
                <div class="sb-bar-col"><div class="sb-bar" style="height:75%;background:#5C4EE8;"></div><span>Tue</span><small>6h</small></div>
                <div class="sb-bar-col"><div class="sb-bar" style="height:65%;background:#5C4EE8;"></div><span>Wed</span><small>5h</small></div>
                <div class="sb-bar-col"><div class="sb-bar" style="height:90%;background:#8B5CF6;"></div><span>Thu</span><small>7h</small></div>
                <div class="sb-bar-col"><div class="sb-bar" style="height:100%;background:#8B5CF6;"></div><span>Fri</span><small>8h</small></div>
                <div class="sb-bar-col"><div class="sb-bar" style="height:40%;background:#CBD5E1;"></div><span>Sat</span><small>3h</small></div>
                <div class="sb-bar-col"><div class="sb-bar" style="height:20%;background:#CBD5E1;"></div><span>Sun</span><small>1.5h</small></div>
              </div>
            </div>
            <div class="sb-analytics-stats-col">
              <div class="sb-analytics-stat">
                <span class="sb-as-icon" style="background:#F3F0FF;">📈</span>
                <div><strong>94%</strong><small>Avg Test Score</small></div>
              </div>
              <div class="sb-analytics-stat">
                <span class="sb-as-icon" style="background:#ECFDF5;">⏱️</span>
                <div><strong>38 hrs</strong><small>Total Study Time</small></div>
              </div>
              <div class="sb-analytics-stat">
                <span class="sb-as-icon" style="background:#FEF3C7;">🏆</span>
                <div><strong>Top 5%</strong><small>Class Rank</small></div>
              </div>
              <div class="sb-analytics-stat">
                <span class="sb-as-icon" style="background:#EFF6FF;">✅</span>
                <div><strong>12/14</strong><small>Assignments Done</small></div>
              </div>
            </div>
          </div>
        </div>

      </div>

      {{-- RIGHT: Platform Feature Showcase List & CTA --}}
      <div class="sb-showcase-features">
        <div class="sb-feat-item">
          <div class="sb-feat-icon-box">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          </div>
          <div>
            <h4 class="sb-feat-title">Easy Tutor Search</h4>
            <p class="sb-feat-desc">Find verified expert tutors tailored to your subject, curriculum, and budget in seconds.</p>
          </div>
        </div>

        <div class="sb-feat-item">
          <div class="sb-feat-icon-box">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
          </div>
          <div>
            <h4 class="sb-feat-title">Interactive Live Classes</h4>
            <p class="sb-feat-desc">Join high-definition 1-on-1 and group live sessions with digital whiteboards and video.</p>
          </div>
        </div>

        <div class="sb-feat-item">
          <div class="sb-feat-icon-box">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
          </div>
          <div>
            <h4 class="sb-feat-title">Real-Time Progress Tracking</h4>
            <p class="sb-feat-desc">Monitor course completion, test scores, and academic milestones with live analytics.</p>
          </div>
        </div>

        <div class="sb-feat-item">
          <div class="sb-feat-icon-box">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          </div>
          <div>
            <h4 class="sb-feat-title">Smart Assignments &amp; Homework</h4>
            <p class="sb-feat-desc">Submit assignments, receive detailed feedback, and track grades effortlessly.</p>
          </div>
        </div>

        <div class="sb-feat-item">
          <div class="sb-feat-icon-box">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          </div>
          <div>
            <h4 class="sb-feat-title">Direct Secure Messaging</h4>
            <p class="sb-feat-desc">Ask questions, request demo feedback, and chat directly with your tutor anytime.</p>
          </div>
        </div>

        <div class="sb-feat-item">
          <div class="sb-feat-icon-box">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          </div>
          <div>
            <h4 class="sb-feat-title">Flexible Class Scheduling</h4>
            <p class="sb-feat-desc">Book, reschedule, or manage free demo sessions synced with your personal calendar.</p>
          </div>
        </div>

        <div class="sb-showcase-action">
          <a href="/instructor-finder" class="sb-showcase-btn">
            <span>Explore Platform</span>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ============ SUCCESS STORIES ============ --}}
<section class="sb-stories" id="storiesSection">
  <div class="sb-stories-inner">
    <div class="sb-stories-header">
      <p class="sb-stories-pre" id="storiesPre">STUDENT SUCCESS STORIES</p>
      <h2 class="sb-stories-title">Real Results. Real Success.</h2>
    </div>
    <div class="sb-stories-body">
      <div style="position:relative;">
        <button class="sb-stories-nav-left" onclick="prevTestimonials()" aria-label="Previous">←</button>
        <div class="sb-testimonials-row" id="testimonialsRow">
          {{-- Populated by JS --}}
        </div>
        <button class="sb-stories-nav-right" onclick="nextTestimonials()" aria-label="Next">→</button>
      </div>
      <div class="sb-stats-grid" id="statsGrid">
        {{-- Populated by JS --}}
      </div>
    </div>
  </div>
</section>

{{-- ============ CTA BANNER ============ --}}
<section class="sb-cta-banner" id="ctaBanner">
  <div class="sb-cta-banner-inner">
    <div class="sb-cta-box">
      <div class="sb-cta-content">
        <span class="sb-cta-badge" id="ctaPre">READY TO INSPIRE &amp; MAKE AN IMPACT?</span>
        <h2 class="sb-cta-title" id="ctaTitle">Join Scholarb as a Tutor</h2>
        <p class="sb-cta-desc" id="ctaDesc">Share your knowledge, empower eager students worldwide, and build your teaching career on your own schedule.</p>
      </div>

      <div class="sb-cta-action">
        <button class="sb-cta-btn" id="ctaBtn" onclick="openModal('signup')">
          <span id="ctaBtnSpan">Join as a Tutor for Free</span>
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </button>
        <span class="sb-cta-free">Free to join • No credit card required</span>
      </div>
    </div>
  </div>
</section>

{{-- ============ FAQ ============ --}}
<section class="sb-faq" id="faqSection">
  <div class="sb-faq-inner">
    <div class="sb-faq-header">
      <span class="sb-faq-badge">HELP &amp; SUPPORT</span>
      <h2 class="sb-faq-title">Frequently Asked Questions</h2>
      <p class="sb-faq-sub">Got questions? We've got answers. Everything you need to know about Scholarb.</p>
    </div>

    <div class="sb-faq-list" id="faqList">
      {{-- Populated by JS --}}
    </div>

    <div class="sb-faq-footer">
      <button class="sb-faq-view-btn" onclick="window.location.href='/contact'">
        <span>View All FAQs</span>
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </button>
      <p class="sb-faq-help-text">Still have questions? <a href="/contact">Contact Support →</a></p>
    </div>
  </div>
</section>

{{-- ============ FOOTER ============ --}}
<footer class="sb-footer" role="contentinfo">
  <div class="sb-footer-inner">
    <div class="sb-footer-grid">
      
      {{-- COLUMN 1 — BRAND --}}
      <div class="sb-footer-brand-col">
        <a href="/" class="sb-footer-logo" aria-label="Scholarb Home">
          <div class="sb-nav-logo-icon">🎓</div>
          <span class="sb-nav-logo-text">SCHOLARB</span>
        </a>
        <p class="sb-footer-brand-desc">Connecting students with expert tutors for personalized learning that delivers real results.</p>
        
        <div class="sb-footer-socials">
          <a href="https://twitter.com" target="_blank" rel="noopener" class="sb-social-btn" aria-label="Twitter / X">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
          </a>
          <a href="https://facebook.com" target="_blank" rel="noopener" class="sb-social-btn" aria-label="Facebook">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
          </a>
          <a href="https://instagram.com" target="_blank" rel="noopener" class="sb-social-btn" aria-label="Instagram">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
          </a>
          <a href="https://linkedin.com" target="_blank" rel="noopener" class="sb-social-btn" aria-label="LinkedIn">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/></svg>
          </a>
          <a href="https://youtube.com" target="_blank" rel="noopener" class="sb-social-btn" aria-label="YouTube">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
          </a>
        </div>
      </div>

      {{-- COLUMN 2 — FOR STUDENTS --}}
      <div class="sb-footer-col sb-footer-accordion open">
        <button class="sb-footer-col-header" onclick="toggleFooterAccordion(this)" aria-expanded="true">
          <h4 class="sb-footer-col-title">For Students</h4>
          <span class="sb-accordion-icon">−</span>
        </button>
        <div class="sb-footer-col-content">
          <ul class="sb-footer-links">
            <li><a href="/instructor-finder" class="sb-footer-link">Find Tutors</a></li>
            <li><a href="/classes" class="sb-footer-link">Find Courses</a></li>
            <li><a href="javascript:void(0)" onclick="openModal('signup')" class="sb-footer-link">Book a Demo</a></li>
            <li><a href="#hiw" class="sb-footer-link">How It Works</a></li>
            <li><a href="/classes" class="sb-footer-link">Student Resources</a></li>
          </ul>
        </div>
      </div>

      {{-- COLUMN 3 — FOR PARENTS --}}
      <div class="sb-footer-col sb-footer-accordion">
        <button class="sb-footer-col-header" onclick="toggleFooterAccordion(this)" aria-expanded="false">
          <h4 class="sb-footer-col-title">For Parents</h4>
          <span class="sb-accordion-icon">+</span>
        </button>
        <div class="sb-footer-col-content">
          <ul class="sb-footer-links">
            <li><a href="/panel" class="sb-footer-link">Parent Dashboard</a></li>
            <li><a href="#hiw" class="sb-footer-link">Track Progress</a></li>
            <li><a href="/classes" class="sb-footer-link">Plans &amp; Pricing</a></li>
            <li><a href="#faqSection" class="sb-footer-link">FAQs</a></li>
          </ul>
        </div>
      </div>

      {{-- COLUMN 4 — FOR TEACHERS --}}
      <div class="sb-footer-col sb-footer-accordion">
        <button class="sb-footer-col-header" onclick="toggleFooterAccordion(this)" aria-expanded="false">
          <h4 class="sb-footer-col-title">For Teachers</h4>
          <span class="sb-accordion-icon">+</span>
        </button>
        <div class="sb-footer-col-content">
          <ul class="sb-footer-links">
            <li><a href="/instructor-finder" class="sb-footer-link">Become a Tutor</a></li>
            <li><a href="/classes" class="sb-footer-link">Tutor Resources</a></li>
            <li><a href="/classes" class="sb-footer-link">Plans &amp; Pricing</a></li>
            <li><a href="/login" class="sb-footer-link">Tutor Login</a></li>
          </ul>
        </div>
      </div>

      {{-- COLUMN 5 — COMPANY & SUPPORT --}}
      <div class="sb-footer-col sb-footer-accordion">
        <button class="sb-footer-col-header" onclick="toggleFooterAccordion(this)" aria-expanded="false">
          <h4 class="sb-footer-col-title">Company &amp; Support</h4>
          <span class="sb-accordion-icon">+</span>
        </button>
        <div class="sb-footer-col-content">
          <ul class="sb-footer-links">
            <li><a href="/pages/about" class="sb-footer-link">About Us</a></li>
            <li><a href="/blog" class="sb-footer-link">Blog</a></li>
            <li><a href="/pages/careers" class="sb-footer-link">Careers</a></li>
            <li><a href="/contact" class="sb-footer-link">Contact Us</a></li>
            <li><a href="/contact" class="sb-footer-link">Help Center</a></li>
            <li><a href="/pages/terms" class="sb-footer-link">Terms of Use</a></li>
            <li><a href="/pages/terms" class="sb-footer-link">Privacy Policy</a></li>
            <li><a href="/pages/terms" class="sb-footer-link">Refund Policy</a></li>
          </ul>
        </div>
      </div>

    </div>{{-- .sb-footer-grid --}}

    {{-- NEWSLETTER ROW --}}
    <div class="sb-footer-newsletter-row">
      <div class="sb-newsletter-info">
        <h4 class="sb-newsletter-title">Stay Updated</h4>
        <p class="sb-newsletter-desc">Subscribe to get learning tips and updates.</p>
      </div>
      <form class="sb-newsletter-form" onsubmit="event.preventDefault(); alert('Thank you for subscribing!');">
        <input type="email" placeholder="Enter your email address..." required aria-label="Email address">
        <button type="submit" class="sb-newsletter-btn">
          <span>Subscribe</span>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </button>
      </form>
    </div>

    {{-- BOTTOM BAR --}}
    <div class="sb-footer-bottom">
      <p class="sb-footer-copy">© {{ date('Y') }} Scholarb. All rights reserved.</p>
      <div class="sb-footer-bottom-links">
        <a href="/pages/terms" class="sb-footer-bottom-link">Privacy Policy</a>
        <span class="sb-footer-dot">•</span>
        <a href="/pages/terms" class="sb-footer-bottom-link">Terms of Use</a>
        <span class="sb-footer-dot">•</span>
        <a href="/pages/terms" class="sb-footer-bottom-link">Refund Policy</a>
      </div>
    </div>
  </div>
</footer>

{{-- ============ AUTH MODAL ============ --}}
<div class="sb-modal-overlay" id="authModal" onclick="if(event.target===this)closeModal()">
  <div class="sb-modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <button class="sb-modal-close" onclick="closeModal()" aria-label="Close">✕</button>

    <div class="sb-modal-left">
      <h4 class="sb-modal-left-title">Why these steps matter?</h4>
      <p class="sb-modal-left-sub">A simple, secure way to start your Scholarb journey.</p>
      <div class="sb-modal-steps">
        <div class="sb-modal-step">
          <div class="sb-modal-step-num">01</div>
          <div><div class="sb-modal-step-title">Secure &amp; Safe</div><div class="sb-modal-step-desc">Verify your identity and protect your account.</div></div>
        </div>
        <div class="sb-modal-step">
          <div class="sb-modal-step-num">02</div>
          <div><div class="sb-modal-step-title">Personalized Experience</div><div class="sb-modal-step-desc">Get learning recommendations suited to your goals.</div></div>
        </div>
        <div class="sb-modal-step">
          <div class="sb-modal-step-num">03</div>
          <div><div class="sb-modal-step-title">Stay Connected</div><div class="sb-modal-step-desc">Communicate with tutors and receive important updates.</div></div>
        </div>
        <div class="sb-modal-step">
          <div class="sb-modal-step-num">04</div>
          <div><div class="sb-modal-step-title">Track &amp; Improve</div><div class="sb-modal-step-desc">Follow your learning progress and outcomes.</div></div>
        </div>
      </div>
    </div>

    <div class="sb-modal-right">
      <h4 class="sb-modal-right-title" id="modalTitle">Welcome to Scholarb</h4>
      <p class="sb-modal-right-sub" id="modalSub">Log in or create your account to continue.</p>
      <div class="sb-modal-tabs">
        <button class="sb-modal-tab active" id="modal-tab-login" onclick="setModalTab('login')">Log In</button>
        <button class="sb-modal-tab" id="modal-tab-signup" onclick="setModalTab('signup')">Sign Up</button>
      </div>
      <button class="sb-modal-oauth-btn" onclick="window.location.href='/login'">
        💬 &nbsp; Continue with WhatsApp
      </button>
      <button class="sb-modal-oauth-btn" onclick="window.location.href='/google'">
        🌐 &nbsp; Continue with Google
      </button>
      <div class="sb-modal-divider">OR</div>
      <button class="sb-modal-email-btn" id="modalEmailBtn" onclick="window.location.href='/login'">Continue with Email</button>
      <div class="sb-modal-switch" id="modalSwitch">
        New to Scholarb? <a href="javascript:void(0)" onclick="setModalTab('signup')">Join Now for Free →</a>
      </div>
    </div>
  </div>
</div>

</div>{{-- .sb-page --}}

{{-- ============ JAVASCRIPT ============ --}}
<script>
// ============================================================
// ROLE DATA — all content by role
// ============================================================
const roles = ['students', 'parents', 'teachers'];
let currentRoleIndex = 0;

const roleData = {
  students: {
    h1Line1: 'Find the Right Tutor.',
    h1Line2: 'Achieve Your Goals.',
    desc: 'Connect with verified expert tutors for 1-to-1 or group classes. Personalized learning that helps you excel.',
    bullets: [
      'Verified & experienced tutors',
      'Flexible learning modes',
      'Better results, you can see',
    ],
    ctaText: 'Join Now for Free',
    ctaHref: null, // opens modal
    trustLine: 'Trusted by 5,000+ students & parents',
    img: '/assets/default/img/scholarb_student_hero.png',
    imgAlt: 'Student studying with laptop',
    features: [
      { iconKey: 'clock', title: 'Learn Anytime, Anywhere', desc: 'Flexible schedules and online classes that fit your routine.' },
      { iconKey: 'users', title: 'Revise With Experts', desc: 'Revise concepts with expert tutors and personalized learning to understand concepts deeply.' },
      { iconKey: 'chart', title: 'Practice & Improve', desc: 'Practice by targeting weak areas and use thoughtful mock tests to see real world improvements.' },
    ],
    hiwTitle: 'Your Learning Journey in 5 Simple Steps',
    hiwSteps: [
      { icon: '🎯', num: '1', title: 'Choose Your Goal', desc: 'Tell us what you want to learn and your academic goal.' },
      { icon: '👤', num: '2', title: 'Pick a Tutor', desc: 'We match you with the best tutor for your needs.' },
      { icon: '📅', num: '3', title: 'Book a Free Demo', desc: 'Choose a convenient time and book your free demo class.' },
      { icon: '💻', num: '4', title: 'Start Learning', desc: 'Attend your demo and start your personalized learning.' },
      { icon: '📈', num: '5', title: 'Track Progress', desc: 'Monitor your progress and achieve your goals.' },
    ],
    boardsTitle: 'What are you learning?',
    boardsSub: 'Choose your board or exam category to get started.',
    boardsItems: [
      { icon: '🏛️', label: 'CBSE' },
      { icon: '📖', label: 'ICSE' },
      { icon: '🌐', label: 'IGCSE' },
      { icon: '🎓', label: 'GCSE' },
      { icon: '🌍', label: 'IB' },
      { icon: '📝', label: 'A Levels' },
      { icon: '🎖️', label: 'AP' },
      { icon: '🏫', label: 'State Boards' },
      { icon: '📚', label: 'AS' },
      { icon: '📄', label: 'O Level' },
      { icon: '🎧', label: 'IELTS' },
      { icon: '🏆', label: 'NTSE' },
      { icon: '🩺', label: 'NEET' },
      { icon: '🥇', label: 'Olympiads', active: true },
    ],
    boardsViewBtn: 'View All Boards & Exams',
    storiesPre: 'STUDENT SUCCESS STORIES',
    testimonials: [
      { quote: 'Scholarb helped me understand complex concepts in Maths. My confidence and scores improved drastically!', author: '— Aryan, Grade 10' },
      { quote: 'The teachers are amazing and the personalized approach really makes a difference. Highly recommended!', author: '— Siya, Grade 12' },
      { quote: 'Great platform! Easy to track my child\'s progress and communicate with tutors.', author: '— Parent of Riya, Grade 9' },
    ],
    stats: [
      { icon: '👥', num: '5,000+', label: 'Happy Students' },
      { icon: '👨‍🏫', num: '1,200+', label: 'Expert Tutors' },
      { icon: '💻', num: '50,000+', label: 'Classes Completed' },
      { icon: '⭐', num: '95%', label: 'Satisfaction Rate' },
    ],
    ctaPre: 'Ready to Start Learning?',
    ctaTitle: 'Join Scholarb for Free',
    ctaDesc: 'Connect with top tutors and achieve your academic goals.',
    ctaBtnText: 'Join Now for Free',
    faqs: [
      { q: 'How does Scholarb work?', a: 'Scholarb matches students with certified expert tutors. You choose your subject and goals, book a free demo, and then begin personalized live classes.' },
      { q: 'Is the first demo class really free?', a: 'Yes, your first 15–30 minute demo session is completely free. No commitment and no credit card required.' },
      { q: 'What learning modes are available?', a: 'We offer flexible learning options including online 1-on-1 private tutoring, interactive group classes, and crash courses.' },
      { q: 'How are tutors verified?', a: 'Every tutor goes through a rigorous verification process, including identity checks, academic credential validation, and interviews.' },
      { q: 'Can I change my tutor later?', a: 'Absolutely! If your learning styles are not fully matched, you can easily change your tutor at any time.' },
    ],
    dotIndex: 1,
  },

  parents: {
    h1Line1: 'Support Your Child.',
    h1Line2: 'Build Their Future.',
    desc: 'Help your child learn from trusted tutors, track progress and build stronger academic confidence.',
    bullets: [
      'Track your child\'s progress',
      'Trusted expert tutors',
      'Personalized learning plans',
      'Stay informed',
    ],
    ctaText: 'Get Started as a Parent',
    ctaHref: null,
    trustLine: 'Trusted by 5,000+ students & parents',
    img: '/assets/default/img/scholarb_parent_hero.png',
    imgAlt: 'Family learning together',
    features: [
      { iconKey: 'shield', title: 'Track Progress with Confidence', desc: 'Monitor your child\'s learning performance, and improvement with detailed reports and insights.' },
      { iconKey: 'chart', title: 'Personalized Learning That Works', desc: 'Tailored learning plans and expert guidance to help your child understand concepts deeply and build strong foundation.' },
      { iconKey: 'target', title: 'Better Results, Brighter Future', desc: 'From homework help to exam preparation, we help your child stay ahead and achieve academic goals.' },
      { iconKey: 'chat', title: 'Stay Informed, Stay Involved', desc: 'Regular updates, seamless communication, and direct interaction with tutors—any time, anywhere.' },
    ],
    hiwTitle: 'Your Learning Journey in 5 Simple Steps',
    hiwSteps: [
      { icon: '🎯', num: '1', title: 'Choose Your Goal', desc: 'Tell us what you want to learn and your academic goal.' },
      { icon: '👤', num: '2', title: 'Pick a Tutor', desc: 'We match you with the best tutor for your needs.' },
      { icon: '📅', num: '3', title: 'Book a Free Demo', desc: 'Choose a convenient time and book your free demo class.' },
      { icon: '💻', num: '4', title: 'Start Learning', desc: 'Attend your demo and start your personalized learning.' },
      { icon: '📈', num: '5', title: 'Track Progress', desc: 'Monitor your progress and achieve your goals.' },
    ],
    boardsTitle: 'What are you learning?',
    boardsSub: 'Choose your board or exam category to get started.',
    boardsItems: [
      { icon: '🏛️', label: 'CBSE' },
      { icon: '📖', label: 'ICSE' },
      { icon: '🌐', label: 'IGCSE' },
      { icon: '🎓', label: 'GCSE' },
      { icon: '🌍', label: 'IB' },
      { icon: '📝', label: 'A Levels' },
      { icon: '🎖️', label: 'AP' },
      { icon: '🏫', label: 'State Boards' },
      { icon: '📚', label: 'AS' },
      { icon: '📄', label: 'O Level' },
      { icon: '🎧', label: 'IELTS' },
      { icon: '🏆', label: 'NTSE' },
      { icon: '🩺', label: 'NEET' },
      { icon: '🥇', label: 'Olympiads', active: true },
    ],
    boardsViewBtn: 'View All Boards & Exams',
    storiesPre: 'STUDENT SUCCESS STORIES',
    testimonials: [
      { quote: 'Scholarb helped me understand complex concepts in Maths. My confidence and scores improved drastically!', author: '— Aryan, Grade 10' },
      { quote: 'The teachers are amazing and the personalized approach really makes a difference. Highly recommended!', author: '— Siya, Grade 12' },
      { quote: 'Great platform! Easy to track my child\'s progress and communicate with tutors.', author: '— Parent of Riya, Grade 9' },
    ],
    stats: [
      { icon: '👥', num: '5,000+', label: 'Happy Students' },
      { icon: '👨‍🏫', num: '1,200+', label: 'Expert Tutors' },
      { icon: '💻', num: '50,000+', label: 'Classes Completed' },
      { icon: '⭐', num: '95%', label: 'Satisfaction Rate' },
    ],
    ctaPre: 'Ready to Start Learning?',
    ctaTitle: 'Join Scholarb for Free',
    ctaDesc: 'Connect with top tutors and achieve your academic goals.',
    ctaBtnText: 'Join Now for Free',
    faqs: [
      { q: 'How does Scholarb work?', a: 'Scholarb matches students with certified expert tutors. You choose your subject and goals, book a free demo, and then begin personalized live classes.' },
      { q: 'Is the first demo class really free?', a: 'Yes, your first 15–30 minute demo session is completely free. No commitment and no credit card required.' },
      { q: 'What learning modes are available?', a: 'We offer flexible learning options including online 1-on-1 private tutoring, interactive group classes, and crash courses.' },
      { q: 'How are tutors verified?', a: 'Every tutor goes through a rigorous verification process, including identity checks, academic credential validation, and interviews.' },
      { q: 'Can I change my tutor later?', a: 'Absolutely! If your learning styles are not fully matched, you can easily change your tutor at any time.' },
    ],
    dotIndex: 1,
  },

  teachers: {
    h1Line1: 'Teach. Inspire.',
    h1Line2: 'Make a Difference.',
    desc: 'Join Scholarb as a tutor and help students achieve their goals while growing your teaching career.',
    bullets: [
      'Connect with motivated students',
      'Set your own schedule',
      'Share your expertise',
      'Earn what you deserve',
    ],
    ctaText: 'Join as a Tutor for Free',
    ctaHref: null,
    trustLine: 'Trusted by 5,000+ tutors & students',
    img: '/assets/default/img/scholarb_teacher_hero.png',
    imgAlt: 'Teacher tutoring students',
    features: [
      { iconKey: 'compass', title: 'Teach Your Way', desc: 'Create your profile, set your subjects, and start teaching your way.' },
      { iconKey: 'users', title: 'Reach More Students', desc: 'Connect with thousands of students actively looking to learn.' },
      { iconKey: 'clock', title: 'Flexible & Convenient', desc: 'Teach online from anywhere and choose your available hours.' },
      { iconKey: 'growth', title: 'Track & Grow', desc: 'Monitor your performance, ratings, and grow your teaching journey.' },
    ],
    hiwTitle: 'Your Teaching Journey in 5 Simple Steps',
    hiwSteps: [
      { icon: '👤', num: '1', title: 'Create Your Profile', desc: 'Sign up and complete your tutor profile with your expertise.' },
      { icon: '📋', num: '2', title: 'Set Your Subjects & Rates', desc: 'Choose the subjects you teach and set your hourly rate.' },
      { icon: '🤝', num: '3', title: 'Get Matched with Students', desc: 'We connect you with students who need your help.' },
      { icon: '🎓', num: '4', title: 'Start Teaching', desc: 'Conduct live sessions and help students achieve their goals.' },
      { icon: '📈', num: '5', title: 'Track Progress & Earn', desc: 'Track your performance, earn reviews, and grow.' },
    ],
    boardsTitle: 'Teach Any Subject. Any Level.',
    boardsSub: 'From school academics to competitive exams and beyond.',
    boardsItems: [
      { icon: '🔢', label: 'Mathematics' },
      { icon: '🔬', label: 'Science' },
      { icon: '⚛️', label: 'Physics' },
      { icon: '🧪', label: 'Chemistry' },
      { icon: '🌿', label: 'Biology' },
      { icon: '📖', label: 'English' },
      { icon: '💻', label: 'Programming' },
      { icon: '📊', label: 'Accounts' },
      { icon: '🏆', label: 'Competitive' },
      { icon: '•••', label: 'More' },
    ],
    boardsViewBtn: 'View All Subjects',
    storiesPre: 'WHY TUTORS LOVE SCHOLARB',
    testimonials: [
      { quote: 'Scholarb has helped me connect with amazing students across the globe. The platform is easy to use and very rewarding.', author: '— Priya S., Math Tutor', stars: true },
      { quote: 'I can set my own schedule and teach the subjects I\'m passionate about. Highly recommended for every tutor!', author: '— Rahul K., Physics Tutor', stars: true },
      { quote: 'The support team is awesome and the students are really motivated to learn.', author: '— Ananya D., English Tutor', stars: true },
    ],
    stats: [
      { icon: '👥', num: '10,000+', label: 'Active Tutors' },
      { icon: '🎓', num: '50,000+', label: 'Students Helped' },
      { icon: '💻', num: '25,000+', label: 'Classes Completed' },
      { icon: '⭐', num: '4.9/5', label: 'Average Rating' },
    ],
    ctaPre: 'Ready to Inspire and Make an Impact?',
    ctaTitle: 'Join Scholarb as a Tutor',
    ctaDesc: 'Share your knowledge. Empower students. Build your future.',
    ctaBtnText: 'Join as a Tutor for Free',
    faqs: [
      { q: 'How does Scholarb work for tutors?', a: 'Sign up, complete your profile, get matched with students, and start teaching live sessions online.' },
      { q: 'How do I get paid?', a: 'Payouts are processed weekly directly to your bank account. You can track your earnings in your tutor dashboard.' },
      { q: 'Can I teach more than one subject?', a: 'Yes! You can list multiple subjects across different grade levels and exam types.' },
      { q: 'Is there any fee to join as a tutor?', a: 'No, joining Scholarb as a tutor is completely free. We only take a small commission per session.' },
    ],
    dotIndex: 2,
  },
};

// ============================================================
// RENDER FUNCTIONS
// ============================================================

// SVG icon map for feature cards
const featureIcons = {
  clock: '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><polyline points="12 6 12 12 16 14"/></svg>',
  users: '<svg viewBox="0 0 24 24"><circle cx="9" cy="7" r="4"/><path d="M3 20c0-3.3 2.7-6 6-6"/><circle cx="16" cy="9" r="3"/><path d="M13 20c0-2.8 1.6-5 3-5s3 2.2 3 5"/></svg>',
  chart: '<svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>',
  shield: '<svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
  target: '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>',
  chat: '<svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
  compass: '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>',
  growth: '<svg viewBox="0 0 24 24"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>',
};

function renderBullets(role) {
  const el = document.getElementById('heroBullets');
  el.innerHTML = roleData[role].bullets.map(b => `
    <li class="sb-hero-bullet">
      <div class="sb-bullet-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
      </div>
      ${b}
    </li>
  `).join('');
}

function renderFeatureCards(role) {
  const el = document.getElementById('roleFeatureCards');
  el.innerHTML = roleData[role].features.map(f => `
    <div class="sb-feature-card">
      <div class="sb-feature-icon">${featureIcons[f.iconKey] || featureIcons.clock}</div>
      <div>
        <div class="sb-feature-card-title">${f.title}</div>
        <div class="sb-feature-card-desc">${f.desc}</div>
      </div>
    </div>
  `).join('');
}

// Keep track of active step and timeline state
let hiwEntranceTimer = null;
let hiwObserver = null;
let currentActiveStep = 1;

const hiwIcons = {
  students: {
    1: `<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/></svg>`,
    2: `<svg viewBox="0 0 24 24"><circle cx="11" cy="8" r="4"/><path d="M4 21v-2a4 4 0 0 1 4-4h6a4 4 0 0 1 4 4v2"/><circle cx="18" cy="8" r="3"/><line x1="16" y1="17" x2="21" y2="22"/></svg>`,
    3: `<svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01"/></svg>`,
    4: `<svg viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>`,
    5: `<svg viewBox="0 0 24 24"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>`
  },
  parents: {
    1: `<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/></svg>`,
    2: `<svg viewBox="0 0 24 24"><circle cx="11" cy="8" r="4"/><path d="M4 21v-2a4 4 0 0 1 4-4h6a4 4 0 0 1 4 4v2"/><circle cx="18" cy="8" r="3"/><line x1="16" y1="17" x2="21" y2="22"/></svg>`,
    3: `<svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01"/></svg>`,
    4: `<svg viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>`,
    5: `<svg viewBox="0 0 24 24"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>`
  },
  teachers: {
    1: `<svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/><rect x="3" y="11" width="4" height="2" rx="1"/><rect x="17" y="11" width="4" height="2" rx="1"/></svg>`,
    2: `<svg viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>`,
    3: `<svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>`,
    4: `<svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>`,
    5: `<svg viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>`
  }
};

const boardIcons = {
  'CBSE': `<svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>`,
  'ICSE': `<svg viewBox="0 0 24 24"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>`,
  'IGCSE': `<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>`,
  'GCSE': `<svg viewBox="0 0 24 24"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 2 2 3 6 3s6-1 6-3v-5"/></svg>`,
  'IB': `<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M8 12h8M12 8v8"/></svg>`,
  'A Levels': `<svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>`,
  'AP': `<svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>`,
  'State Boards': `<svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>`,
  'AS': `<svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>`,
  'O Level': `<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>`,
  'IELTS': `<svg viewBox="0 0 24 24"><path d="M3 18v-6a9 9 0 0 1 18 0v6"/><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/></svg>`,
  'NTSE': `<svg viewBox="0 0 24 24"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2z"/></svg>`,
  'NEET': `<svg viewBox="0 0 24 24"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>`,
  'Olympiads': `<svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>`,
  'Mathematics': `<svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>`,
  'Science': `<svg viewBox="0 0 24 24"><path d="M10 2v7.31L4.75 18.55A2 2 0 0 0 6.42 21.5h11.16a2 2 0 0 0 1.67-2.95L14 9.31V2a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1z"/></svg>`,
  'Physics': `<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="2"/><path d="M12 2v20M2 12h20"/></svg>`,
  'Chemistry': `<svg viewBox="0 0 24 24"><path d="M10 2v7.31L4.75 18.55A2 2 0 0 0 6.42 21.5h11.16a2 2 0 0 0 1.67-2.95L14 9.31V2"/></svg>`,
  'Biology': `<svg viewBox="0 0 24 24"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.4 17 2c2.5 3 2.5 8-.5 12-1.5 2-4 3.5-5.5 6z"/></svg>`,
  'English': `<svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>`,
  'Programming': `<svg viewBox="0 0 24 24"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>`,
  'Accounts': `<svg viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><line x1="6" y1="8" x2="10" y2="8"/><line x1="6" y1="12" x2="14" y2="12"/><line x1="6" y1="16" x2="18" y2="16"/></svg>`,
  'Competitive': `<svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>`,
  'More': `<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/><circle cx="5" cy="12" r="1.5"/></svg>`
};

function setStepState(stepNum) {
  currentActiveStep = stepNum;
  const stepsContainer = document.getElementById('hiwSteps');
  const timelineBar = document.getElementById('hiwTimelineBar');
  if (!stepsContainer || !timelineBar) return;

  const steps = Array.from(stepsContainer.querySelectorAll('.sb-step'));
  const totalSteps = steps.length;
  const progress = ((stepNum - 1) / (totalSteps - 1)) * 100;

  // Set line progress
  timelineBar.style.setProperty('--progress-width', `${progress}%`);
  timelineBar.style.setProperty('--progress-height', `${progress}%`);

  // Update step states: past = completed, current = active, future = upcoming
  steps.forEach((step, idx) => {
    const num = idx + 1;
    const badgeNum = step.querySelector('.sb-badge-num');
    const badgeCheck = step.querySelector('.sb-badge-check');

    step.classList.remove('active', 'completed');
    step.removeAttribute('aria-current');

    if (num < stepNum) {
      step.classList.add('completed');
    } else if (num === stepNum) {
      step.classList.add('active');
      step.setAttribute('aria-current', 'step');
    }
  });
}

function selectStep(stepNum) {
  setStepState(stepNum);
}

function handleStepKeydown(e, stepNum) {
  if (e.key === 'Enter' || e.key === ' ') {
    e.preventDefault();
    selectStep(stepNum);
  }
}

function initHIWEvents() {
  const stepsContainer = document.getElementById('hiwSteps');
  if (!stepsContainer) return;

  // Set initial state to Step 1 without background timer
  setStepState(1);

  // Smooth hover handlers
  const steps = Array.from(stepsContainer.querySelectorAll('.sb-step'));
  steps.forEach(step => {
    step.addEventListener('mouseenter', () => {
      const stepNum = parseInt(step.getAttribute('data-step'), 10);
      setStepState(stepNum);
    });
  });
}

function renderHIW(role) {
  const titleEl = document.getElementById('hiwTitle');
  if (titleEl) titleEl.textContent = roleData[role].hiwTitle;

  const stepsEl = document.getElementById('hiwSteps');
  if (stepsEl) {
    const trackHTML = `
      <div class="sb-hiw-timeline-track" aria-hidden="true">
        <div class="sb-hiw-timeline-bar" id="hiwTimelineBar"></div>
      </div>
    `;

    const stepsHTML = roleData[role].hiwSteps.map(s => {
      const svgIcon = (hiwIcons[role] && hiwIcons[role][s.num]) || '';
      return `
        <li class="sb-step" data-step="${s.num}" tabindex="0" role="button" aria-label="Step ${s.num}: ${s.title}" onclick="selectStep(${s.num})" onkeydown="handleStepKeydown(event, ${s.num})">
          <div class="sb-step-marker">
            <div class="sb-step-badge" id="badge-step-${s.num}">
              <span class="sb-badge-num">${s.num}</span>
            </div>
            <div class="sb-step-icon-wrap sb-hiw-hide-mobile">
              ${svgIcon}
            </div>
          </div>
          <div class="sb-step-content-card">
            <div class="sb-step-icon-wrap sb-hiw-show-mobile">
              ${svgIcon}
            </div>
            <div class="sb-step-text">
              <div class="sb-step-title">${s.title}</div>
              <div class="sb-step-desc">${s.desc}</div>
            </div>
          </div>
        </li>
      `;
    }).join('');

    stepsEl.innerHTML = trackHTML + stepsHTML;
    
    initHIWEvents();
  }
}

function renderBoards(role) {
  const d = roleData[role];
  const titleEl = document.getElementById('boardsTitle');
  if (titleEl) titleEl.textContent = d.boardsTitle || 'What are you learning?';
  const subEl = document.getElementById('boardsSub');
  if (subEl) subEl.textContent = d.boardsSub || 'Choose your board or exam category to get started.';
  const btnEl = document.getElementById('boardsViewBtn');
  if (btnEl) btnEl.innerHTML = `<span>${d.boardsViewBtn || 'View All Boards & Exams'}</span> <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>`;

  const gridEl = document.getElementById('boardsGrid');
  if (gridEl) {
    gridEl.innerHTML = d.boardsItems.map(item => {
      const svgIcon = boardIcons[item.label] || `<svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>`;
      return `
        <div class="sb-board-chip${item.active ? ' active' : ''}" onclick="selectBoard(this)" tabindex="0" role="button" aria-label="${item.label}">
          <div class="sb-board-icon-wrapper">
            ${svgIcon}
          </div>
          <span class="sb-board-label">${item.label}</span>
          <span class="sb-board-check-dot" style="${item.active ? '' : 'display:none;'}">✓</span>
        </div>
      `;
    }).join('');
  }
}

function selectBoard(el) {
  document.querySelectorAll('.sb-board-chip').forEach(c => {
    c.classList.remove('active');
    const dot = c.querySelector('.sb-board-check-dot');
    if (dot) dot.style.display = 'none';
  });
  el.classList.add('active');
  const activeDot = el.querySelector('.sb-board-check-dot');
  if (activeDot) activeDot.style.display = 'inline-flex';
}

function renderTestimonials(role) {
  const d = roleData[role];
  document.getElementById('storiesPre').textContent = d.storiesPre;
  document.getElementById('testimonialsRow').innerHTML = d.testimonials.map(t => `
    <div class="sb-tcard">
      <div class="sb-tcard-quote">❝</div>
      <p class="sb-tcard-text">${t.quote}</p>
      <div class="sb-tcard-author">${t.author}</div>
      ${t.stars ? '<div class="sb-tcard-stars"><span>★</span><span>★</span><span>★</span><span>★</span><span>★</span></div>' : ''}
    </div>
  `).join('');
  document.getElementById('statsGrid').innerHTML = d.stats.map(s => `
    <div class="sb-stat-card">
      <div class="sb-stat-icon">${s.icon}</div>
      <div class="sb-stat-num">${s.num}</div>
      <div class="sb-stat-label">${s.label}</div>
    </div>
  `).join('');
}

function renderCTA(role) {
  const d = roleData[role];
  if (!d) return;
  const preEl = document.getElementById('ctaPre');
  if (preEl) preEl.textContent = d.ctaPre;
  const titleEl = document.getElementById('ctaTitle');
  if (titleEl) titleEl.textContent = d.ctaTitle;
  const descEl = document.getElementById('ctaDesc');
  if (descEl) descEl.textContent = d.ctaDesc;
  const btnSpan = document.getElementById('ctaBtnSpan');
  if (btnSpan) btnSpan.textContent = d.ctaBtnText || 'Join Now for Free';
}

function renderFAQ(role) {
  const faqContainer = document.getElementById('faqList');
  if (!faqContainer) return;
  const faqs = (roleData[role] && roleData[role].faqs) ? roleData[role].faqs : roleData['students'].faqs;

  faqContainer.innerHTML = faqs.map((f, i) => {
    const isOpen = i === 0;
    return `
      <div class="sb-faq-item${isOpen ? ' open' : ''}" id="faqItem${i}">
        <button class="sb-faq-question" onclick="toggleFaq(${i})" aria-expanded="${isOpen}">
          <span class="sb-faq-q-text">${f.q}</span>
          <div class="sb-faq-toggle">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          </div>
        </button>
        <div class="sb-faq-answer">
          <div class="sb-faq-answer-inner">${f.a}</div>
        </div>
      </div>
    `;
  }).join('');
}

function updateDots(role) {
  const dotIndex = roleData[role].dotIndex;
  document.querySelectorAll('.sb-dot').forEach((d, i) => {
    d.classList.toggle('active', i === dotIndex - 1);
  });
}

// ============================================================
// SWITCH ROLE — main function
// ============================================================
function switchRole(role) {
  currentRoleIndex = roles.indexOf(role);
  const d = roleData[role];

  // Update tabs
  document.querySelectorAll('.sb-role-tab').forEach(t => {
    t.classList.remove('active');
    t.setAttribute('aria-selected', 'false');
  });
  const activeTab = document.getElementById('tab-' + role);
  activeTab.classList.add('active');
  activeTab.setAttribute('aria-selected', 'true');

  // Fade left hero content
  const heroLeft = document.querySelector('.sb-hero-left');
  heroLeft.style.opacity = '0';
  heroLeft.style.transform = 'translateY(8px)';

  // Fade illustration
  const img = document.getElementById('roleIllus');
  img.style.opacity = '0';
  img.style.transform = 'scale(0.96)';

  // Fade feature cards
  const cards = document.getElementById('roleFeatureCards');
  cards.style.opacity = '0';

  setTimeout(() => {
    // Update hero left
    const h1l1 = document.getElementById('heroH1Line1');
    if (h1l1) h1l1.textContent = d.h1Line1;
    const h1l2 = document.getElementById('heroH1Line2');
    if (h1l2) h1l2.textContent = d.h1Line2;
    const descEl = document.getElementById('heroDesc');
    if (descEl) descEl.textContent = d.desc;
    const ctaTextEl = document.getElementById('heroCtaText');
    if (ctaTextEl) ctaTextEl.textContent = d.ctaText;
    const trustLineEl = document.getElementById('heroTrustLine');
    if (trustLineEl) trustLineEl.textContent = d.trustLine;
    renderBullets(role);

    // Update illustration
    img.src = d.img;
    img.alt = d.imgAlt;

    // Update feature cards
    renderFeatureCards(role);

    // Update carousel dots
    updateDots(role);

    // Restore opacity
    heroLeft.style.opacity = '1';
    heroLeft.style.transform = 'translateY(0)';
    img.style.opacity = '1';
    img.style.transform = 'scale(1)';
    cards.style.opacity = '1';

    // Update other sections
    renderHIW(role);
    renderBoards(role);
    renderTestimonials(role);
    renderCTA(role);
    renderFAQ(role);

  }, 200);
}

// Cycle role via arrow button
function rotateRole() {
  currentRoleIndex = (currentRoleIndex + 1) % roles.length;
  switchRole(roles[currentRoleIndex]);
}

// ============================================================
// FAQ ACCORDION
// ============================================================
function toggleFaq(index) {
  const targetItem = document.getElementById('faqItem' + index);
  if (!targetItem) return;

  const isOpen = targetItem.classList.contains('open');

  document.querySelectorAll('.sb-faq-item').forEach(item => {
    item.classList.remove('open');
    const btn = item.querySelector('.sb-faq-question');
    if (btn) btn.setAttribute('aria-expanded', 'false');
  });

  if (!isOpen) {
    targetItem.classList.add('open');
    const btn = targetItem.querySelector('.sb-faq-question');
    if (btn) btn.setAttribute('aria-expanded', 'true');
  }
}

// ============================================================
// FOOTER ACCORDION (MOBILE)
// ============================================================
function toggleFooterAccordion(btn) {
  if (window.innerWidth > 767) return; // Only run on mobile
  const parent = btn.parentElement;
  const isOpen = parent.classList.contains('open');
  
  // Close all accordions
  document.querySelectorAll('.sb-footer-accordion').forEach(acc => {
    acc.classList.remove('open');
    const b = acc.querySelector('.sb-footer-col-header');
    if (b) b.setAttribute('aria-expanded', 'false');
    const icon = acc.querySelector('.sb-accordion-icon');
    if (icon) icon.textContent = '+';
  });

  // Toggle current if it was closed
  if (!isOpen) {
    parent.classList.add('open');
    btn.setAttribute('aria-expanded', 'true');
    const icon = btn.querySelector('.sb-accordion-icon');
    if (icon) icon.textContent = '−';
  }
}

// ============================================================
// TESTIMONIAL CAROUSEL (simple)
// ============================================================
function prevTestimonials() {
  // For now just re-render (full carousel implementation would require more JS)
  // This is a visual affordance — already shows all 3 on desktop
}
function nextTestimonials() {}

// ============================================================
// AUTH MODAL
// ============================================================
let modalMode = 'login';

function openModal(mode) {
  modalMode = mode;
  setModalTab(mode);
  document.getElementById('authModal').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeModal() {
  document.getElementById('authModal').classList.remove('open');
  document.body.style.overflow = '';
}

function setModalTab(mode) {
  modalMode = mode;
  document.getElementById('modal-tab-login').classList.toggle('active', mode === 'login');
  document.getElementById('modal-tab-signup').classList.toggle('active', mode === 'signup');
  const btn = document.getElementById('modalEmailBtn');
  const sw = document.getElementById('modalSwitch');
  if (mode === 'login') {
    btn.textContent = 'Continue with Email';
    btn.onclick = () => window.location.href = '/login';
    sw.innerHTML = 'New to Scholarb? <a href="javascript:void(0)" onclick="setModalTab(\'signup\')">Join Now for Free →</a>';
  } else {
    btn.textContent = 'Sign Up with Email';
    btn.onclick = () => window.location.href = '/register';
    sw.innerHTML = 'Already have an account? <a href="javascript:void(0)" onclick="setModalTab(\'login\')">Log In →</a>';
  }
}

// ESC to close
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

// ============================================================
// CSS TRANSITIONS on hero left
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
  const heroLeft = document.querySelector('.sb-hero-left');
  heroLeft.style.transition = 'opacity 0.22s ease, transform 0.22s ease';

  const img = document.getElementById('roleIllus');
  img.style.transition = 'opacity 0.22s ease, transform 0.22s ease';

  const cards = document.getElementById('roleFeatureCards');
  cards.style.transition = 'opacity 0.18s ease';

  // Initial render
  switchRole('students');
  loadFeaturedTutors();
  loadDashboardPreview();
});

// ============================================================
// FEATURED TUTORS FETCH & RENDER
// ============================================================
async function loadFeaturedTutors() {
  const grid = document.getElementById('tutorsGrid');
  if (!grid) return;

  try {
    const res = await fetch('/api/featured-tutors');
    const json = await res.json();

    if (json.success && json.data && json.data.length > 0) {
      const tutors = json.data.slice(0, 4);
      // Duplicate list for seamless 100% continuous marquee on mobile
      const displayList = [...tutors, ...tutors];

      grid.innerHTML = displayList.map((t, idx) => `
        <div class="sb-tutor-card${idx >= 4 ? ' sb-tutor-card-dup' : ''}">
          <div class="sb-tutor-card-top">
            <div class="sb-tutor-avatar-wrap">
              <img src="${t.avatar}" alt="${t.name}" class="sb-tutor-avatar" onerror="this.src='/assets/default/img/avatar.png'">
              ${t.verified ? '<span class="sb-tutor-verified" title="Verified Instructor">✓</span>' : ''}
            </div>
            <span class="sb-tutor-subject">${t.subject}</span>
          </div>

          <h3 class="sb-tutor-name">${t.name}</h3>

          <div class="sb-tutor-meta">
            <div class="sb-tutor-rating">
              <span class="sb-star">★</span>
              <span class="sb-score">${t.rate}</span>
              <span class="sb-reviews">(${t.reviews_count} reviews)</span>
            </div>
            <div class="sb-tutor-price">${t.price}</div>
          </div>

          <p class="sb-tutor-bio">${t.bio}</p>

          <a href="${t.profile_url}" class="sb-tutor-profile-btn">
            <span>View Profile</span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </a>
        </div>
      `).join('');
    } else {
      renderTutorsEmptyState();
    }
  } catch (err) {
    console.error('Failed to load featured tutors:', err);
    renderTutorsErrorState();
  }
}

function renderTutorsEmptyState() {
  const grid = document.getElementById('tutorsGrid');
  if (grid) {
    grid.innerHTML = `
      <div class="sb-tutors-state-card">
        <div class="sb-state-icon">🎓</div>
        <h4 class="sb-state-title">No Tutors Found</h4>
        <p class="sb-state-desc">Explore our full instructor catalog to find expert tutors across all subjects.</p>
        <a href="/instructor-finder" class="sb-tutors-view-btn">Browse Instructor Catalog</a>
      </div>
    `;
  }
}

function renderTutorsErrorState() {
  const grid = document.getElementById('tutorsGrid');
  if (grid) {
    grid.innerHTML = `
      <div class="sb-tutors-state-card">
        <div class="sb-state-icon">⚠️</div>
        <h4 class="sb-state-title">Unable to Load Tutors</h4>
        <p class="sb-state-desc">Something went wrong while fetching tutor profiles. Please try again.</p>
        <button onclick="loadFeaturedTutors()" class="sb-tutors-view-btn" style="border:none; cursor:pointer;">Try Again</button>
      </div>
    `;
  }
}

async function loadDashboardPreview() {
  try {
    const res = await fetch('/api/student-dashboard-preview');
    const json = await res.json();

    if (json.success && json.stats) {
      const cEl = document.getElementById('dashCoursesCount');
      if (cEl) cEl.textContent = json.stats.courses_count;
    }
    if (json.success && json.upcoming_class) {
      const u = json.upcoming_class;
      const tEl = document.getElementById('dashLiveTitle');
      if (tEl) tEl.textContent = u.title;
      const tutEl = document.getElementById('dashLiveTutor');
      if (tutEl) tutEl.textContent = 'Instructor: ' + u.instructor;
      const tmEl = document.getElementById('dashLiveTime');
      if (tmEl) tmEl.textContent = u.time;
    }
  } catch (e) {
    console.log('Using default dashboard preview data');
  }
}
</script>

<script>
// ============================================================
// INTERACTIVE DASHBOARD NAV SWITCHER
// ============================================================
var sbDemoViews = ['dashboard','courses','tutors','live','assignments','messages','analytics'];
var sbDemoIndex = 0;
var sbDemoTimer = null;
var sbDemoPaused = false;

function sbSwitchView(view, navEl) {
  // Update active nav item
  var allNavItems = document.querySelectorAll('.sb-nav-item');
  allNavItems.forEach(function(el) { el.classList.remove('active'); });
  if (navEl) {
    navEl.classList.add('active');
  } else {
    // Find and activate by data-view
    var target = document.querySelector('.sb-nav-item[data-view="' + view + '"]');
    if (target) target.classList.add('active');
  }

  // Sync demo index
  sbDemoIndex = sbDemoViews.indexOf(view);
  if (sbDemoIndex < 0) sbDemoIndex = 0;

  // Get the main workspace
  var workspace = document.querySelector('#showcaseSection .sb-app-main');
  if (!workspace) return;

  // Fade out
  workspace.style.transition = 'opacity 0.18s ease, transform 0.18s ease';
  workspace.style.opacity = '0';
  workspace.style.transform = 'translateY(6px)';

  setTimeout(function() {
    if (view === 'dashboard') {
      var metrics = workspace.querySelector('.sb-metrics-grid');
      var split   = workspace.querySelector('.sb-workspace-split');
      if (metrics) metrics.style.display = '';
      if (split)   split.style.display   = '';
      var injected = workspace.querySelector('.sb-dynamic-view');
      if (injected) injected.remove();
    } else {
      var metrics = workspace.querySelector('.sb-metrics-grid');
      var split   = workspace.querySelector('.sb-workspace-split');
      if (metrics) metrics.style.display = 'none';
      if (split)   split.style.display   = 'none';

      var prev = workspace.querySelector('.sb-dynamic-view');
      if (prev) prev.remove();

      var tpl = document.getElementById('tpl-' + view);
      if (tpl) {
        var clone = tpl.cloneNode(true);
        clone.removeAttribute('id');
        clone.classList.add('sb-dynamic-view');
        workspace.appendChild(clone);
      }
    }

    // Clear msg badge when messages viewed
    if (view === 'messages') {
      var pill = document.getElementById('sbMsgPill');
      if (pill) { pill.textContent = ''; pill.style.display = 'none'; }
    }

    // Fade in
    workspace.style.opacity = '1';
    workspace.style.transform = 'translateY(0)';
  }, 180);
}

function sbDemoNext() {
  if (sbDemoPaused) return;
  sbDemoIndex = (sbDemoIndex + 1) % sbDemoViews.length;
  sbSwitchView(sbDemoViews[sbDemoIndex], null);
}

function sbStartDemo() {
  sbDemoTimer = setInterval(sbDemoNext, 1800);
}

function sbStopDemo() {
  if (sbDemoTimer) { clearInterval(sbDemoTimer); sbDemoTimer = null; }
}

// Boot the auto-demo after page loads
document.addEventListener('DOMContentLoaded', function() {
  var dashCard = document.querySelector('#showcaseSection .sb-dash-card');
  if (dashCard) {
    // Pause on hover
    dashCard.addEventListener('mouseenter', function() {
      sbDemoPaused = true;
    });
    dashCard.addEventListener('mouseleave', function() {
      sbDemoPaused = false;
    });
    // Pause on manual nav click (resume after 5s)
    document.querySelectorAll('.sb-nav-item').forEach(function(el) {
      el.addEventListener('click', function() {
        sbDemoPaused = true;
        clearTimeout(window._sbResumeTimer);
        window._sbResumeTimer = setTimeout(function() { sbDemoPaused = false; }, 5000);
      });
    });
  }
  // Start the demo after 2 seconds delay
  setTimeout(sbStartDemo, 2000);
});
</script>
@endsection
