@extends('layouts.admin')
@section('title',"Citizen's Requests/Reports")
@section('page-title',"Citizen's Requests & Incident Reports")
@section('content')

<!-- Instant Auto-Filter Bar (No Filter Button Needed) -->
<div class="card-custom mb-4 p-3">
    <form method="GET" id="autoFilterForm" action="{{ route('admin.citizen-requests.index') }}" class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2 flex-wrap flex-grow-1">
            <!-- Search -->
            <div class="position-relative" style="min-width: 220px;">
                <input type="text" name="search" id="searchInput" class="form-control form-control-sm ps-4" 
                       placeholder="Search tracking, location, issue..." 
                       value="{{ request('search') }}">
                <i class="ti ti-search position-absolute top-50 translate-middle-y ms-2 text-muted" style="font-size:14px"></i>
            </div>

            <!-- Status Filter -->
            <div>
                <select name="status" class="form-select form-select-sm" onchange="document.getElementById('autoFilterForm').submit()" style="min-width:160px">
                    <option value="">All Statuses (Active First)</option>
                    @foreach(\App\Models\CitizenRequest::STATUSES as $s)
                        <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>
                            {{ ucwords(str_replace('_',' ',$s)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Type Filter -->
            <div>
                <select name="type" class="form-select form-select-sm" onchange="document.getElementById('autoFilterForm').submit()" style="min-width:180px">
                    <option value="">All Issue Types</option>
                    @foreach(\App\Models\CitizenRequest::TYPES as $t)
                        <option value="{{ $t }}" {{ request('type') == $t ? 'selected' : '' }}>
                            {{ ucwords(str_replace('_',' ',$t)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if(request('search') || request('status') || request('type'))
                <a href="{{ route('admin.citizen-requests.index') }}" class="btn btn-outline-secondary btn-sm" title="Clear all filters">
                    <i class="ti ti-rotate-2 me-1"></i>Reset
                </a>
            @endif
        </div>

        <div class="text-muted small">
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle me-1">{{ $activeCount }} Active</span>
            <span class="badge bg-success-subtle text-success border border-success-subtle">{{ $resolvedCount }} Resolved</span>
        </div>
    </form>
</div>

<!-- Active / Unresolved Requests Section -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="fw-bold mb-0">
        @if(request('status'))
            Showing {{ ucwords(str_replace('_',' ',request('status'))) }} Reports ({{ $requests->total() }})
        @else
            <i class="ti ti-alert-circle text-warning me-1"></i>Active & Pending Reports ({{ $requests->total() }})
        @endif
    </h6>
</div>

@forelse($requests as $req)
<div class="card-custom mb-3 {{ $req->status === 'pending' ? 'border-danger border-opacity-50' : '' }}">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
        <div>
            <span style="font-size:12px;font-weight:700;color:#185fa5;font-family:monospace">{{ $req->tracking_number }}</span>
            <h5 class="mb-0 mt-1 fw-bold">{{ ucwords(str_replace('_',' ',$req->request_type)) }}</h5>
            <small class="text-muted">
                <i class="ti ti-user me-1"></i>{{ optional($req->resident)->full_name ?? 'Anonymous' }} · 
                <i class="ti ti-calendar me-1 ms-2"></i>{{ $req->created_at->format('M d, Y g:i A') }}
            </small>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if($req->assignedTo)
                <span class="badge bg-light text-dark border"><i class="ti ti-user-check me-1"></i>{{ $req->assignedTo->name }}</span>
            @endif
            <span class="badge-status badge-{{ $req->status }}">{{ ucwords(str_replace('_',' ',$req->status)) }}</span>
        </div>
    </div>
    
    <p class="mb-2" style="font-size:13.5px;color:#444;line-height:1.5">{{ Str::limit($req->description, 160) }}</p>
    
    <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap pt-2 border-top">
        <small class="text-muted"><i class="ti ti-map-pin text-danger"></i> {{ $req->location }}</small>
        <a href="{{ route('admin.citizen-requests.show', $req) }}" class="btn btn-navy btn-sm">
            <i class="ti ti-arrow-right me-1"></i>View & Manage
        </a>
    </div>

    <!-- Step Tracker -->
    <div class="step-tracker mt-3 pt-2 border-top">
        @php 
            $statuses = ['pending','under_review','assigned','in_progress','resolved'];
            $statusIdx = array_search($req->status, $statuses);
        @endphp
        @foreach($statuses as $currentIdx => $s)
            @php
                $state = '';
                if ($req->status === 'resolved') {
                    $state = 'done';
                } elseif ($statusIdx !== false) {
                    if ($currentIdx < $statusIdx) {
                        $state = 'done';
                    } elseif ($currentIdx === $statusIdx) {
                        $state = 'current';
                    }
                }
            @endphp
            <div class="step-item {{ $state }}">
                <div class="step-dot {{ $state }}">
                    @if($state == 'done')
                        <i class="ti ti-check" style="font-size:10px"></i>
                    @elseif($state == 'current')
                        <i class="ti ti-clock" style="font-size:10px"></i>
                    @else
                        <span style="font-size:9px">{{ $currentIdx + 1 }}</span>
                    @endif
                </div>
                <div class="step-label">{{ ucwords(str_replace('_',' ',$s)) }}</div>
            </div>
        @endforeach
    </div>
</div>
@empty
<div class="card-custom text-center py-5 mb-4">
    <i class="ti ti-inbox" style="font-size:40px;color:#aaa"></i>
    <div class="fw-semibold mt-2 text-muted">No reports found matching your criteria.</div>
</div>
@endforelse

<div class="mt-3 mb-4">{{ $requests->links() }}</div>

<!-- Collapsible Resolved Cases Section (Available on Default View) -->
@if($hasSplitView && $resolvedRequests->count() > 0)
<div class="mt-4">
    <div class="card-custom border-success border-opacity-50 p-0 overflow-hidden">
        <button class="btn w-100 p-3 text-start d-flex justify-content-between align-items-center bg-light" 
                type="button" 
                data-bs-toggle="collapse" 
                data-bs-target="#resolvedCasesCollapse" 
                aria-expanded="false" 
                style="border:none;box-shadow:none">
            <div class="d-flex align-items-center gap-2">
                <i class="ti ti-circle-check text-success" style="font-size:20px"></i>
                <span class="fw-bold" style="font-size:15px;color:#15803d">
                    Resolved & Closed Cases ({{ $resolvedRequests->count() }})
                </span>
            </div>
            <div class="d-flex align-items-center gap-2 text-muted small">
                <span>Click to View / Hide</span>
                <i class="ti ti-chevron-down"></i>
            </div>
        </button>

        <div class="collapse p-3" id="resolvedCasesCollapse">
            @foreach($resolvedRequests as $req)
            <div class="card-custom mb-3 bg-light bg-opacity-50 border">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                    <div>
                        <span style="font-size:12px;font-weight:700;color:#185fa5;font-family:monospace">{{ $req->tracking_number }}</span>
                        <h6 class="mb-0 mt-1 fw-bold">{{ ucwords(str_replace('_',' ',$req->request_type)) }}</h6>
                        <small class="text-muted">
                            Submitted {{ $req->created_at->format('M d, Y') }} · Resolved on {{ optional($req->resolved_at)->format('M d, Y') ?? 'N/A' }}
                        </small>
                    </div>
                    <span class="badge-status badge-{{ $req->status }}">{{ ucwords(str_replace('_',' ',$req->status)) }}</span>
                </div>
                
                @if($req->resolution_note)
                <div class="p-2 bg-white rounded border border-success-subtle text-success-emphasis small mb-2">
                    <strong><i class="ti ti-check me-1"></i>Resolution Note:</strong> {{ $req->resolution_note }}
                </div>
                @endif

                <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap pt-2 border-top">
                    <small class="text-muted"><i class="ti ti-map-pin text-danger"></i> {{ $req->location }}</small>
                    <a href="{{ route('admin.citizen-requests.show', $req) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="ti ti-eye me-1"></i>View Details
                    </a>
                </div>

                <!-- Complete Step Tracker for Resolved Item -->
                <div class="step-tracker mt-3 pt-2 border-top">
                    @php $statuses = ['pending','under_review','assigned','in_progress','resolved']; @endphp
                    @foreach($statuses as $currentIdx => $s)
                        <div class="step-item done">
                            <div class="step-dot done">
                                <i class="ti ti-check" style="font-size:10px"></i>
                            </div>
                            <div class="step-label">{{ ucwords(str_replace('_',' ',$s)) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
// Submit on Enter key for search input
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('autoFilterForm').submit();
    }
});
</script>
@endpush
@endsection
