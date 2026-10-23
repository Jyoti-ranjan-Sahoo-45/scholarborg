@extends('admin.layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $pageTitle }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a></div>
                <div class="breadcrumb-item">{{ $pageTitle }}</div>
            </div>
        </div>

        <div class="section-body">
            <!-- Search & Filters -->
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="row align-items-center">
                        <div class="col-md-4 mb-2 mb-md-0">
                            <label class="form-label font-12 text-gray-500">Call Status</label>
                            <select name="status" class="form-control font-12">
                                <option value="">All Statuses</option>
                                <option value="initiated" {{ request('status') == 'initiated' ? 'selected' : '' }}>Initiated</option>
                                <option value="ringing" {{ request('status') == 'ringing' ? 'selected' : '' }}>Ringing</option>
                                <option value="connected" {{ request('status') == 'connected' ? 'selected' : '' }}>Connected</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="missed" {{ request('status') == 'missed' ? 'selected' : '' }}>Missed</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-2 mb-md-0">
                            <label class="form-label font-12 text-gray-500">Audit Status</label>
                            <select name="audit_status" class="form-control font-12">
                                <option value="">All Audits</option>
                                <option value="pending" {{ request('audit_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="reviewed" {{ request('audit_status') == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                                <option value="flagged" {{ request('audit_status') == 'flagged' ? 'selected' : '' }}>Flagged</option>
                            </select>
                        </div>
                        <div class="col-md-4 align-self-end mt-10 mt-md-0">
                            <button type="submit" class="btn btn-primary w-100 font-12">Filter Logs</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Call Logs Card -->
            <section class="card">
                <div class="card-header justify-content-between">
                    <div>
                        <h5 class="font-14 mb-0">Auditable Voice Logs</h5>
                        <p class="font-12 mt-4 mb-0 text-gray-500">Review platform VoIP call records and listen to cloud recordings synced with OneDrive.</p>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table custom-table text-center font-14">
                            <thead>
                                <tr>
                                    <th class="text-left">Caller (Student/Teacher)</th>
                                    <th class="text-left">Recipient (Teacher/Student)</th>
                                    <th class="text-center">Date & Time</th>
                                    <th class="text-center">Duration</th>
                                    <th class="text-center">Call Status</th>
                                    <th class="text-center">Audit Status</th>
                                    <th class="text-center">Recording</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($calls as $call)
                                    <tr>
                                        <td class="text-left">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $call->caller->getAvatar() }}" class="size-36 rounded-circle border mr-10" alt="">
                                                <div>
                                                    <span class="d-block font-weight-bold text-dark-50">{{ $call->caller->full_name }}</span>
                                                    <span class="d-block text-gray font-12">{{ $call->caller->role->caption }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-left">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $call->receiver->getAvatar() }}" class="size-36 rounded-circle border mr-10" alt="">
                                                <div>
                                                    <span class="d-block font-weight-bold text-dark-50">{{ $call->receiver->full_name }}</span>
                                                    <span class="d-block text-gray font-12">{{ $call->receiver->role->caption }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="d-block">{{ dateTimeFormat($call->created_at, 'j M Y | H:i') }}</span>
                                        </td>
                                        <td>
                                            @if($call->duration)
                                                <span>{{ gmdate("i:s", $call->duration) }} mins</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @switch($call->status)
                                                @case('completed')
                                                    <span class="badge badge-success">Completed</span>
                                                    @break
                                                @case('connected')
                                                    <span class="badge badge-primary">Connected</span>
                                                    @break
                                                @case('missed')
                                                    <span class="badge badge-warning">Missed</span>
                                                    @break
                                                @case('rejected')
                                                    <span class="badge badge-danger">Rejected</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-secondary">{{ ucfirst($call->status) }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            @switch($call->audit_status)
                                                @case('reviewed')
                                                    <span class="text-success font-weight-bold">Reviewed</span>
                                                    @break
                                                @case('flagged')
                                                    <span class="text-danger font-weight-bold">Flagged</span>
                                                    @break
                                                @default
                                                    <span class="text-warning font-weight-bold">Pending</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            @if($call->recording_file)
                                                <a href="{{ $call->recording_file }}" target="_blank" class="btn btn-sm btn-light text-primary border-gray-300 d-inline-flex-center">
                                                    <span class="mr-4">🎧 Play Audio</span>
                                                </a>
                                            @else
                                                <span class="text-gray-400 font-12">No recording</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" 
                                                    class="btn btn-sm btn-primary btn-audit"
                                                    data-id="{{ $call->id }}"
                                                    data-status="{{ $call->audit_status }}"
                                                    data-notes="{{ $call->audit_notes }}"
                                                    data-caller="{{ $call->caller->full_name }}"
                                                    data-receiver="{{ $call->receiver->full_name }}">
                                                Audit
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer text-center">
                    {{ $calls->appends(request()->input())->links() }}
                </div>
            </section>
        </div>
    </section>

    <!-- Auditing Modal -->
    <div class="modal fade" id="auditModal" tabindex="-1" role="dialog" aria-labelledby="auditModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="auditModalLabel">Audit Call Record</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="auditForm">
                    @csrf
                    <input type="hidden" id="auditCallId">
                    <div class="modal-body">
                        <div class="form-group mb-10">
                            <label class="form-label font-weight-bold">Call Summary</label>
                            <p class="font-13 mb-0 text-dark-50" id="auditCallSummary"></p>
                        </div>
                        <div class="form-group mb-15">
                            <label class="form-label">Audit Assessment</label>
                            <select id="auditStatusField" class="form-control">
                                <option value="pending">Pending</option>
                                <option value="reviewed">Reviewed (Clean)</option>
                                <option value="flagged">Flagged (Unapproved/Issues)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Internal Audit Notes</label>
                            <textarea id="auditNotesField" class="form-control" rows="4" placeholder="Enter auditing details, guidelines followed, or concerns..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="btnSaveAudit">Save Review</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts_bottom')
    <script>
        $(document).ready(function() {
            // Open modal and pre-fill details
            $('.btn-audit').click(function() {
                var id = $(this).data('id');
                var status = $(this).data('status');
                var notes = $(this).data('notes') || '';
                var caller = $(this).data('caller');
                var receiver = $(this).data('receiver');

                $('#auditCallId').val(id);
                $('#auditStatusField').val(status);
                $('#auditNotesField').val(notes);
                $('#auditCallSummary').text(caller + ' ➔ ' + receiver + ' (Log #' + id + ')');

                $('#auditModal').modal('show');
            });

            // Submit audit form
            $('#auditForm').submit(function(e) {
                e.preventDefault();
                var id = $('#auditCallId').val();
                var status = $('#auditStatusField').val();
                var notes = $('#auditNotesField').val();

                $('#btnSaveAudit').attr('disabled', true).text('Saving...');

                $.ajax({
                    url: '{{ getAdminPanelUrl() }}/calls/' + id + '/update',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        audit_status: status,
                        audit_notes: notes
                    },
                    success: function(res) {
                        $('#auditModal').modal('hide');
                        window.location.reload();
                    },
                    error: function(xhr) {
                        alert('Failed to save audit review: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'Unknown error'));
                        $('#btnSaveAudit').attr('disabled', false).text('Save Review');
                    }
                });
            });
        });
    </script>
@endpush
