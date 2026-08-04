@if(session()->has('parent_proxy_student_id') && !empty($proxyStudent))
    <div class="alert alert-warning border-0 rounded-0 mb-0 d-flex align-items-center justify-content-between px-24 py-12" style="background-color: #fff3cd; border-bottom: 2px solid #ffeba2 !important; position: sticky; top: 0; z-index: 1050;">
        <div class="d-flex align-items-center">
            <span class="font-18 mr-8">👁️</span>
            <div>
                <strong class="text-dark font-14">Viewing as Student: {{ $proxyStudent->full_name }}</strong>
                <span class="text-gray-600 font-12 d-block d-sm-inline ml-sm-8">(Read-Only Student Mode Active)</span>
            </div>
        </div>
        <div>
            <a href="/panel/parent/exit-student-view" class="btn btn-sm btn-primary font-12 font-weight-bold px-16">
                <i class="fas fa-undo mr-4"></i> Exit Student View
            </a>
        </div>
    </div>
@endif
