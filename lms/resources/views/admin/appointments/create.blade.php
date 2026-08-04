@extends('admin.layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $pageTitle }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.appointments') }}">Appointments</a></div>
                <div class="breadcrumb-item">Create</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12 col-md-8 offset-md-2">
                    <div class="card">
                        <div class="card-header">
                            <h4>Manually Book a Meeting Reservation</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.appointments.store') }}" method="POST">
                                @csrf

                                {{-- Student --}}
                                <div class="form-group">
                                    <label class="input-label">Student</label>
                                    <select name="student_id" data-plugin-selectTwo class="form-control populate" required>
                                        <option value="">-- Select Student --</option>
                                        @foreach($students as $student)
                                            <option value="{{ $student->id }}">{{ $student->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Instructor --}}
                                <div class="form-group">
                                    <label class="input-label">Instructor</label>
                                    <select name="instructor_id" id="js-admin-instructor-select" data-plugin-selectTwo class="form-control populate" required>
                                        <option value="">-- Select Instructor --</option>
                                        @foreach($instructors as $instructor)
                                            <option value="{{ $instructor->id }}">{{ $instructor->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Date --}}
                                <div class="form-group">
                                    <label class="input-label">Date</label>
                                    <input type="date" name="day" class="form-control" required>
                                </div>

                                {{-- Time Slot (loaded via AJAX after selecting instructor) --}}
                                <div class="form-group">
                                    <label class="input-label">Time Slot</label>
                                    <select name="meeting_time_id" id="js-admin-time-select" class="form-control" required>
                                        <option value="">-- Select instructor first --</option>
                                    </select>
                                </div>

                                {{-- Meeting Type --}}
                                <div class="form-group">
                                    <label class="input-label">Meeting Type</label>
                                    <select name="meeting_type" class="form-control" required>
                                        <option value="online">Online</option>
                                        <option value="in_person">In Person</option>
                                    </select>
                                </div>

                                {{-- Status --}}
                                <div class="form-group">
                                    <label class="input-label">Status</label>
                                    <select name="status" class="form-control">
                                        <option value="pending">Pending</option>
                                        <option value="open">Open</option>
                                        <option value="finished">Finished</option>
                                        <option value="canceled">Canceled</option>
                                    </select>
                                </div>

                                {{-- Meeting Link --}}
                                <div class="form-group">
                                    <label class="input-label">Meeting Link (optional)</label>
                                    <input type="url" name="link" class="form-control" placeholder="https://...">
                                </div>

                                {{-- Is Demo --}}
                                <div class="form-group d-flex align-items-center">
                                    <div class="custom-switch mr-8">
                                        <input id="isDemoSwitch" type="checkbox" name="is_demo" value="1" class="custom-control-input">
                                        <label class="custom-control-label cursor-pointer" for="isDemoSwitch"></label>
                                    </div>
                                    <label class="cursor-pointer mb-0" for="isDemoSwitch">Mark as Demo Booking</label>
                                </div>

                                <div class="d-flex align-items-center gap-8 mt-20">
                                    <button type="submit" class="btn btn-primary btn-lg">Save Reservation</button>
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
