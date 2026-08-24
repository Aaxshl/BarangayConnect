@extends('layouts.portal')
@section('title','Track Requests')
@section('content')
<div class="container-fluid px-3 px-md-4 mt-4">
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <h2 class="section-title mb-0">Your requests</h2>
        <div class="d-flex gap-2">
            <input type="text" id="track-search" class="form-control form-control-sm" placeholder="Tracking number..." style="width:180px">
        </div>
    </div>

    <h6 class="fw-semibold mb-2" style="font-size:13px;color:#888">Issue reports</h6>
    @forelse($myRequests as $req)
    <div class="portal-card mb-3">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
                <div class="request-id">{{ $req->tracking_number }}</div>
                <div class="request-title mt-1">{{ ucwords(str_replace('_',' ',$req->request_type)) }}</div>
                <div class="request-meta">{{ $req->location }} · {{ $req->created_at->format('M d, Y') }}</div>
            </div>
            <span class="badge-status badge-{{ $req->status }}">{{ ucwords(str_replace('_',' ',$req->status)) }}</span>
        </div>
        <div class="step-tracker mt-3">
            @php $statuses = ['pending','under_review','assigned','in_progress','resolved']; @endphp
            @foreach($statuses as $s)
                @php
                    $idx = array_search($req->status, $statuses);
                    $cur = array_search($s, $statuses);
                    $state = $cur < $idx ? 'done' : ($cur == $idx ? 'current' : '');
                @endphp
                <div class="step-item {{ $state }}">
                    <div class="step-dot {{ $state }}">
                        @if($state == 'done')<i class="ti ti-check" style="font-size:9px"></i>
                        @elseif($state == 'current')<i class="ti ti-clock" style="font-size:9px"></i>@endif
                    </div>
                    <div class="step-label">{{ ucwords(str_replace('_',' ',$s)) }}</div>
                </div>
            @endforeach
        </div>
        @if($req->resolution_note)
        <div class="mt-2 pt-2 border-top" style="font-size:12.5px;color:#555">
            <i class="ti ti-message me-1"></i>{{ $req->resolution_note }}
        </div>
        @endif
    </div>
    @empty
    <p class="text-muted small mb-4">No issue reports yet.</p>
    @endforelse

    <h6 class="fw-semibold mb-2 mt-2" style="font-size:13px;color:#888">Document requests</h6>
    @forelse($myDocuments as $doc)
    <div class="portal-card mb-3">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <div class="request-id">{{ $doc->document_number }}</div>
                <div class="request-title mt-1">{{ \App\Models\Document::TYPES[$doc->document_type] ?? $doc->document_type }}</div>
                <div class="request-meta">{{ $doc->issue_date->format('M d, Y') }}</div>
            </div>
            <span class="badge-status badge-{{ $doc->status }}">{{ ucwords(str_replace('_',' ',$doc->status)) }}</span>
        </div>
        @if($doc->status === 'pending_pickup')
        <div class="info-banner mt-2">
            <i class="ti ti-building"></i>
            Ready for pickup at the Barangay Hall. Office hours: Mon–Fri, 8 AM – 5 PM.
        </div>
        @endif
    </div>
    @empty
    <p class="text-muted small">No document requests yet.</p>
    @endforelse
</div>
@endsection
