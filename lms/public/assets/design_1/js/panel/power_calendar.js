/**
 * Power Calendar — FullCalendar.js Integration
 * Handles all calendar interactions for Students, Instructors, and Admins.
 */
(function ($) {
    'use strict';

    var calendar;

    // ─── Init ──────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        // Relocate all modals to body to prevent z-index backdrop bugs
        $('.modal').appendTo('body');

        initCalendar();
        loadTodaySchedule();
        if (IS_TEACHER) loadRescheduleRequests();
        bindModals();
        bindStudentSearch();
    });

    function initCalendar() {
        var el = document.getElementById('powerCalendar');
        if (!el) return;

        calendar = new FullCalendar.Calendar(el, {
            initialView: 'dayGridMonth',
            headerToolbar: false,
            height: 'auto',
            nowIndicator: true,
            selectable: IS_TEACHER,
            editable: false,
            events: {
                url: CALENDAR_BASE + '/json',
                method: 'GET',
            },
            eventClick: function (info) {
                var id = info.event.id;
                if (!id) return;
                openEventPopup(id);
            },
            dateClick: function (info) {
                if (!IS_TEACHER) return;
                // Pre-fill date in schedule modal
                $('#scheduleDay').val(info.dateStr);
                $('#scheduleClassModal').modal('show');
            },
            loading: function (isLoading) {
                if (!isLoading) {
                    updateCalendarTitle();
                    loadTodaySchedule();
                }
            }
        });

        calendar.render();

        // Custom toolbar buttons
        $('#btnMonthView').on('click', function () {
            calendar.changeView('dayGridMonth');
            setActiveViewBtn(this);
        });
        $('#btnWeekView').on('click', function () {
            calendar.changeView('timeGridWeek');
            setActiveViewBtn(this);
        });
        $('#btnDayView').on('click', function () {
            calendar.changeView('timeGridDay');
            setActiveViewBtn(this);
        });
        $('#btnPrev').on('click', function () { calendar.prev(); updateCalendarTitle(); });
        $('#btnNext').on('click', function () { calendar.next(); updateCalendarTitle(); });
        $('#btnToday').on('click', function () { calendar.today(); updateCalendarTitle(); loadTodaySchedule(); });

        updateCalendarTitle();
    }

    function setActiveViewBtn(btn) {
        $('.pc-view-btns .btn').removeClass('active');
        $(btn).addClass('active');
    }

    function updateCalendarTitle() {
        if (!calendar) return;
        var d = calendar.getDate();
        var formatted = d.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
        $('#calendarTitle').text(formatted);
    }

    // ─── Today's Schedule Sidebar ──────────────────────────────────────────
    function loadTodaySchedule() {
        var today = new Date();
        var startStr = today.toISOString().slice(0, 10) + 'T00:00:00';
        var endStr   = today.toISOString().slice(0, 10) + 'T23:59:59';

        $.get(CALENDAR_BASE + '/json', { start: startStr, end: endStr }, function (events) {
            var $list = $('#todayScheduleList').empty();

            if (!events || events.length === 0) {
                $list.html('<p class="text-gray-400 font-12 mt-8">No sessions today</p>');
                return;
            }

            events.forEach(function (ev) {
                var props = ev.extendedProps || {};
                var statusClass = 'pc-badge-' + props.status;
                var joinBtn = (props.has_link && props.status === 'open')
                    ? '<a href="' + props.link + '" target="_blank" class="btn btn-sm btn-success ml-8" style="font-size:11px;">🎥 Join</a>'
                    : '';

                var card = '<div class="d-flex align-items-center justify-content-between p-10 rounded-12 mb-8" style="background:#f9fafb;border:1px solid #e5e7eb;">' +
                    '<div>' +
                        '<div class="font-13 font-weight-600">' + ev.title + '</div>' +
                        '<div class="font-11 text-gray-400 mt-2">' + (props.time || '') + '</div>' +
                        '<span class="pc-badge ' + statusClass + ' mt-4">' + props.status + '</span>' +
                        (props.is_demo ? ' <span class="pc-badge pc-badge-demo">Demo</span>' : '') +
                    '</div>' +
                    '<div class="d-flex align-items-center gap-8">' + joinBtn +
                        (ev.id ? '<button class="btn btn-sm btn-outline-secondary" style="font-size:11px;" onclick="powerCalendar.openPopup(' + ev.id + ')">Details</button>' : '') +
                    '</div></div>';

                $list.append(card);
            });
        }).fail(function () {
            $('#todayScheduleList').html('<p class="text-danger font-12">Failed to load</p>');
        });
    }

    // ─── Reschedule Requests Sidebar ───────────────────────────────────────
    function loadRescheduleRequests() {
        if (!IS_TEACHER) return;
        $.get(CALENDAR_BASE + '/reschedule-requests', function (requests) {
            var $list = $('#rescheduleRequestsList').empty();
            var $badge = $('#requestsBadge');

            if (!requests || requests.length === 0) {
                $list.html('<p class="text-gray-400 font-12">No pending requests</p>');
                $badge.hide();
                return;
            }

            $badge.text(requests.length).show();

            requests.forEach(function (req) {
                var card = '<div class="pc-request-card">' +
                    '<div class="name">' + (req.requested_by ? req.requested_by.full_name : 'Student') + '</div>' +
                    '<div class="text-gray-400">Wants: ' + req.new_day + ' @ ' + (req.new_start_at ? formatTime(req.new_start_at) : '?') + '</div>' +
                    (req.note ? '<div class="text-gray-500 mt-4">"' + req.note + '"</div>' : '') +
                    '<div class="d-flex gap-6 mt-8">' +
                        '<button class="btn btn-sm btn-success" onclick="powerCalendar.approveRequest(' + req.id + ')">✓ Approve</button>' +
                        '<button class="btn btn-sm btn-danger" onclick="powerCalendar.rejectRequest(' + req.id + ')">✗ Reject</button>' +
                    '</div></div>';
                $list.append(card);
            });
        }).fail(function () {
            $('#rescheduleRequestsList').html('<p class="text-danger font-12">Failed to load</p>');
        });
    }

    // ─── Event Popup ───────────────────────────────────────────────────────
    function openEventPopup(bookingId) {
        $('#popupBody').html('<p class="text-gray-400">Loading...</p>');
        $('#popupFooter').empty();
        $('#eventPopup, #popupOverlay').show();

        $.get(CALENDAR_BASE + '/booking/' + bookingId, function (data) {
            renderPopup(data);
        }).fail(function () {
            $('#popupBody').html('<p class="text-danger">Failed to load booking details.</p>');
        });
    }

    function renderPopup(data) {
        var statusClass = 'pc-badge-' + data.status;
        var demoTag = data.is_demo ? '<span class="pc-badge pc-badge-demo ml-4">Demo</span>' : '';

        var html = '<div class="pc-info-row"><span class="label">Status</span>' +
            '<span class="pc-badge ' + statusClass + '">' + data.status + '</span>' + demoTag + '</div>';

        html += '<div class="pc-info-row"><span class="label">Date</span><span>' + data.day + '</span></div>';
        html += '<div class="pc-info-row"><span class="label">Time</span><span>' + data.time + '</span></div>';

        if (data.student_name) {
            html += '<div class="pc-info-row"><span class="label">Student</span><span>' + data.student_name + '</span></div>';
        }
        if (data.instructor_name) {
            html += '<div class="pc-info-row"><span class="label">Instructor</span><span>' + data.instructor_name + '</span></div>';
        }
        if (data.attendance_status) {
            var attIcon = data.attendance_status === 'present' ? '✅' : (data.attendance_status === 'absent' ? '❌' : '⚠️');
            html += '<div class="pc-info-row"><span class="label">Attendance</span><span>' + attIcon + ' ' + data.attendance_status + '</span></div>';
        }
        if (data.cancelled_by) {
            html += '<div class="pc-info-row"><span class="label">Cancelled by</span><span>' + data.cancelled_by + '</span></div>';
            if (data.cancel_reason) {
                html += '<div class="pc-info-row"><span class="label">Reason</span><span>' + data.cancel_reason + '</span></div>';
            }
        }

        // Pending reschedule request notice
        if (data.pending_reschedule) {
            var pr = data.pending_reschedule;
            html += '<div class="pc-reschedule-notice">⏳ <strong>' + pr.by + '</strong> requested reschedule to ' +
                pr.new_day + (pr.new_start ? ' at ' + pr.new_start : '') +
                (pr.note ? '<br><em>"' + pr.note + '"</em>' : '') +
                '<div class="d-flex gap-6 mt-8">' +
                '<button class="btn btn-sm btn-success" onclick="powerCalendar.approveRequest(' + pr.id + ')">✓ Approve</button>' +
                '<button class="btn btn-sm btn-danger" onclick="powerCalendar.rejectRequest(' + pr.id + ')">✗ Reject</button>' +
                '</div></div>';
        }

        $('#popupTitle').text(data.student_name || data.instructor_name || 'Meeting Details');
        $('#popupBody').html(html);

        // Action buttons
        var $footer = $('#popupFooter').empty();

        if (data.can_join && data.link) {
            $footer.append('<a href="' + data.link + '" target="_blank" class="btn btn-success">🎥 Join Meeting</a>');
        }
        if (data.can_reschedule) {
            $footer.append('<button class="btn btn-outline-primary" onclick="powerCalendar.openRescheduleModal(' + data.id + ')">✏️ Reschedule</button>');
        }
        if (data.can_request_reschedule) {
            $footer.append('<button class="btn btn-outline-primary" onclick="powerCalendar.openRequestRescheduleModal(' + data.id + ')">🔄 Request Reschedule</button>');
        }
        if (data.can_mark_attendance) {
            $footer.append('<button class="btn btn-outline-secondary" onclick="powerCalendar.openAttendanceModal(' + data.id + ')">📊 Mark Attendance</button>');
        }
        if (data.can_resend_invite) {
            $footer.append('<button class="btn btn-outline-secondary" onclick="powerCalendar.resendInvite(' + data.id + ')">📧 Resend Invite</button>');
        }
        if (data.can_cancel) {
            $footer.append('<button class="btn btn-outline-danger" onclick="powerCalendar.openCancelModal(' + data.id + ')">❌ Cancel</button>');
        }
    }

    function closePopup() {
        $('#eventPopup, #popupOverlay').hide();
    }

    // ─── Student Search ─────────────────────────────────────────────────────
    var selectedStudentIds = [];

    function bindStudentSearch() {
        var searchTimeout;
        $(document).on('input', '#studentSearchInput', function () {
            var q = $(this).val().trim();
            clearTimeout(searchTimeout);
            if (q.length < 2) { $('#studentSearchResults').hide(); return; }

            searchTimeout = setTimeout(function () {
                $.get(CALENDAR_BASE + '/students/search', { q: q }, function (users) {
                    var $res = $('#studentSearchResults').empty();
                    if (!users.length) {
                        $res.html('<div class="p-10 text-gray-400 font-12">No students found</div>').show();
                        return;
                    }
                    users.forEach(function (u) {
                        $res.append('<div class="p-10 cursor-pointer student-result" data-id="' + u.id + '" data-name="' + u.full_name + '" style="border-bottom:1px solid #f3f4f6;">' +
                            u.full_name + ' <span class="text-gray-400 font-11">(' + u.email + ')</span></div>');
                    });
                    $res.show();
                });
            }, 300);
        });

        $(document).on('click', '.student-result', function () {
            var id   = $(this).data('id');
            var name = $(this).data('name');
            $('#studentSearchResults').hide();
            $('#studentSearchInput').val('');

            if (selectedStudentIds.indexOf(id) !== -1) {
                return; // Already added
            }

            selectedStudentIds.push(id);
            var chip = $('<div class="pc-student-chip" data-id="' + id + '">' + name + '<span class="remove">&times;</span></div>');
            $('#selectedStudentsChips').append(chip);
        });

        // Remove student chip
        $(document).on('click', '.pc-student-chip .remove', function () {
            var chip = $(this).closest('.pc-student-chip');
            var id = chip.data('id');
            var index = selectedStudentIds.indexOf(id);
            if (index !== -1) {
                selectedStudentIds.splice(index, 1);
            }
            chip.remove();
        });

        $(document).on('click', '#scheduleRepeat', function () {
            $('#repeatOptions').toggle(this.checked);
            updateRepeatPreview();
        });

        $(document).on('input', '#repeatCount, #scheduleDay, #repeatType', updateRepeatPreview);
    }

    function updateRepeatPreview() {
        var count = parseInt($('#repeatCount').val()) || 2;
        var type  = $('#repeatType').val();
        var day   = $('#scheduleDay').val();
        if (!day) return;
        var interval = type === 'daily' ? 1 : 7;
        var d = new Date(day);
        d.setDate(d.getDate() + (count - 1) * interval);
        var last = d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        $('#repeatPreview').text(count + ' sessions, last on ' + last);
    }

    // ─── Modal Bindings ─────────────────────────────────────────────────────
    function bindModals() {
        // Close popup
        $('#btnClosePopup, #popupOverlay').on('click', closePopup);

        // Schedule class
        $('#btnScheduleClass').on('click', function () {
            // Reset selected students state
            selectedStudentIds = [];
            $('#selectedStudentsChips').empty();
            $('#studentSearchInput').val('');
            $('#scheduleClassModal').modal('show');
        });

        $('#btnConfirmSchedule').on('click', function () {
            var day       = $('#scheduleDay').val();
            var start     = $('#scheduleStart').val();
            var end       = $('#scheduleEnd').val();

            if (selectedStudentIds.length === 0) { alert('Please select at least one student.'); return; }
            if (!day || !start || !end) { alert('Please fill date and times.'); return; }

            var data = {
                _token:       CSRF,
                student_ids:  selectedStudentIds,
                day:          day,
                start_time:   start,
                end_time:     end,
                price:        $('#schedulePrice').val() || 0,
                is_demo:      $('#scheduleIsDemo').is(':checked') ? 1 : 0,
                repeat:       $('#scheduleRepeat').is(':checked') ? 1 : 0,
                repeat_type:  $('#repeatType').val(),
                repeat_count: $('#repeatCount').val(),
            };

            $('#scheduleSpinner').show();
            $('#btnConfirmSchedule').prop('disabled', true);

            $.post(CALENDAR_BASE + '/schedule-class', data, function (res) {
                showSuccess(res.message || 'Class scheduled!');
                $('#scheduleClassModal').modal('hide');
                calendar.refetchEvents();
                loadTodaySchedule();
            }).fail(function (xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error scheduling class.';
                alert(msg);
            }).always(function () {
                $('#scheduleSpinner').hide();
                $('#btnConfirmSchedule').prop('disabled', false);
            });
        });

        // Reschedule (instructor)
        $('#btnConfirmReschedule').on('click', function () {
            var id = $('#rescheduleBookingId').val();
            $.post(CALENDAR_BASE + '/booking/' + id + '/reschedule', {
                _token:         CSRF,
                new_day:        $('#rescheduleDay').val(),
                new_start_time: $('#rescheduleStart').val(),
                new_end_time:   $('#rescheduleEnd').val(),
            }, function (res) {
                showSuccess(res.message);
                $('#rescheduleModal').modal('hide');
                closePopup();
                calendar.refetchEvents();
            }).fail(handleAjaxError);
        });

        // Cancel
        $('#btnConfirmCancel').on('click', function () {
            var id = $('#cancelBookingId').val();
            $.post(CALENDAR_BASE + '/booking/' + id + '/cancel', {
                _token: CSRF,
                reason: $('#cancelReason').val(),
            }, function (res) {
                showSuccess(res.message);
                $('#cancelBookingModal').modal('hide');
                closePopup();
                calendar.refetchEvents();
                loadTodaySchedule();
            }).fail(handleAjaxError);
        });

        // Request reschedule (student)
        $('#btnConfirmRequestReschedule').on('click', function () {
            var id = $('#requestRescheduleBookingId').val();
            $.post(CALENDAR_BASE + '/booking/' + id + '/request-reschedule', {
                _token:         CSRF,
                new_day:        $('#requestRescheduleDay').val(),
                new_start_time: $('#requestRescheduleStart').val(),
                note:           $('#requestRescheduleNote').val(),
            }, function (res) {
                showSuccess(res.message);
                $('#requestRescheduleModal').modal('hide');
                closePopup();
            }).fail(handleAjaxError);
        });

        // Attendance
        $('#btnConfirmAttendance').on('click', function () {
            var id = $('#attendanceBookingId').val();
            $.post(CALENDAR_BASE + '/booking/' + id + '/attendance', {
                _token:            CSRF,
                attendance_status: $('#attendanceStatus').val(),
            }, function (res) {
                showSuccess(res.message);
                $('#attendanceModal').modal('hide');
                closePopup();
                calendar.refetchEvents();
            }).fail(handleAjaxError);
        });
    }

    // ─── Action Helpers (called by inline onclick) ──────────────────────────
    window.powerCalendar = {
        openPopup: function (id) { openEventPopup(id); },

        openRescheduleModal: function (id) {
            $('#rescheduleBookingId').val(id);
            $('#rescheduleDay').val('');
            $('#rescheduleStart').val('');
            $('#rescheduleEnd').val('');
            $('#rescheduleModal').modal('show');
        },

        openCancelModal: function (id) {
            $('#cancelBookingId').val(id);
            $('#cancelReason').val('');
            $('#cancelBookingModal').modal('show');
        },

        openAttendanceModal: function (id) {
            $('#attendanceBookingId').val(id);
            $('#attendanceModal').modal('show');
        },

        openRequestRescheduleModal: function (id) {
            $('#requestRescheduleBookingId').val(id);
            $('#requestRescheduleDay').val('');
            $('#requestRescheduleStart').val('');
            $('#requestRescheduleNote').val('');
            $('#requestRescheduleModal').modal('show');
        },

        approveRequest: function (requestId) {
            if (!confirm('Approve this reschedule request?')) return;
            $.post(CALENDAR_BASE + '/reschedule-request/' + requestId + '/approve', { _token: CSRF }, function (res) {
                showSuccess(res.message);
                closePopup();
                calendar.refetchEvents();
                loadTodaySchedule();
                loadRescheduleRequests();
            }).fail(handleAjaxError);
        },

        rejectRequest: function (requestId) {
            if (!confirm('Reject this reschedule request?')) return;
            $.post(CALENDAR_BASE + '/reschedule-request/' + requestId + '/reject', { _token: CSRF }, function (res) {
                showSuccess(res.message);
                closePopup();
                loadRescheduleRequests();
            }).fail(handleAjaxError);
        },

        resendInvite: function (id) {
            $.post(CALENDAR_BASE + '/booking/' + id + '/resend-invite', { _token: CSRF }, function (res) {
                showSuccess(res.message);
            }).fail(handleAjaxError);
        },
    };

    // ─── Utilities ──────────────────────────────────────────────────────────
    function showSuccess(msg) {
        var toast = $('<div class="alert alert-success" style="position:fixed;bottom:24px;right:24px;z-index:99999;border-radius:12px;padding:14px 24px;box-shadow:0 4px 20px rgba(0,0,0,.15);">' + msg + '</div>');
        $('body').append(toast);
        setTimeout(function () { toast.fadeOut(400, function () { $(this).remove(); }); }, 3000);
    }

    function handleAjaxError(xhr) {
        var msg = 'An error occurred.';
        if (xhr.responseJSON) {
            msg = xhr.responseJSON.message || msg;
            if (xhr.responseJSON.errors) {
                msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
            }
        }
        alert(msg);
    }

    function formatTime(ts) {
        var d = new Date(ts * 1000);
        return d.getHours().toString().padStart(2,'0') + ':' + d.getMinutes().toString().padStart(2,'0');
    }

})(jQuery);
