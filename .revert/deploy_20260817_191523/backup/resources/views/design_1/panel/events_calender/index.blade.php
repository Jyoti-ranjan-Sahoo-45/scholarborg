@extends('design_1.panel.layouts.panel')

@push('styles_top')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css"/>
<style>
/* ── Power Calendar Styles ───────────────────────────── */
.power-calendar-wrap        { display:flex; gap:20px; }
.power-calendar-main        { flex:1; min-width:0; }
.power-calendar-sidebar     { width:320px; flex-shrink:0; display:flex; flex-direction:column; gap:16px; }

#powerCalendar              { background:#fff; border-radius:20px; padding:20px; box-shadow:0 1px 6px rgba(0,0,0,.06); }

/* Toolbar */
.pc-toolbar                 { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-bottom:16px; }
.pc-toolbar h2              { font-size:18px; font-weight:700; color:#1e1e2f; margin:0; }
.pc-view-btns .btn          { border-radius:8px; font-size:13px; font-weight:600; }
.pc-view-btns .btn.active   { background:#5c67f2; color:#fff; border-color:#5c67f2; }

/* Legend */
.pc-legend                  { display:flex; align-items:center; gap:14px; flex-wrap:wrap; font-size:12px; color:#555; }
.pc-legend-dot              { width:10px; height:10px; border-radius:50%; display:inline-block; margin-right:4px; }

/* Event popup */
.pc-popup                   { display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%);
                              background:#fff; border-radius:20px; box-shadow:0 8px 40px rgba(0,0,0,.18);
                              z-index:9999; width:440px; max-width:95vw; max-height:90vh; overflow-y:auto; }
.pc-popup-overlay           { display:none; position:fixed; inset:0; background:rgba(0,0,0,.35); z-index:9998; }
.pc-popup-header            { padding:20px 24px 0; display:flex; justify-content:space-between; align-items:center; }
.pc-popup-header h4         { font-size:16px; font-weight:700; margin:0; }
.pc-popup-body              { padding:20px 24px; }
.pc-popup-footer            { padding:0 24px 20px; display:flex; flex-wrap:wrap; gap:8px; }
.pc-badge                   { padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; display:inline-block; }
.pc-badge-pending           { background:#fef3c7; color:#92400e; }
.pc-badge-open              { background:#d1fae5; color:#065f46; }
.pc-badge-finished          { background:#f3f4f6; color:#374151; }
.pc-badge-canceled          { background:#fee2e2; color:#991b1b; }
.pc-badge-demo              { background:#ede9fe; color:#5b21b6; }
.pc-info-row                { display:flex; align-items:center; gap:10px; margin-bottom:10px; font-size:13px; }
.pc-info-row .label         { color:#888; min-width:100px; font-weight:500; }
.pc-reschedule-notice       { background:#fffbeb; border:1px solid #fbbf24; border-radius:10px; padding:10px 14px; font-size:12px; margin-bottom:12px; }

/* Sidebar panels */
.pc-sidebar-panel           { background:#fff; border-radius:16px; padding:18px; box-shadow:0 1px 6px rgba(0,0,0,.06); }
.pc-sidebar-panel h3        { font-size:14px; font-weight:700; margin:0 0 14px; color:#1e1e2f; }
.pc-request-card            { border:1px solid #e5e7eb; border-radius:10px; padding:10px 12px; margin-bottom:10px; font-size:12px; }
.pc-request-card .name      { font-weight:600; font-size:13px; margin-bottom:3px; }

/* Schedule class button */
#btnScheduleClass           { background:linear-gradient(135deg,#5c67f2,#7c3aed); color:#fff; border:none;
                              border-radius:10px; padding:9px 18px; font-size:13px; font-weight:600; cursor:pointer; }
#btnScheduleClass:hover     { opacity:.9; }

/* Student chips */
.pc-student-chips-container { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; }
.pc-student-chip           { display: inline-flex; align-items: center; background: #e0e7ff; color: #3730a3;
                              padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
.pc-student-chip .remove    { margin-left: 6px; cursor: pointer; color: #ef4444; font-weight: 700; }

/* Custom Close Button Fixes */
.modal-header .close {
    border: none !important;
    background: transparent !important;
    padding: 12px 18px !important;
    margin: 0 !important;
    color: #9ca3af !important;
    opacity: 0.7;
    font-size: 24px !important;
    cursor: pointer;
    box-shadow: none !important;
    outline: none !important;
    position: absolute;
    right: 12px;
    top: 10px;
    height: auto !important;
    width: auto !important;
}
.modal-header .close:hover {
    color: #1e1e2f !important;
    opacity: 1;
}
.modal-header {
    position: relative;
}

/* FullCalendar overrides */
.fc-event                   { border-radius:6px; border:none !important; font-size:12px; font-weight:600; cursor:pointer; padding:2px 6px; }
.fc-daygrid-event-harness   { margin-top:2px; }
.fc-toolbar.fc-header-toolbar { display:none; }
.fc-col-header-cell         { font-size:13px; font-weight:600; padding:8px 0; }
.fc-daygrid-day-number      { font-size:13px; font-weight:500; }

@media(max-width:900px) {
    .power-calendar-wrap    { flex-direction:column; }
    .power-calendar-sidebar { width:100%; }
}
</style>
@endpush

@section('content')
@php
    $user = auth()->user();
    $isInstructor = in_array($user->role_name, ['teacher','organization']);
    $isAdmin      = $user->role_name === 'admin';
    $isTeacher    = $isInstructor || $isAdmin;
    $csrfToken    = csrf_token();
@endphp

<div class="pc-toolbar">
    <h2>📅 {{ trans('update.events_calendar') }}</h2>
    <div class="d-flex align-items-center gap-10 flex-wrap">
        {{-- View toggle --}}
        <div class="pc-view-btns btn-group">
            <button class="btn btn-sm btn-outline-secondary active" id="btnMonthView">Month</button>
            <button class="btn btn-sm btn-outline-secondary" id="btnWeekView">Week</button>
            <button class="btn btn-sm btn-outline-secondary" id="btnDayView">Day</button>
        </div>
        {{-- Nav --}}
        <button class="btn btn-sm btn-outline-secondary" id="btnPrev">‹ Prev</button>
        <span id="calendarTitle" class="font-weight-bold text-dark font-14"></span>
        <button class="btn btn-sm btn-outline-secondary" id="btnNext">Next ›</button>
        <button class="btn btn-sm btn-outline-secondary" id="btnToday">Today</button>

        @if($isTeacher)
            <button id="btnScheduleClass">+ Schedule Class</button>
        @endif
    </div>
</div>

{{-- Legend --}}
<div class="pc-legend mb-16">
    <span><span class="pc-legend-dot" style="background:#f59e0b"></span>Pending</span>
    <span><span class="pc-legend-dot" style="background:#22c55e"></span>Active</span>
    <span><span class="pc-legend-dot" style="background:#6b7280"></span>Finished</span>
    <span><span class="pc-legend-dot" style="background:#ef4444"></span>Cancelled</span>
    <span><span class="pc-legend-dot" style="background:#8b5cf6"></span>Demo</span>
</div>

<div class="power-calendar-wrap">
    {{-- Main Calendar --}}
    <div class="power-calendar-main">
        <div id="powerCalendar"></div>
    </div>

    {{-- Sidebar --}}
    <div class="power-calendar-sidebar">

        {{-- Today's Schedule --}}
        <div class="pc-sidebar-panel">
            <h3>📋 Today's Schedule</h3>
            <div id="todayScheduleList">
                <p class="text-gray-400 font-12">Loading...</p>
            </div>
        </div>

        @if($isTeacher)
        {{-- Pending Reschedule Requests --}}
        <div class="pc-sidebar-panel">
            <h3>🔄 Reschedule Requests <span id="requestsBadge" class="badge badge-warning ml-4" style="display:none"></span></h3>
            <div id="rescheduleRequestsList">
                <p class="text-gray-400 font-12">No pending requests</p>
            </div>
        </div>
        @endif

    </div>
</div>

{{-- ───────────────────────────────────────────────── --}}
{{-- EVENT DETAIL POPUP                               --}}
{{-- ───────────────────────────────────────────────── --}}
<div class="pc-popup-overlay" id="popupOverlay"></div>
<div class="pc-popup" id="eventPopup">
    <div class="pc-popup-header">
        <h4 id="popupTitle">Meeting Details</h4>
        <button class="btn btn-sm" id="btnClosePopup">✕</button>
    </div>
    <div class="pc-popup-body" id="popupBody">
        <p>Loading...</p>
    </div>
    <div class="pc-popup-footer" id="popupFooter"></div>
</div>

{{-- ───────────────────────────────────────────────── --}}
{{-- SCHEDULE CLASS MODAL                             --}}
{{-- ───────────────────────────────────────────────── --}}
@if($isTeacher)
<div class="modal fade" id="scheduleClassModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content rounded-20">
            <div class="modal-header">
                <h5 class="modal-title font-16 font-weight-bold">📅 Schedule a Class</h5>
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="font-13 font-weight-500">Students</label>
                    <input type="text" id="studentSearchInput" class="form-control" placeholder="Search by name or email..." autocomplete="off">
                    <div id="studentSearchResults" class="border rounded mt-1" style="display:none; max-height:160px; overflow-y:auto; z-index: 10000; background: #fff; position: absolute; width: 90%;"></div>
                    <div id="selectedStudentsChips" class="pc-student-chips-container"></div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-13 font-weight-500">Date</label>
                            <input type="date" id="scheduleDay" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-13 font-weight-500">Start</label>
                            <input type="time" id="scheduleStart" class="form-control" value="10:00">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-13 font-weight-500">End</label>
                            <input type="time" id="scheduleEnd" class="form-control" value="11:00">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-13 font-weight-500">Price ($)</label>
                            <input type="number" id="schedulePrice" class="form-control" value="0" min="0">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group pt-28">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="scheduleIsDemo">
                                <label class="custom-control-label font-13" for="scheduleIsDemo">Free Demo Session</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="scheduleRepeat">
                        <label class="custom-control-label font-13" for="scheduleRepeat">Repeat this class</label>
                    </div>
                </div>
                <div id="repeatOptions" style="display:none;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-13 font-weight-500">Repeat</label>
                                <select id="repeatType" class="form-control">
                                    <option value="weekly">Weekly</option>
                                    <option value="daily">Daily</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-13 font-weight-500">Sessions</label>
                                <input type="number" id="repeatCount" class="form-control" value="4" min="2" max="52">
                            </div>
                        </div>
                    </div>
                    <p class="font-12 text-muted" id="repeatPreview"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" id="btnConfirmSchedule">
                    <span id="scheduleSpinner" class="spinner-border spinner-border-sm" style="display:none"></span>
                    Schedule + Send Invite
                </button>
            </div>
        </div>
    </div>
</div>

{{-- RESCHEDULE MODAL (instructor/admin) --}}
<div class="modal fade" id="rescheduleModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content rounded-20">
            <div class="modal-header">
                <h5 class="modal-title font-16 font-weight-bold">✏️ Reschedule</h5>
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="rescheduleBookingId">
                <div class="form-group">
                    <label class="font-13 font-weight-500">New Date</label>
                    <input type="date" id="rescheduleDay" class="form-control">
                </div>
                <div class="form-group">
                    <label class="font-13 font-weight-500">New Start Time</label>
                    <input type="time" id="rescheduleStart" class="form-control">
                </div>
                <div class="form-group">
                    <label class="font-13 font-weight-500">New End Time</label>
                    <input type="time" id="rescheduleEnd" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" id="btnConfirmReschedule">Reschedule</button>
            </div>
        </div>
    </div>
</div>

{{-- ATTENDANCE MODAL --}}
<div class="modal fade" id="attendanceModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content rounded-20">
            <div class="modal-header">
                <h5 class="modal-title font-16 font-weight-bold">📊 Mark Attendance</h5>
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="attendanceBookingId">
                <div class="form-group">
                    <label class="font-13 font-weight-500">Attendance Status</label>
                    <select id="attendanceStatus" class="form-control">
                        <option value="present">✅ Present</option>
                        <option value="absent">❌ Absent</option>
                        <option value="excused">⚠️ Excused</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button class="btn btn-success" id="btnConfirmAttendance">Save</button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- CANCEL MODAL --}}
<div class="modal fade" id="cancelBookingModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content rounded-20">
            <div class="modal-header">
                <h5 class="modal-title font-16 font-weight-bold">❌ Cancel Booking</h5>
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="cancelBookingId">
                <div class="form-group">
                    <label class="font-13 font-weight-500">Reason (optional)</label>
                    <textarea id="cancelReason" class="form-control" rows="3" placeholder="Reason for cancellation..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Keep Booking</button>
                <button class="btn btn-danger" id="btnConfirmCancel">Cancel Booking</button>
            </div>
        </div>
    </div>
</div>

{{-- STUDENT RESCHEDULE REQUEST MODAL --}}
<div class="modal fade" id="requestRescheduleModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content rounded-20">
            <div class="modal-header">
                <h5 class="modal-title font-16 font-weight-bold">🔄 Request Reschedule</h5>
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="requestRescheduleBookingId">
                <div class="form-group">
                    <label class="font-13 font-weight-500">Preferred New Date</label>
                    <input type="date" id="requestRescheduleDay" class="form-control">
                </div>
                <div class="form-group">
                    <label class="font-13 font-weight-500">Preferred Start Time</label>
                    <input type="time" id="requestRescheduleStart" class="form-control">
                </div>
                <div class="form-group">
                    <label class="font-13 font-weight-500">Note</label>
                    <textarea id="requestRescheduleNote" class="form-control" rows="2" placeholder="Why do you need to reschedule?"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" id="btnConfirmRequestReschedule">Send Request</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts_bottom')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
var CALENDAR_BASE = '/panel/events-calender';
var IS_TEACHER   = {{ $isTeacher ? 'true' : 'false' }};
var IS_ADMIN     = {{ $isAdmin   ? 'true' : 'false' }};
var CSRF         = '{{ $csrfToken }}';
</script>
<script src="/assets/design_1/js/panel/power_calendar.js?v={{ time() }}"></script>
@endpush
