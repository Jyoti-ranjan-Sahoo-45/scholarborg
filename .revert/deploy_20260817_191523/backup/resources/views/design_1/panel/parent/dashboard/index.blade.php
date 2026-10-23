@extends('design_1.panel.layouts.panel')

@push('styles_top')
<style>
    .parent-stat-card {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border: 1px solid #f0f0f0;
        transition: all 0.3s ease;
    }
    .parent-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    }
    .child-selector-card {
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        transition: all 0.2s;
    }
    .child-selector-card.active {
        background: #eff6ff;
        border-color: #3b82f6;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">
    {{-- Header Banner & Child Selector --}}
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-24 pb-16 border-bottom">
        <div>
            <h1 class="font-24 font-weight-bold text-dark">Parent Dashboard</h1>
            <p class="font-14 text-gray-500 mt-4">Monitor academic progress, quiz results, attendance, and manage your children's learning journey.</p>
        </div>
        <div class="mt-16 mt-md-0">
            <a href="/panel/parent/children" class="btn btn-primary btn-sm px-20">
                <i class="fas fa-user-plus mr-8"></i> Manage / Link Children
            </a>
        </div>
    </div>

    @if($children->count() > 0)
        {{-- Children Selector Bar --}}
        <div class="mb-24">
            <h3 class="font-16 font-weight-bold text-dark mb-12">Linked Children</h3>
            <div class="row">
                @foreach($children as $child)
                    @php $isActive = (!empty($selectedChild) && $selectedChild->id == $child->id); @endphp
                    <div class="col-12 col-sm-6 col-md-4 mb-16">
                        <div class="child-selector-card {{ $isActive ? 'active' : '' }} d-flex align-items-center justify-content-between">
                            <a href="/panel/parent/dashboard?child_id={{ $child->id }}" class="d-flex align-items-center text-decoration-none flex-grow-1">
                                <img src="{{ $child->getAvatar(48) }}" class="rounded-circle mr-12" width="48" height="48" alt="{{ $child->full_name }}">
                                <div>
                                    <h4 class="font-14 font-weight-bold text-dark mb-2">{{ $child->full_name }}</h4>
                                    <span class="font-12 text-gray-500">Code: <strong>{{ $child->student_code ?? $child->getStudentCode() }}</strong></span>
                                </div>
                            </a>
                            <div class="ml-12">
                                <a href="/panel/parent/switch-student-view/{{ $child->id }}" class="btn btn-outline-primary btn-sm font-11 px-10 py-4" title="View Dashboard as Student">
                                    <i class="fas fa-eye mr-4"></i> Student View
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        @if($selectedChild)
            {{-- Overview Stats for Selected Child --}}
            <div class="row mb-24">
                <div class="col-6 col-md-3 mb-16">
                    <div class="parent-stat-card d-flex align-items-center">
                        <div class="rounded-circle bg-primary-light p-12 mr-16 text-primary">
                            <i class="fas fa-book-open fa-2x"></i>
                        </div>
                        <div>
                            <span class="font-24 font-weight-bold text-dark d-block">{{ $stats['totalCourses'] }}</span>
                            <span class="font-12 text-gray-500">Enrolled Courses</span>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3 mb-16">
                    <div class="parent-stat-card d-flex align-items-center">
                        <div class="rounded-circle bg-success-light p-12 mr-16 text-success">
                            <i class="fas fa-poll-h fa-2x"></i>
                        </div>
                        <div>
                            <span class="font-24 font-weight-bold text-dark d-block">{{ $stats['quizAvgGrade'] }}</span>
                            <span class="font-12 text-gray-500">Quiz Avg Score</span>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3 mb-16">
                    <div class="parent-stat-card d-flex align-items-center">
                        <div class="rounded-circle bg-warning-light p-12 mr-16 text-warning">
                            <i class="fas fa-tasks fa-2x"></i>
                        </div>
                        <div>
                            <span class="font-24 font-weight-bold text-dark d-block">{{ $stats['assignmentsCount'] }}</span>
                            <span class="font-12 text-gray-500">Assignments</span>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3 mb-16">
                    <div class="parent-stat-card d-flex align-items-center">
                        <div class="rounded-circle bg-info-light p-12 mr-16 text-info">
                            <i class="fas fa-calendar-check fa-2x"></i>
                        </div>
                        <div>
                            <span class="font-24 font-weight-bold text-dark d-block">{{ $stats['attendanceCount'] }}</span>
                            <span class="font-12 text-gray-500">Attended Sessions</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Quiz Results & Assignments Tables --}}
            <div class="row mb-24">
                <div class="col-12 col-lg-6 mb-24">
                    <div class="card card-custom p-20 h-100">
                        <div class="d-flex align-items-center justify-content-between mb-16">
                            <h3 class="font-16 font-weight-bold text-dark">Recent Quiz Results</h3>
                        </div>

                        @if($recentQuizzes->count() > 0)
                            <div class="table-responsive">
                                <table class="table custom-table font-13">
                                    <thead>
                                        <tr>
                                            <th>Quiz</th>
                                            <th>Grade</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentQuizzes as $quizResult)
                                            <tr>
                                                <td class="font-weight-bold">{{ $quizResult->quiz->title ?? 'Quiz' }}</td>
                                                <td>{{ $quizResult->user_grade }} / {{ $quizResult->quiz->total_mark ?? 100 }}</td>
                                                <td>
                                                    @if($quizResult->status == 'passed')
                                                        <span class="badge badge-success">Passed</span>
                                                    @else
                                                        <span class="badge badge-danger">Failed</span>
                                                    @endif
                                                </td>
                                                <td class="text-gray-500">{{ dateTimeFormat($quizResult->created_at, 'j M Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-32 text-gray-500 font-14">
                                No quiz results recorded yet for {{ $selectedChild->full_name }}.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-12 col-lg-6 mb-24">
                    <div class="card card-custom p-20 h-100">
                        <div class="d-flex align-items-center justify-content-between mb-16">
                            <h3 class="font-16 font-weight-bold text-dark">Enrolled Courses Overview</h3>
                        </div>

                        @if($purchasedCourses->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($purchasedCourses as $course)
                                    <div class="list-group-item px-0 py-12 border-bottom d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $course->getImage() }}" class="rounded mr-12" width="48" height="48" style="object-fit: cover;">
                                            <div>
                                                <h4 class="font-14 font-weight-bold text-dark mb-2">{{ $course->title }}</h4>
                                                <span class="font-12 text-gray-500">Teacher: {{ $course->teacher->full_name ?? 'Instructor' }}</span>
                                            </div>
                                        </div>
                                        <a href="/panel/meeting/reservation?teacher_id={{ $course->teacher_id }}" class="btn btn-outline-secondary btn-sm font-11">
                                            Book Teacher
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-32 text-gray-500 font-14">
                                {{ $selectedChild->full_name }} is not enrolled in any courses yet.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    @else
        {{-- Empty State (No linked children) --}}
        <div class="card card-custom p-40 text-center">
            <div class="size-80 rounded-circle bg-gray-200 d-inline-flex align-items-center justify-content-center mx-auto mb-20 text-gray-600">
                <i class="fas fa-child fa-3x"></i>
            </div>
            <h3 class="font-20 font-weight-bold text-dark mb-8">No Linked Children Found</h3>
            <p class="font-14 text-gray-500 max-w-500 mx-auto mb-24">Link your child's student account using their unique Student Access Code or Email address to start tracking their progress, quizzes, and live classes.</p>
            <div>
                <a href="/panel/parent/children" class="btn btn-primary px-32 py-12 font-14 font-weight-bold">
                    <i class="fas fa-plus-circle mr-8"></i> Link Your First Child
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
