@extends('design_1.panel.layouts.panel')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center mb-24">
                <x-iconsax-lin-people class="icons text-primary mr-8" width="24px" height="24px"/>
                <h4 class="font-20 font-weight-bold">People & Connections</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- LEFT: Search & Pending Requests -->
        <div class="col-lg-4 mb-24">

            <!-- Search for People -->
            <div class="bg-white rounded-16 p-24 mb-16 shadow-sm">
                <h6 class="font-16 font-weight-bold mb-16 d-flex align-items-center">
                    <x-iconsax-lin-search-normal class="icons text-primary mr-8" width="18px" height="18px"/>
                    Find People
                </h6>
                <div class="form-group mb-16">
                    <input type="text" id="userSearchInput" class="form-control rounded-12"
                           placeholder="Search by name or email..." autocomplete="off">
                </div>
                <div id="searchResults"></div>
            </div>

            <!-- Pending Incoming Requests -->
            @if($pendingRequests->count() > 0)
            <div class="bg-white rounded-16 p-24 mb-16 shadow-sm">
                <h6 class="font-16 font-weight-bold mb-16 d-flex align-items-center">
                    <x-iconsax-lin-notification class="icons text-warning mr-8" width="18px" height="18px"/>
                    Incoming Requests
                    <span class="ml-auto badge badge-warning text-white rounded-pill px-10">{{ $pendingRequests->count() }}</span>
                </h6>
                @foreach($pendingRequests as $req)
                <div class="d-flex align-items-center py-12 border-bottom" id="req-{{ $req->id }}">
                    <div class="size-48 rounded-circle bg-gray-200 flex-shrink-0">
                        <img src="{{ $req->sender ? $req->sender->getAvatar(48) : '' }}" alt="" class="img-cover rounded-circle">
                    </div>
                    <div class="ml-12 flex-1 min-width-0">
                        <div class="font-14 font-weight-bold text-dark text-truncate">{{ $req->sender->full_name ?? '' }}</div>
                        <div class="font-12 text-gray-500 text-truncate">{{ $req->sender->email ?? '' }}</div>
                    </div>
                    <div class="d-flex flex-column ml-8" style="gap:4px;">
                        <button onclick="respondRequest({{ $req->id }}, 'accept')"
                                class="btn btn-sm btn-primary rounded-8 px-12 font-12">Accept</button>
                        <button onclick="respondRequest({{ $req->id }}, 'decline')"
                                class="btn btn-sm btn-outline-secondary rounded-8 px-12 font-12">Decline</button>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Outgoing Pending Requests -->
            @if($sentRequests->count() > 0)
            <div class="bg-white rounded-16 p-24 shadow-sm">
                <h6 class="font-16 font-weight-bold mb-16 d-flex align-items-center">
                    <x-iconsax-lin-send-2 class="icons text-gray-500 mr-8" width="18px" height="18px"/>
                    Sent Requests
                </h6>
                @foreach($sentRequests as $req)
                <div class="d-flex align-items-center py-10 border-bottom" id="sent-{{ $req->id }}">
                    <div class="size-40 rounded-circle bg-gray-200 flex-shrink-0">
                        <img src="{{ $req->receiver ? $req->receiver->getAvatar(40) : '' }}" alt="" class="img-cover rounded-circle">
                    </div>
                    <div class="ml-12 flex-1 min-width-0">
                        <div class="font-13 font-weight-bold text-dark text-truncate">{{ $req->receiver->full_name ?? '' }}</div>
                        <span class="badge badge-light font-11 text-gray-500">Pending</span>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- RIGHT: Accepted Connections (Contact List) -->
        <div class="col-lg-8">
            <div class="bg-white rounded-16 p-24 shadow-sm">
                <h6 class="font-16 font-weight-bold mb-20 d-flex align-items-center">
                    <x-iconsax-lin-people class="icons text-success mr-8" width="18px" height="18px"/>
                    My Connections
                    <span class="ml-auto font-13 font-weight-normal text-gray-500">{{ $connections->count() }} people</span>
                </h6>

                @if($connections->count() > 0)
                <div class="row">
                    @foreach($connections as $conn)
                    <div class="col-md-6 col-lg-4 mb-16">
                        <div class="d-flex flex-column align-items-center bg-gray-50 rounded-16 p-16 text-center h-100 connection-card">
                            <div class="size-64 rounded-circle bg-gray-200 mb-12">
                                <img src="{{ $conn->other_party ? $conn->other_party->getAvatar(64) : '' }}"
                                     alt="" class="img-cover rounded-circle">
                            </div>
                            <div class="font-14 font-weight-bold text-dark mb-4 text-truncate w-100">
                                {{ $conn->other_party->full_name ?? '' }}
                            </div>
                            <div class="font-12 text-gray-500 mb-16 text-truncate w-100">
                                {{ ucfirst($conn->other_party->role ?? '') }}
                            </div>
                            <div class="d-flex" style="gap:8px;">
                                <button onclick="openChat({{ $conn->other_party->id ?? 0 }})"
                                        class="btn btn-sm btn-primary rounded-8 px-12 font-12">
                                    <x-iconsax-lin-message class="icons mr-4" width="14px" height="14px"/> Chat
                                </button>
                                <button onclick="initiateCall({{ $conn->other_party->id ?? 0 }})"
                                        class="btn btn-sm btn-outline-success rounded-8 px-12 font-12">
                                    <x-iconsax-lin-call class="icons mr-4" width="14px" height="14px"/> Call
                                </button>
                            </div>
                            <button onclick="removeConnection({{ $conn->id }})"
                                    class="btn btn-link btn-sm font-11 text-danger mt-8 p-0">Remove</button>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-48">
                    <x-iconsax-lin-people class="icons text-gray-300 mb-16" width="48px" height="48px"/>
                    <p class="font-14 text-gray-500">No connections yet. Search for people on the left to get started!</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Toast notification -->
<div id="connToast" class="position-fixed" style="bottom:24px;right:24px;z-index:9999;display:none;">
    <div class="bg-dark text-white rounded-12 px-20 py-12 font-13 shadow-lg" id="connToastMsg"></div>
</div>
@endsection

@push('scripts_bottom')
<script>
const csrfToken = '{{ csrf_token() }}';

/* ── Search ─────────────────────────────── */
let searchTimer;
document.getElementById('userSearchInput').addEventListener('input', function() {
    clearTimeout(searchTimer);
    const q = this.value.trim();
    if (q.length < 2) {
        document.getElementById('searchResults').innerHTML = '';
        return;
    }
    searchTimer = setTimeout(() => doSearch(q), 350);
});

function doSearch(q) {
    fetch('/panel/connections/search-users?q=' + encodeURIComponent(q))
        .then(r => r.json())
        .then(data => renderSearchResults(data.users || []));
}

function renderSearchResults(users) {
    const el = document.getElementById('searchResults');
    if (!users.length) {
        el.innerHTML = '<p class="text-gray-500 font-13 text-center py-12">No users found.</p>';
        return;
    }
    el.innerHTML = users.map(u => {
        let btn = '';
        if (!u.connection_status) {
            btn = `<button onclick="sendRequest(${u.id}, this)" class="btn btn-sm btn-primary rounded-8 px-10 font-12">Connect</button>`;
        } else if (u.connection_status === 'pending') {
            if (u.is_sender) {
                btn = `<span class="badge badge-light font-11 text-gray-500">Pending</span>`;
            } else {
                btn = `<button onclick="acceptFromSearch(${u.connection_id}, this)" class="btn btn-sm btn-success rounded-8 px-10 font-12">Accept</button>`;
            }
        } else if (u.connection_status === 'accepted') {
            btn = `<span class="badge badge-success text-white font-11">Connected</span>`;
        }
        return `<div class="d-flex align-items-center py-10 border-bottom">
            <div class="size-40 rounded-circle bg-gray-200 flex-shrink-0">
                <img src="${u.avatar}" alt="" class="img-cover rounded-circle">
            </div>
            <div class="ml-10 flex-1 min-width-0">
                <div class="font-13 font-weight-bold text-dark text-truncate">${u.full_name}</div>
                <div class="font-12 text-gray-500 text-truncate">${u.email}</div>
            </div>
            <div class="ml-8 flex-shrink-0">${btn}</div>
        </div>`;
    }).join('');
}

/* ── Send Request ────────────────────────── */
function sendRequest(receiverId, btn) {
    btn.disabled = true;
    btn.textContent = '...';
    fetch('/panel/connections/send-request', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken},
        body: JSON.stringify({receiver_id: receiverId})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            btn.outerHTML = `<span class="badge badge-light font-11 text-gray-500">Pending</span>`;
            showToast('Connection request sent!');
        } else {
            btn.disabled = false;
            btn.textContent = 'Connect';
            showToast(data.error || 'Error sending request.');
        }
    });
}

/* ── Respond to Request ──────────────────── */
function respondRequest(id, action) {
    fetch(`/panel/connections/${id}/${action}`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken},
        body: JSON.stringify({})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const el = document.getElementById('req-' + id);
            if (el) el.remove();
            showToast(action === 'accept' ? 'Connection accepted! Reload to see them in your contacts.' : 'Request declined.');
            if (action === 'accept') setTimeout(() => location.reload(), 1500);
        }
    });
}

function acceptFromSearch(id, btn) {
    respondRequest(id, 'accept');
}

/* ── Open Chat ───────────────────────────── */
function openChat(receiverId) {
    fetch('/panel/public-chat/start-direct', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken},
        body: JSON.stringify({receiver_id: receiverId})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) window.location.href = data.redirect;
        else showToast(data.error || 'Could not open chat.');
    });
}

/* ── Initiate Call ───────────────────────── */
function initiateCall(receiverId) {
    if (typeof window.initiateCallGlobal === 'function') {
        window.initiateCallGlobal(receiverId);
    } else {
        fetch('/panel/audio-call/initiate', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken},
            body: JSON.stringify({receiver_id: receiverId})
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) showToast(data.message || 'Could not start call.');
        });
    }
}

/* ── Remove Connection ───────────────────── */
function removeConnection(id) {
    if (!confirm('Remove this connection?')) return;
    fetch(`/panel/connections/${id}/remove`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken},
        body: JSON.stringify({})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { showToast('Connection removed.'); location.reload(); }
    });
}

/* ── Toast ───────────────────────────────── */
function showToast(msg) {
    const t = document.getElementById('connToast');
    document.getElementById('connToastMsg').textContent = msg;
    t.style.display = 'block';
    setTimeout(() => t.style.display = 'none', 3000);
}
</script>
<style>
.connection-card { transition: transform .15s, box-shadow .15s; }
.connection-card:hover { transform: translateY(-2px); box-shadow: 0 4px 20px rgba(0,0,0,.08) !important; }
</style>
@endpush
