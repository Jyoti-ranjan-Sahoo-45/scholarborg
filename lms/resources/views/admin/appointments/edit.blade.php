@extends('admin.layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $pageTitle }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.appointments') }}">Appointments</a></div>
                <div class="breadcrumb-item">Edit / Reschedule</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12 col-md-8 offset-md-2">
                    <div class="card">
                        <div class="card-header">
                            <h4>Edit Meeting Reservation #{{ $appointment->id }}</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.appointments.update', $appointment->id) }}" method="POST">
                                @csrf

                                {{-- Student --}}
                                <div class="form-group">
                                    <label class="input-label">Student</label>
                                    <select name="student_id" data-plugin-selectTwo class="form-control populate" required>
                                        <option value="">-- Select Student --</option>
                                        @foreach($students as $student)
                                            <option value="{{ $student->id }}" {{ $appointment->user_id == $student->id ? 'selected' : '' }}>
                                                {{ $student->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Instructor --}}
                                <div class="form-group">
                                    <label class="input-label">Instructor</label>
                                    <select name="instructor_id" id="js-admin-instructor-select" data-plugin-selectTwo class="form-control populate" required>
                                        <option value="">-- Select Instructor --</option>
                                        @foreach($instructors as $instructor)
                                            @php $currentInstructorId = $appointment->meeting->creator_id ?? null; @endphp
                                            <option value="{{ $instructor->id }}" {{ $currentInstructorId == $instructor->id ? 'selected' : '' }}>
                                                {{ $instructor->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Date --}}
                                <div class="form-group">
                                    <label class="input-label">Date</label>
                                    <input type="date" name="day" class="form-control" value="{{ $appointment->day }}" required>
                                </div>

                                {{-- Time Slot --}}
                                <div class="form-group">
                                    <label class="input-label">Time Slot</label>
                                    <select name="meeting_time_id" id="js-admin-time-select" class="form-control" required>
                                        {{-- Pre-populate with current instructor's slots --}}
                                        <option value="{{ $appointment->meeting_time_id }}" selected>
                                            {{ optional($appointment->meetingTime)->day_label }} | {{ optional($appointment->meetingTime)->time }}
                                        </option>
                                    </select>
                                    <small class="text-gray-500">Change instructor above to reload available slots.</small>
                                </div>

                                {{-- Meeting Type --}}
                                <div class="form-group">
                                    <label class="input-label">Meeting Type</label>
                                    <select name="meeting_type" class="form-control" required>
                                        <option value="online" {{ $appointment->meeting_type == 'online' ? 'selected' : '' }}>Online</option>
                                        <option value="in_person" {{ $appointment->meeting_type == 'in_person' ? 'selected' : '' }}>In Person</option>
                                    </select>
                                </div>

                                {{-- Status --}}
                                <div class="form-group">
                                    <label class="input-label">Status</label>
                                    <select name="status" class="form-control" required>
                                        <option value="pending" {{ $appointment->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="open" {{ $appointment->status == 'open' ? 'selected' : '' }}>Open</option>
                                        <option value="finished" {{ $appointment->status == 'finished' ? 'selected' : '' }}>Finished</option>
                                        <option value="canceled" {{ $appointment->status == 'canceled' ? 'selected' : '' }}>Canceled</option>
                                    </select>
                                </div>

                                {{-- Meeting Link --}}
                                <div class="form-group">
                                    <label class="input-label">Meeting Join Link</label>
                                    <input type="url" name="link" class="form-control" value="{{ $appointment->link }}" placeholder="https://...">
                                </div>

                                {{-- Is Demo --}}
                                <div class="form-group d-flex align-items-center">
                                    <div class="custom-switch mr-8">
                                        <input id="isDemoSwitch" type="checkbox" name="is_demo" value="1" class="custom-control-input" {{ $appointment->is_demo ? 'checked' : '' }}>
                                        <label class="custom-control-label cursor-pointer" for="isDemoSwitch"></label>
                                    </div>
                                    <label class="cursor-pointer mb-0" for="isDemoSwitch">Mark as Demo Booking</label>
                                </div>

                                <div class="d-flex align-items-center gap-8 mt-20">
                                    <button type="submit" class="btn btn-primary btn-lg">Save Changes</button>
                                    <a href="{{ route('admin.appointments') }}" class="btn btn-transparent btn-lg">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')
<script>
$(document).ready(function () {
    // When instructor changes, reload time slots
    $('#js-admin-instructor-select').on('change', function () {
        var instructorId = $(this).val();
        var $timeSelect = $('#js-admin-time-select');
        $timeSelect.html('<option value="">Loading...</option>');

        if (!instructorId) {
            $timeSelect.html('<option value="">-- Select instructor first --</option>');
            return;
        }

        $.get('{{ route('admin.appointments.getTimes') }}', { instructor_id: instructorId }, function (data) {
            var html = '<option value="">-- Select Time Slot --</option>';
            data.forEach(function (slot) {
                html += '<option value="' + slot.id + '">' + slot.day_label + ' | ' + slot.time + '</option>';
            });
            $timeSelect.html(html);
        }).fail(function () {
            $timeSelect.html('<option value="">No slots found</option>');
        });
    });
});
</script>
@endpush
