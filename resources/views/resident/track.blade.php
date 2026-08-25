@extends('layouts.portal')
@section('title','Track Requests')
@section('content')
<div class="container-fluid px-3 px-md-4 mt-4">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <h2 class="section-title mb-0">My Requests</h2>
        <input type="text" id="track-search" class="form-control form-control-sm"
            placeholder="Search by tracking number..." style="width:200px">
    </div>

    {{-- ══ ISSUE REPORTS ══ --}}
    <h6 class="fw-bold mb-3" style="font-size:13px;color:#64748b;text-transform:uppercase;letter-spacing:0.5px">
        <i class="ti ti-alert-triangle me-1 text-warning"></i>Issue Reports / Complaints
    </h6>
    @forelse($myRequests as $req)
    <div class="portal-card mb-3 track-item" data-id="{{ strtolower($req->tracking_number) }}">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <div class="request-id">{{ $req->tracking_number }}</div>
                <div class="request-title mt-1">{{ ucwords(str_replace('_',' ',$req->request_type)) }}</div>
                <div class="request-meta">{{ $req->location }} · {{ $req->created_at->format('M d, Y') }}</div>
            </div>
            <span class="badge-status badge-{{ $req->status }}">{{ ucwords(str_replace('_',' ',$req->status)) }}</span>
        </div>
        {{-- Step Tracker --}}
        <div class="step-tracker">
            @php
                $reqStatuses = ['pending','under_review','assigned','in_progress','resolved'];
                $reqIdx = array_search($req->status, $reqStatuses);
                if ($reqIdx === false) $reqIdx = -1;
            @endphp
            @foreach($reqStatuses as $s)
                @php
                    $cur = array_search($s, $reqStatuses);
                    if ($req->status === 'resolved') {
                        $state = 'done';
                    } elseif ($cur < $reqIdx) {
                        $state = 'done';
                    } elseif ($cur === $reqIdx) {
                        $state = 'current';
                    } else {
                        $state = '';
                    }
                @endphp
                <div class="step-item {{ $state }}">
                    <div class="step-dot {{ $state }}">
                        @if($state === 'done') <i class="ti ti-check" style="font-size:9px"></i>
                        @elseif($state === 'current') <i class="ti ti-clock" style="font-size:9px"></i>
                        @endif
                    </div>
                    <div class="step-label">{{ ucwords(str_replace('_',' ',$s)) }}</div>
                </div>
            @endforeach
        </div>
        @if($req->resolution_note)
        <div class="mt-3 pt-2 border-top" style="font-size:12.5px;color:#555">
            <i class="ti ti-message me-1"></i>{{ $req->resolution_note }}
        </div>
        @endif
    </div>
    @empty
    <div class="portal-card text-center py-4 mb-4" style="color:#94a3b8">
        <i class="ti ti-message-report" style="font-size:32px;display:block;margin-bottom:6px"></i>
        No issue reports yet. <a href="{{ route('portal.report') }}" style="color:#185fa5">Report an issue →</a>
    </div>
    @endforelse

    {{-- ══ DOCUMENT REQUESTS ══ --}}
    <h6 class="fw-bold mb-3 mt-4" style="font-size:13px;color:#64748b;text-transform:uppercase;letter-spacing:0.5px">
        <i class="ti ti-file-certificate me-1 text-primary"></i>Document Requests
    </h6>
    @forelse($myDocuments as $doc)
    <div class="portal-card mb-3 track-item" data-id="{{ strtolower($doc->document_number) }}">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <div class="request-id">{{ $doc->document_number }}</div>
                <div class="request-title mt-1">{{ \App\Models\Document::TYPES[$doc->document_type] ?? $doc->document_type }}</div>
                <div class="request-meta">Requested {{ $doc->issue_date->format('M d, Y') }} · {{ $doc->number_of_copies }} copy/ies</div>
            </div>
            @if($doc->status === 'cancelled')
                <span class="badge-status badge-cancelled">Rejected</span>
            @else
                <span class="badge-status badge-{{ $doc->status }}">{{ ucwords(str_replace('_',' ',$doc->status)) }}</span>
            @endif
        </div>

        @if($doc->status !== 'cancelled')
        {{-- 5-step Document Progress Tracker --}}
        <div class="step-tracker">
            @php
                $docSteps = [
                    ['key' => 'pending',          'label' => 'Requested'],
                    ['key' => 'under_review',     'label' => 'Under Review'],
                    ['key' => 'processing',       'label' => 'Processing'],
                    ['key' => 'ready_for_pickup', 'label' => 'Ready for Pickup'],
                    ['key' => 'released',         'label' => 'Released'],
                ];
                $docIdx = array_search($doc->status, array_column($docSteps,'key'));
                if ($docIdx === false) $docIdx = -1;
                $isReleased = $doc->status === 'released';
            @endphp
            @foreach($docSteps as $i => $ds)
                @php
                    if ($isReleased) {
                        $state = 'done';
                    } elseif ($i < $docIdx) {
                        $state = 'done';
                    } elseif ($i === $docIdx) {
                        $state = 'current';
                    } else {
                        $state = '';
                    }
                @endphp
                <div class="step-item {{ $state }}">
                    <div class="step-dot {{ $state }}">
                        @if($state === 'done') <i class="ti ti-check" style="font-size:9px"></i>
                        @elseif($state === 'current') <i class="ti ti-clock" style="font-size:9px"></i>
                        @endif
                    </div>
                    <div class="step-label">{{ $ds['label'] }}</div>
                </div>
            @endforeach
        </div>
        @endif

        {{-- Status-specific Messages --}}
        @if($doc->status === 'ready_for_pickup')
        <div class="info-banner mt-3">
            <i class="ti ti-building me-1"></i>
            <strong>Your document is ready!</strong> Please bring a valid government ID to the Barangay Hall to claim it.
            Office hours: Mon–Fri, 8:00 AM – 5:00 PM.
        </div>
        @elseif($doc->status === 'released')
        <div class="mt-3 pt-2 border-top d-flex align-items-center gap-2" style="font-size:12.5px;color:#059669">
            <i class="ti ti-circle-check" style="font-size:16px"></i>
            Document officially released
            @if($doc->released_at) on {{ $doc->released_at->format('F d, Y') }} @endif.
        </div>
        @elseif($doc->status === 'cancelled')
        <div class="mt-3 pt-2 border-top" style="font-size:12.5px;color:#dc2626">
            <i class="ti ti-x me-1"></i>
            <strong>Request rejected.</strong>
            @if($doc->rejection_reason) Reason: {{ $doc->rejection_reason }} @endif
        </div>
        @endif

        @if($doc->remarks && !in_array($doc->status, ['cancelled']))
        <div class="mt-2 pt-2 border-top" style="font-size:12px;color:#555">
            <i class="ti ti-message me-1"></i>{{ $doc->remarks }}
        </div>
        @endif
    </div>
    @empty
    <div class="portal-card text-center py-4" style="color:#94a3b8">
        <i class="ti ti-file-off" style="font-size:32px;display:block;margin-bottom:6px"></i>
        No document requests yet. <a href="{{ route('portal.request') }}" style="color:#185fa5">Request a document →</a>
    </div>
    @endforelse
</div>

@push('scripts')
<script>
document.getElementById('track-search').addEventListener('input', function() {
    const q = this.value.toLowerCase().trim();
    document.querySelectorAll('.track-item').forEach(el => {
        el.style.display = q === '' || el.dataset.id.includes(q) ? '' : 'none';
    });
});
</script>
@endpush
@endsection
