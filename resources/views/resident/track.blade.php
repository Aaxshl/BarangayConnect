@extends('layouts.portal')
@section('title','Track Requests')
@section('content')
<div class="container-fluid px-3 px-md-4 mt-4">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h2 class="section-title mb-1">Track Requests &amp; Reports</h2>
            <p class="text-muted small mb-0">Monitor the live progress and status of your submitted documents and issue reports.</p>
        </div>
        <div class="input-group input-group-sm" style="max-width:260px">
            <span class="input-group-text bg-white"><i class="ti ti-search text-muted"></i></span>
            <input type="text" id="track-search" class="form-control" placeholder="Search tracking #...">
        </div>
    </div>

    {{-- Tabs Navigation --}}
    <ul class="nav nav-pills mb-4 gap-2 border-bottom pb-3" id="trackTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold px-3 py-2 d-flex align-items-center gap-2" id="docs-tab" data-bs-toggle="pill" data-bs-target="#docs-panel" type="button" role="tab" style="border-radius:10px;font-size:13.5px">
                <i class="ti ti-file-certificate"></i>
                <span>Document Requests</span>
                <span class="badge bg-primary text-white rounded-pill ms-1">{{ $totalDocs }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold px-3 py-2 d-flex align-items-center gap-2" id="reports-tab" data-bs-toggle="pill" data-bs-target="#reports-panel" type="button" role="tab" style="border-radius:10px;font-size:13.5px">
                <i class="ti ti-alert-triangle"></i>
                <span>Issue Reports / Blotter</span>
                <span class="badge bg-secondary text-white rounded-pill ms-1">{{ $totalReports }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="trackTabsContent">
        {{-- ════════════════════ TAB 1: DOCUMENTS ════════════════════ --}}
        <div class="tab-pane fade show active" id="docs-panel" role="tabpanel" aria-labelledby="docs-tab">
            @php
                $docSteps = [
                    ['key' => 'pending',          'label' => 'Requested',       'icon' => 'ti-file-plus'],
                    ['key' => 'under_review',     'label' => 'Under Review',    'icon' => 'ti-eye-check'],
                    ['key' => 'processing',       'label' => 'Processing',      'icon' => 'ti-file-settings'],
                    ['key' => 'ready_for_pickup', 'label' => 'Ready for Pickup','icon' => 'ti-package'],
                    ['key' => 'released',         'label' => 'Released',        'icon' => 'ti-circle-check'],
                ];
                $docOrder = array_column($docSteps, 'key');
            @endphp

            {{-- Active Document Requests --}}
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-bold text-uppercase small text-muted" style="letter-spacing:0.5px">
                        <i class="ti ti-clock me-1 text-primary"></i>Active Document Requests ({{ $activeDocuments->count() }})
                    </span>
                    <a href="{{ route('portal.request') }}" class="btn btn-navy btn-sm" style="font-size:12px">
                        <i class="ti ti-plus me-1"></i>New Request
                    </a>
                </div>

                @forelse($activeDocuments as $doc)
                @php
                    $docIdx = array_search($doc->status, $docOrder);
                    if ($docIdx === false) $docIdx = -1;
                @endphp
                <div class="portal-card mb-3 track-item" data-id="{{ strtolower($doc->document_number) }}" 
                     style="cursor:pointer;transition:transform .2s,box-shadow .2s;border:1px solid #e2e8f0"
                     data-bs-toggle="modal" data-bs-target="#docModal-{{ $doc->id }}">
                    <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
                        <div>
                            <div class="request-id fw-bold text-primary" style="font-family:monospace;font-size:14px">{{ $doc->document_number }}</div>
                            <h5 class="request-title mt-1 mb-1" style="font-size:16px">{{ \App\Models\Document::TYPES[$doc->document_type] ?? $doc->document_type }}</h5>
                            <div class="request-meta text-muted small">
                                <span><i class="ti ti-calendar me-1"></i>Requested {{ $doc->issue_date->format('M d, Y') }}</span>
                                <span class="mx-2">•</span>
                                <span><i class="ti ti-copy me-1"></i>{{ $doc->number_of_copies }} {{ Str::plural('copy', $doc->number_of_copies) }}</span>
                                <span class="mx-2">•</span>
                                <span><i class="ti ti-target me-1"></i>{{ Str::limit($doc->purpose, 40) }}</span>
                            </div>
                        </div>
                        <span class="badge-status badge-{{ $doc->status }}">{{ ucwords(str_replace('_',' ',$doc->status)) }}</span>
                    </div>

                    {{-- Step Tracker --}}
                    <div class="step-tracker">
                        @foreach($docSteps as $i => $ds)
                            @php
                                if ($i < $docIdx) {
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

                    @if($doc->status === 'ready_for_pickup')
                    <div class="info-banner mt-3" style="background:#fefce8;border-color:#fef08a;color:#854d0e">
                        <i class="ti ti-building me-1"></i>
                        <strong>Your document is ready!</strong> Please bring a valid ID to the Barangay Hall to claim it.
                    </div>
                    @endif

                    <div class="d-flex justify-content-end align-items-center mt-3 pt-2 border-top">
                        <span class="text-primary small fw-semibold">View Full Details &amp; History <i class="ti ti-arrow-right ms-1"></i></span>
                    </div>
                </div>
                @empty
                <div class="portal-card text-center py-4 text-muted mb-4">
                    <i class="ti ti-file-check" style="font-size:36px;opacity:0.4;display:block;margin-bottom:8px"></i>
                    No active document requests at this moment.
                </div>
                @endforelse
            </div>

            {{-- Collapsible Section for Completed & Cancelled Documents --}}
            @if($completedDocuments->count() > 0)
            <div class="mt-4 pt-2">
                <button class="btn btn-outline-secondary w-100 py-2.5 d-flex justify-content-between align-items-center" 
                        type="button" data-bs-toggle="collapse" data-bs-target="#completedDocsCollapse" 
                        aria-expanded="false" style="border-radius:12px;font-size:13.5px;font-weight:600">
                    <span>
                        <i class="ti ti-archive me-1"></i> Completed &amp; Cancelled Documents ({{ $completedDocuments->count() }})
                    </span>
                    <i class="ti ti-chevron-down"></i>
                </button>

                <div class="collapse mt-3" id="completedDocsCollapse">
                    @foreach($completedDocuments as $doc)
                    <div class="portal-card mb-3 track-item" data-id="{{ strtolower($doc->document_number) }}" 
                         style="cursor:pointer;opacity:0.85;border:1px solid #e2e8f0;background:#f8fafc"
                         data-bs-toggle="modal" data-bs-target="#docModal-{{ $doc->id }}">
                        <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-2">
                            <div>
                                <div class="request-id text-muted" style="font-family:monospace;font-size:13.5px">{{ $doc->document_number }}</div>
                                <h6 class="request-title mt-1 mb-1">{{ \App\Models\Document::TYPES[$doc->document_type] ?? $doc->document_type }}</h6>
                                <div class="text-muted small">
                                    Requested: {{ $doc->issue_date->format('M d, Y') }} · Purpose: {{ Str::limit($doc->purpose, 45) }}
                                </div>
                            </div>
                            <span class="badge-status badge-{{ $doc->status }}">
                                {{ $doc->status === 'cancelled' ? 'Rejected / Cancelled' : ucwords(str_replace('_',' ',$doc->status)) }}
                            </span>
                        </div>

                        @if($doc->status === 'released')
                            <div class="text-success small pt-1"><i class="ti ti-circle-check me-1"></i>Released on {{ $doc->released_at ? $doc->released_at->format('F d, Y · g:i A') : $doc->updated_at->format('F d, Y') }}</div>
                        @elseif($doc->status === 'cancelled')
                            <div class="text-danger small pt-1"><i class="ti ti-x me-1"></i>Rejection reason: {{ $doc->rejection_reason ?: 'Cancelled by administrator' }}</div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- ════════════════════ TAB 2: ISSUE REPORTS ════════════════════ --}}
        <div class="tab-pane fade" id="reports-panel" role="tabpanel" aria-labelledby="reports-tab">
            @php
                $repSteps = [
                    ['key' => 'pending',      'label' => 'Submitted',   'icon' => 'ti-send'],
                    ['key' => 'under_review', 'label' => 'Under Review','icon' => 'ti-eye'],
                    ['key' => 'assigned',     'label' => 'Assigned',    'icon' => 'ti-user-check'],
                    ['key' => 'in_progress',  'label' => 'In Progress', 'icon' => 'ti-tool'],
                    ['key' => 'resolved',     'label' => 'Resolved',    'icon' => 'ti-circle-check'],
                ];
                $repOrder = array_column($repSteps, 'key');
            @endphp

            {{-- Active Issue Reports --}}
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-bold text-uppercase small text-muted" style="letter-spacing:0.5px">
                        <i class="ti ti-clock me-1 text-warning"></i>Active Reports &amp; Complaints ({{ $activeReports->count() }})
                    </span>
                    <a href="{{ route('portal.report') }}" class="btn btn-navy btn-sm" style="font-size:12px">
                        <i class="ti ti-plus me-1"></i>Report Issue
                    </a>
                </div>

                @forelse($activeReports as $req)
                @php
                    $repIdx = array_search($req->status, $repOrder);
                    if ($repIdx === false) $repIdx = -1;
                @endphp
                <div class="portal-card mb-3 track-item" data-id="{{ strtolower($req->tracking_number) }}" 
                     style="cursor:pointer;transition:transform .2s,box-shadow .2s;border:1px solid #e2e8f0"
                     data-bs-toggle="modal" data-bs-target="#repModal-{{ $req->id }}">
                    <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
                        <div>
                            <div class="request-id fw-bold text-primary" style="font-family:monospace;font-size:14px">{{ $req->tracking_number }}</div>
                            <h5 class="request-title mt-1 mb-1" style="font-size:16px">{{ ucwords(str_replace('_',' ',$req->request_type)) }}</h5>
                            <div class="request-meta text-muted small">
                                <span><i class="ti ti-map-pin me-1"></i>{{ $req->location }}</span>
                                <span class="mx-2">•</span>
                                <span><i class="ti ti-calendar me-1"></i>Submitted {{ $req->created_at->format('M d, Y · g:i A') }}</span>
                            </div>
                        </div>
                        <span class="badge-status badge-{{ $req->status }}">{{ ucwords(str_replace('_',' ',$req->status)) }}</span>
                    </div>

                    {{-- Step Tracker --}}
                    <div class="step-tracker">
                        @foreach($repSteps as $i => $rs)
                            @php
                                if ($i < $repIdx) {
                                    $state = 'done';
                                } elseif ($i === $repIdx) {
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
                                <div class="step-label">{{ $rs['label'] }}</div>
                            </div>
                        @endforeach
                    </div>

                    @if($req->assignedTo)
                    <div class="mt-3 pt-2 border-top small text-muted">
                        <i class="ti ti-user-check me-1 text-primary"></i>Assigned to: <strong>{{ $req->assignedTo->name }}</strong>
                    </div>
                    @endif

                    <div class="d-flex justify-content-end align-items-center mt-3 pt-2 border-top">
                        <span class="text-primary small fw-semibold">View Full Details &amp; History <i class="ti ti-arrow-right ms-1"></i></span>
                    </div>
                </div>
                @empty
                <div class="portal-card text-center py-4 text-muted mb-4">
                    <i class="ti ti-message-report" style="font-size:36px;opacity:0.4;display:block;margin-bottom:8px"></i>
                    No active issue reports.
                </div>
                @endforelse
            </div>

            {{-- Collapsible Section for Resolved & Closed Reports --}}
            @if($completedReports->count() > 0)
            <div class="mt-4 pt-2">
                <button class="btn btn-outline-secondary w-100 py-2.5 d-flex justify-content-between align-items-center" 
                        type="button" data-bs-toggle="collapse" data-bs-target="#completedReportsCollapse" 
                        aria-expanded="false" style="border-radius:12px;font-size:13.5px;font-weight:600">
                    <span>
                        <i class="ti ti-archive me-1"></i> Resolved &amp; Closed Reports ({{ $completedReports->count() }})
                    </span>
                    <i class="ti ti-chevron-down"></i>
                </button>

                <div class="collapse mt-3" id="completedReportsCollapse">
                    @foreach($completedReports as $req)
                    <div class="portal-card mb-3 track-item" data-id="{{ strtolower($req->tracking_number) }}" 
                         style="cursor:pointer;opacity:0.85;border:1px solid #e2e8f0;background:#f8fafc"
                         data-bs-toggle="modal" data-bs-target="#repModal-{{ $req->id }}">
                        <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-2">
                            <div>
                                <div class="request-id text-muted" style="font-family:monospace;font-size:13.5px">{{ $req->tracking_number }}</div>
                                <h6 class="request-title mt-1 mb-1">{{ ucwords(str_replace('_',' ',$req->request_type)) }}</h6>
                                <div class="text-muted small">
                                    Location: {{ $req->location }} · Date: {{ $req->created_at->format('M d, Y') }}
                                </div>
                            </div>
                            <span class="badge-status badge-{{ $req->status }}">{{ ucwords(str_replace('_',' ',$req->status)) }}</span>
                        </div>

                        @if($req->resolution_note)
                            <div class="text-success small pt-1"><i class="ti ti-check me-1"></i>Resolution: {{ $req->resolution_note }}</div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ════════════════════ MODALS: ALL DOCUMENTS ════════════════════ --}}
@foreach($activeDocuments->concat($completedDocuments) as $doc)
@php
    $docIdx = array_search($doc->status, $docOrder);
    if ($docIdx === false) $docIdx = -1;
    $isReleased = $doc->status === 'released';
@endphp
<div class="modal fade" id="docModal-{{ $doc->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 12px 35px rgba(0,0,0,0.18)">
            <div class="modal-header pb-2" style="padding:22px 28px 12px">
                <div>
                    <div class="text-primary fw-bold" style="font-family:monospace;font-size:14px">{{ $doc->document_number }}</div>
                    <h5 class="modal-title fw-bold text-dark mt-1">{{ \App\Models\Document::TYPES[$doc->document_type] ?? $doc->document_type }}</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body" style="padding:16px 28px 24px">
                {{-- Status Step Tracker in Modal --}}
                @if($doc->status !== 'cancelled')
                <div class="p-3 mb-4 rounded bg-light" style="border:1px solid #e2e8f0">
                    <div class="step-tracker mb-0">
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
                </div>
                @else
                <div class="alert alert-danger py-2 px-3 mb-3 small d-flex align-items-center gap-2">
                    <i class="ti ti-alert-triangle" style="font-size:18px"></i>
                    <div><strong>This request was rejected.</strong> {{ $doc->rejection_reason ?: 'Contact the barangay hall for details.' }}</div>
                </div>
                @endif

                {{-- Document Details Grid --}}
                <div class="row g-3" style="font-size:13.5px">
                    <div class="col-sm-6">
                        <span class="text-muted d-block small">Status</span>
                        <span class="badge-status badge-{{ $doc->status }} mt-1">{{ ucwords(str_replace('_',' ',$doc->status)) }}</span>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted d-block small">Purpose</span>
                        <div class="fw-medium text-dark mt-1">{{ $doc->purpose }}</div>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted d-block small">Date Requested</span>
                        <div class="fw-medium text-dark mt-1">{{ $doc->issue_date->format('F d, Y') }}</div>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted d-block small">Number of Copies</span>
                        <div class="fw-medium text-dark mt-1">{{ $doc->number_of_copies }}</div>
                    </div>
                    @if($doc->issuedBy)
                    <div class="col-sm-6">
                        <span class="text-muted d-block small">Processed By</span>
                        <div class="fw-medium text-dark mt-1">{{ $doc->issuedBy->name }}</div>
                    </div>
                    @endif
                    @if($doc->viewed_at)
                    <div class="col-sm-6">
                        <span class="text-muted d-block small">Reviewed On</span>
                        <div class="fw-medium text-dark mt-1">{{ $doc->viewed_at->format('M d, Y · g:i A') }}</div>
                    </div>
                    @endif
                    @if($doc->released_at)
                    <div class="col-sm-6">
                        <span class="text-muted d-block small">Released On</span>
                        <div class="fw-medium text-success mt-1">{{ $doc->released_at->format('M d, Y · g:i A') }}</div>
                    </div>
                    @endif
                    @if($doc->remarks)
                    <div class="col-12">
                        <span class="text-muted d-block small">Remarks / Notes</span>
                        <div class="p-2 rounded bg-light mt-1 text-dark">{{ $doc->remarks }}</div>
                    </div>
                    @endif
                </div>

                @if($doc->status === 'ready_for_pickup')
                <div class="info-banner mt-4" style="background:#fefce8;border-color:#fef08a;color:#854d0e">
                    <i class="ti ti-building me-1"></i>
                    <strong>Claiming Instructions:</strong> Please visit Barangay Hall with 1 valid ID during office hours (Mon–Fri, 8 AM – 5 PM) to claim your document.
                </div>
                @endif
            </div>

            <div class="modal-footer bg-light" style="padding:12px 28px">
                <button type="button" class="btn btn-secondary btn-sm px-4 fw-semibold" data-bs-dismiss="modal" style="border-radius:8px">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

{{-- ════════════════════ MODALS: ALL REPORTS ════════════════════ --}}
@foreach($activeReports->concat($completedReports) as $req)
@php
    $repIdx = array_search($req->status, $repOrder);
    if ($repIdx === false) $repIdx = -1;
    $isResolved = in_array($req->status, ['resolved','closed']);
@endphp
<div class="modal fade" id="repModal-{{ $req->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 12px 35px rgba(0,0,0,0.18)">
            <div class="modal-header pb-2" style="padding:22px 28px 12px">
                <div>
                    <div class="text-primary fw-bold" style="font-family:monospace;font-size:14px">{{ $req->tracking_number }}</div>
                    <h5 class="modal-title fw-bold text-dark mt-1">{{ ucwords(str_replace('_',' ',$req->request_type)) }}</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body" style="padding:16px 28px 24px">
                {{-- Status Step Tracker in Modal --}}
                @if(!in_array($req->status, ['rejected','cancelled']))
                <div class="p-3 mb-4 rounded bg-light" style="border:1px solid #e2e8f0">
                    <div class="step-tracker mb-0">
                        @foreach($repSteps as $i => $rs)
                            @php
                                if ($isResolved) {
                                    $state = 'done';
                                } elseif ($i < $repIdx) {
                                    $state = 'done';
                                } elseif ($i === $repIdx) {
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
                                <div class="step-label">{{ $rs['label'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="alert alert-danger py-2 px-3 mb-3 small">
                    <i class="ti ti-alert-circle me-1"></i><strong>This report has been rejected or closed.</strong>
                </div>
                @endif

                {{-- Photo Attachment if any --}}
                @if($req->photo)
                <div class="mb-4 text-center">
                    <div class="text-muted small mb-1 text-start">Photo Attachment:</div>
                    <img src="{{ asset('storage/'.$req->photo) }}" class="rounded img-fluid" style="max-height:260px;object-fit:cover;border:1px solid #e2e8f0">
                </div>
                @endif

                {{-- Report Details Grid --}}
                <div class="row g-3" style="font-size:13.5px">
                    <div class="col-sm-6">
                        <span class="text-muted d-block small">Status</span>
                        <span class="badge-status badge-{{ $req->status }} mt-1">{{ ucwords(str_replace('_',' ',$req->status)) }}</span>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted d-block small">Location</span>
                        <div class="fw-medium text-dark mt-1"><i class="ti ti-map-pin me-1 text-danger"></i>{{ $req->location }}</div>
                    </div>
                    <div class="col-12">
                        <span class="text-muted d-block small">Description of Concern</span>
                        <div class="p-2.5 rounded bg-light mt-1 text-dark" style="line-height:1.6">{{ $req->description }}</div>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted d-block small">Date Submitted</span>
                        <div class="fw-medium text-dark mt-1">{{ $req->created_at->format('F d, Y · g:i A') }}</div>
                    </div>
                    @if($req->assignedTo)
                    <div class="col-sm-6">
                        <span class="text-muted d-block small">Assigned Personnel</span>
                        <div class="fw-medium text-primary mt-1">{{ $req->assignedTo->name }} ({{ ucfirst($req->assignedTo->role) }})</div>
                    </div>
                    @endif
                    @if($req->resolved_at)
                    <div class="col-sm-6">
                        <span class="text-muted d-block small">Resolved Date</span>
                        <div class="fw-medium text-success mt-1">{{ $req->resolved_at->format('F d, Y · g:i A') }}</div>
                    </div>
                    @endif
                    @if($req->resolution_note)
                    <div class="col-12">
                        <span class="text-muted d-block small">Resolution Notes</span>
                        <div class="p-2.5 rounded mt-1 bg-success bg-opacity-10 text-success border border-success border-opacity-25" style="line-height:1.6">
                            <i class="ti ti-circle-check me-1"></i>{{ $req->resolution_note }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="modal-footer bg-light" style="padding:12px 28px">
                <button type="button" class="btn btn-secondary btn-sm px-4 fw-semibold" data-bs-dismiss="modal" style="border-radius:8px">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

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
