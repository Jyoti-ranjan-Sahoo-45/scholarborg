@extends('design_1.panel.layouts.panel')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-24 pb-16 border-bottom">
        <div>
            <h1 class="font-24 font-weight-bold text-dark">My Children</h1>
            <p class="font-14 text-gray-500 mt-4">Link, manage, and toggle student views for your connected student accounts.</p>
        </div>
        <div class="mt-16 mt-md-0">
            <a href="/panel/parent/dashboard" class="btn btn-outline-secondary btn-sm px-20">
                <i class="fas fa-arrow-left mr-8"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <div class="row">
        {{-- Link Child Card --}}
        <div class="col-12 col-lg-5 mb-24">
            <div class="card card-custom p-24">
                <h3 class="font-18 font-weight-bold text-dark mb-8">Link a Child Account</h3>
                <p class="font-13 text-gray-500 mb-20">Enter your child's <strong>Student Access Code</strong> (e.g. <code>SCH-84920</code>) or registered <strong>Email Address</strong> to instantly connect their account.</p>

                <form action="/panel/parent/children/link" method="POST">
                    {{ csrf_field() }}
                    <div class="form-group mb-20">
                        <label class="font-14 font-weight-bold text-dark mb-8">Student Access Code or Email</label>
                        <div class="input-group">
                            <input type="text" name="student_code_or_email" class="form-control @error('student_code_or_email') is-invalid @enderror" placeholder="e.g. SCH-84920 or student@email.com" required>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary px-20">
                                    <i class="fas fa-link mr-4"></i> Link Child
                                </button>
                            </div>
                        </div>
                        @error('student_code_or_email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </form>

                <div class="bg-gray-100 p-16 rounded-10 mt-16 border">
                    <h4 class="font-13 font-weight-bold text-dark mb-4"><i class="fas fa-lightbulb text-warning mr-6"></i> Where to find Student Access Code?</h4>
                    <p class="font-12 text-gray-600 mb-0">Students can find their 8-character Access Code on their Student Panel Dashboard header or profile settings page.</p>
                </div>
            </div>
        </div>

        {{-- Linked Children List --}}
        <div class="col-12 col-lg-7 mb-24">
            <div class="card card-custom p-24">
                <h3 class="font-18 font-weight-bold text-dark mb-16">Connected Children ({{ $children->count() }})</h3>

                @if($children->count() > 0)
                    <div class="table-responsive">
                        <table class="table custom-table font-13">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Access Code</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($children as $child)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $child->getAvatar(40) }}" class="rounded-circle mr-12" width="40" height="40" alt="{{ $child->full_name }}">
                                                <div>
                                                    <span class="font-14 font-weight-bold text-dark d-block">{{ $child->full_name }}</span>
                                                    <span class="font-12 text-gray-500">{{ $child->email }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <code class="font-13 font-weight-bold text-primary">{{ $child->student_code ?? $child->getStudentCode() }}</code>
                                        </td>
                                        <td>
                                            <span class="badge badge-success">Active</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <a href="/panel/parent/switch-student-view/{{ $child->id }}" class="btn btn-outline-primary btn-sm font-11 mr-8" title="Toggle Student View">
                                                    <i class="fas fa-eye mr-2"></i> Student View
                                                </a>
                                                <a href="/panel/parent/children/unlink/{{ $child->id }}" class="btn btn-outline-danger btn-sm font-11" onclick="return confirm('Are you sure you want to unlink {{ $child->full_name }}?')" title="Unlink Child">
                                                    <i class="fas fa-unlink"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-40 text-gray-500 font-14 border rounded-10 bg-gray-50">
                        <i class="fas fa-users-slash fa-2x mb-12 d-block text-gray-400"></i>
                        No children linked to your account yet. Use the form on the left to link your child.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
