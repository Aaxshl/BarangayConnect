@extends('layouts.admin')
@section('title','Request Details')
@section('page-title','Citizen Report Details')
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
#request-map { height: 240px; border-radius: 10px; width: 100%; }
.photo-lightbox { cursor: zoom-in; transition: transform .2s; }
.photo-lightbox:hover { transform: scale(1.02); }
.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 700;
}
.status-pill-pending { background: #fee2e2; color: #991b1b; }
.status-pill-under_review { background: #fef3c7; color: #92400e; }
.status-pill-assigned { background: #e0e7ff; color: #3730a3; }
.status-pill-in_progress { background: #dbeafe; color: #1e40af; }
.status-pill-resolved { background: #dcfce7; color: #166534; }
.status-pill-closed { background: #f3f4f6; color: #4b5563; }
</style>
@endpush
@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <a href="{{ route('admin.citizen-requests.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back to Citizen Reports
    </a>
    <div class="d-flex align-items-center gap-2">
        {!! $citizenRequest->priority_badge !!}
        {!! $citizenRequest->aging_badge !!}
        <span class="status-pill status-pill-{{ $citizenRequest->status }}">
            <i class="ti ti-circle-filled" style="font-size:8px"></i>
            {{ ucwords(str_replace('_',' ',$citizenRequest->status)) }}
        </span>
    </div>
</div>

<div class="row g-3">
    <!-- Left Column: Report Details, Map, Photo -->
    <div class="col-12 col-lg-8">
        <div class="card-custom mb-3">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                <div>
                    <div style="font-family:monospace;font-size:14px;font-weight:700;color:#185fa5">{{ $citizenRequest->tracking_number }}</div>
                    <h4 class="fw-bold mb-1 mt-1">{{ ucwords(str_replace('_',' ',$citizenRequest->request_type)) }}</h4>
                    <div class="text-muted small">
                        <i class="ti ti-calendar me-1"></i>Submitted on {{ $citizenRequest->created_at->format('M d, Y \a\t g:i A') }}
                    </div>
                </div>
            </div>

            <!-- Progressive Step Tracker -->
            <div class="step-tracker mb-4">
                @php 
                    $workflow = ['pending','under_review','assigned','in_progress','resolved'];
                    $currentIdx = array_search($citizenRequest->status, $workflow);
                @endphp
                @foreach($workflow as $idx => $s)
                    @php
                        $state = '';
                        if ($citizenRequest->status === 'resolved') {
                            $state = 'done';
                        } elseif ($currentIdx !== false) {
                            if ($idx < $currentIdx) {
                                $state = 'done';
                            } elseif ($idx === $currentIdx) {
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
                                <span style="font-size:9px">{{ $idx + 1 }}</span>
                            @endif
                        </div>
                        <div class="step-label">{{ ucwords(str_replace('_',' ',$s)) }}</div>
                    </div>
                @endforeach
            </div>

            <h6 class="fw-semibold mb-2">Issue Description</h6>
            <div class="p-3 bg-light rounded mb-3" style="font-size:14px;line-height:1.6;color:#333">
                {{ $citizenRequest->description }}
            </div>

            {{-- Location Section --}}
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                    <h6 class="fw-semibold mb-0"><i class="ti ti-map-pin me-1 text-danger"></i>Report Location</h6>
                    @if($citizenRequest->latitude && $citizenRequest->longitude)
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $citizenRequest->latitude }},{{ $citizenRequest->longitude }}"
                           target="_blank" class="btn btn-sm btn-outline-danger py-1 px-2">
                            <i class="ti ti-brand-google-maps me-1"></i>Get Google Maps Directions
                        </a>
                    @endif
                </div>
                <div class="fw-medium mb-2" style="font-size:13.5px">
                    <i class="ti ti-building me-1 text-muted"></i>{{ $citizenRequest->location }}
                </div>
                @if($citizenRequest->latitude && $citizenRequest->longitude)
                    <div id="request-map" class="border"></div>
                    <div class="text-muted small mt-1">
                        GPS Coordinates: {{ number_format($citizenRequest->latitude, 6) }}, {{ number_format($citizenRequest->longitude, 6) }}
                    </div>
                @else
                    <div class="alert alert-secondary py-2 mb-0 small">No GPS coordinates recorded for this location.</div>
                @endif
            </div>

            {{-- Submitted Photo --}}
            @if($citizenRequest->photo)
            <div class="mb-3">
                <h6 class="fw-semibold mb-2"><i class="ti ti-photo me-1"></i>Attached Citizen Photo</h6>
                <a href="{{ asset('storage/'.$citizenRequest->photo) }}" target="_blank">
                    <img src="{{ asset('storage/'.$citizenRequest->photo) }}"
                         class="photo-lightbox rounded border"
                         style="max-width:100%;max-height:340px;object-fit:cover"
                         alt="Citizen Report Photo">
                </a>
                <div class="text-muted small mt-1">Click photo to view full resolution</div>
            </div>
            @endif

            {{-- Submitter & Details --}}
            <div class="row g-2 pt-3 border-top" style="font-size:13.5px">
                <div class="col-md-6">
                    <span class="text-muted d-block">Submitted by</span>
                    <div class="fw-semibold">
                        @if($citizenRequest->resident)
                            <a href="{{ route('admin.residents.show', $citizenRequest->resident) }}" class="text-navy text-decoration-none">
                                {{ $citizenRequest->resident->full_name }}
                            </a>
                        @else
                            Anonymous Citizen
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <span class="text-muted d-block">Assigned Officer / Staff</span>
                    <div class="fw-semibold">{{ optional($citizenRequest->assignedTo)->name ?? 'None / Unassigned' }}</div>
                </div>
                @if($citizenRequest->resolved_at)
                <div class="col-md-6">
                    <span class="text-muted d-block">Resolved Date</span>
                    <div class="text-success fw-semibold">{{ $citizenRequest->resolved_at->format('M d, Y g:i A') }}</div>
                </div>
                @endif
                @if($citizenRequest->resolution_note)
                <div class="col-12 mt-2">
                    <span class="text-muted d-block">Official Resolution Note</span>
                    <div class="p-3 bg-success-subtle text-success-emphasis border border-success-subtle rounded mt-1">
                        <i class="ti ti-check-circle me-1"></i>{{ $citizenRequest->resolution_note }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Interactive Discussion & Activity Thread (Restricted to Assigned Personnel & Reporting Resident) --}}
        @if($citizenRequest->canAccessConversation(auth()->user()))
        <div class="card-custom mb-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">
                    <i class="ti ti-messages me-2 text-primary"></i>Case Communication &amp; Updates
                    <span class="badge bg-secondary ms-1">{{ $citizenRequest->comments->count() }}</span>
                </h6>
                <span class="small text-muted">Direct messaging with resident &amp; internal logs</span>
            </div>

            {{-- Timeline / Comments List --}}
            <div class="comments-thread mb-3" style="max-height: 480px; overflow-y: auto; padding-right: 4px;">
                @forelse($citizenRequest->comments as $comment)
                    <div class="p-3 mb-2 rounded border {{ $comment->is_internal ? 'border-warning bg-warning bg-opacity-10' : ($comment->sender_type === 'resident' ? 'border-info bg-info bg-opacity-10' : 'border-light bg-light') }}">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <strong class="small" style="color: #1e293b;">
                                    @if($comment->sender_type === 'resident')
                                        <i class="ti ti-user text-info me-1"></i>{{ optional($comment->resident)->full_name ?? 'Resident' }}
                                    @else
                                        <i class="ti ti-shield text-primary me-1"></i>{{ optional($comment->user)->name ?? 'Barangay Official' }}
                                    @endif
                                </strong>
                                @if($comment->sender_type === 'resident')
                                    <span class="badge bg-info text-dark" style="font-size: 10px;">Resident</span>
                                @else
                                    <span class="badge bg-primary" style="font-size: 10px;">Staff</span>
                                @endif
                                @if($comment->is_internal)
                                    <span class="badge bg-warning text-dark" style="font-size: 10px;">
                                        <i class="ti ti-lock me-1"></i>Internal Note (Hidden from Resident)
                                    </span>
                                @endif
                            </div>
                            <span class="text-muted" style="font-size: 11px;" title="{{ $comment->created_at->format('M d, Y g:i A') }}">
                                <i class="ti ti-clock me-1"></i>{{ $comment->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <div style="font-size: 13.5px; line-height: 1.5; color: #334155; white-space: pre-wrap;">{{ $comment->message }}</div>
                        @if($comment->attachment)
                            <div class="mt-2 pt-2 border-top border-secondary border-opacity-25 small">
                                <a href="{{ asset('storage/'.$comment->attachment) }}" target="_blank" class="text-decoration-none fw-semibold">
                                    <i class="ti ti-paperclip me-1"></i>View Attachment
                                </a>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-4 text-muted border rounded bg-light">
                        <i class="ti ti-message-off mb-2 d-block" style="font-size: 28px; opacity: 0.5;"></i>
                        No communication or notes posted yet. Submit an update below.
                    </div>
                @endforelse
            </div>

            {{-- New Comment Form --}}
            <form method="POST" action="{{ route('admin.citizen-requests.comment', $citizenRequest) }}" enctype="multipart/form-data" class="pt-2 border-top">
                @csrf
                <div class="mb-2">
                    <label class="form-label fw-semibold small mb-1">Post an Update / Message</label>
                    <textarea name="message" class="form-control form-control-sm" rows="3" placeholder="Type your response to the resident, or record an internal investigation note..." required></textarea>
                </div>
                <div class="row g-2 align-items-center">
                    <div class="col-md-6">
                        <input type="file" name="attachment" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                        <div class="form-text" style="font-size: 11px;">Attach photo or document (max 5MB, optional)</div>
                    </div>
                    <div class="col-md-6 d-flex align-items-center justify-content-between justify-content-md-end gap-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_internal" value="1" id="internalNoteCheck">
                            <label class="form-check-label small" for="internalNoteCheck">
                                <i class="ti ti-lock me-1"></i>Internal note
                            </label>
                        </div>
                        <button type="submit" class="btn btn-navy btn-sm">
                            <i class="ti ti-send me-1"></i>Post
                        </button>
                    </div>
                </div>
            </form>
        </div>
        @else
        {{-- Restricted Notice for Staff Not Assigned to This Case --}}
        <div class="card-custom mb-3 border-secondary border-opacity-25">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold mb-0 text-muted">
                    <i class="ti ti-lock me-2 text-warning"></i>Case Communication (Confidential)
                </h6>
                <span class="badge bg-secondary-subtle text-secondary border">Restricted</span>
            </div>
            <div class="p-4 bg-light rounded border text-center">
                <i class="ti ti-shield-lock text-muted mb-2 d-block" style="font-size:32px;"></i>
                <div class="fw-semibold text-dark mb-1" style="font-size:14px;">Conversation is Private to This Case</div>
                <p class="text-muted small mb-0" style="max-width:480px;margin:0 auto;line-height:1.5;">
                    @if($citizenRequest->assigned_to)
                        This case conversation is confidential between the reporting resident and the assigned personnel (<strong>{{ optional($citizenRequest->assignedTo)->name }}</strong>). Personnel assigned to other cases or unassigned staff cannot view or send messages.
                    @else
                        This case conversation is confidential between the reporting resident and the assigned personnel. No officer is currently assigned to this case. Once an officer is assigned, case communication will be unlocked for them.
                    @endif
                </p>
            </div>
        </div>
        @endif
    </div>

    <!-- Right Column: Progressive Workflow Actions & Assignment -->
    <div class="col-12 col-lg-4">
        {{-- Progressive Workflow Action Card --}}
        <div class="card-custom mb-3 border-primary border-opacity-25">
            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="ti ti-progress me-1 text-primary"></i>Next Workflow Step
            </h6>

            @if($citizenRequest->status === 'under_review' || $citizenRequest->status === 'pending' || !$citizenRequest->assigned_to)
                <div class="alert alert-warning py-2 mb-3 small">
                    <i class="ti ti-user-exclamation me-1"></i><strong>Step 2: Assign Personnel Required.</strong> Please assign a designated officer/staff member below before starting the investigation.
                </div>
                <button type="button" class="btn btn-secondary w-100 py-2" disabled style="opacity: 0.65; cursor: not-allowed;">
                    <i class="ti ti-lock me-1"></i>Assign Personnel First to Start Investigation
                </button>

            @elseif($citizenRequest->status === 'assigned')
                <div class="alert alert-info py-2 mb-3 small">
                    <i class="ti ti-user-check me-1"></i>Assigned to <strong>{{ optional($citizenRequest->assignedTo)->name }}</strong>. Ready to begin work.
                </div>
                @if(auth()->user()->canDo('requests.status'))
                    @if(auth()->id() === (int)$citizenRequest->assigned_to)
                    <form method="POST" action="{{ route('admin.citizen-requests.status', $citizenRequest) }}" class="mb-2">
                        @csrf
                        <input type="hidden" name="status" value="in_progress">
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="ti ti-player-play me-1"></i>Start Investigation / In Progress
                        </button>
                    </form>
                    @else
                    <div class="alert alert-secondary py-2 mb-2 small text-center">
                        <i class="ti ti-lock me-1"></i>Only the assigned personnel (<strong>{{ optional($citizenRequest->assignedTo)->name }}</strong>) can start this investigation.
                    </div>
                    @endif
                @endif

            @elseif($citizenRequest->status === 'in_progress')
                <div class="alert alert-primary py-2 mb-3 small">
                    <i class="ti ti-tools me-1"></i><strong>Work is currently in progress.</strong> When completed, mark as resolved with a note.
                </div>
                @if(auth()->user()->canDo('requests.status'))
                <button type="button" class="btn btn-success w-100 py-2 mb-2" data-bs-toggle="modal" data-bs-target="#resolveModal">
                    <i class="ti ti-circle-check me-1"></i>Mark as Resolved
                </button>
                @endif

            @elseif($citizenRequest->status === 'resolved')
                <div class="alert alert-success py-2 mb-3 small">
                    <i class="ti ti-check-circle me-1"></i><strong>Case Resolved!</strong>
                </div>
                @if(auth()->user()->canDo('requests.status'))
                <form method="POST" action="{{ route('admin.citizen-requests.status', $citizenRequest) }}" class="mb-2">
                    @csrf
                    <input type="hidden" name="status" value="in_progress">
                    <button type="submit" class="btn btn-outline-warning w-100 py-2 btn-sm">
                        <i class="ti ti-rotate-2 me-1"></i>Reopen Request (In Progress)
                    </button>
                </form>
                @endif

            @elseif($citizenRequest->status === 'closed')
                <div class="alert alert-secondary py-2 mb-3 small">
                    <i class="ti ti-lock me-1"></i>This request is closed.
                </div>
                @if(auth()->user()->canDo('requests.status'))
                <form method="POST" action="{{ route('admin.citizen-requests.status', $citizenRequest) }}">
                    @csrf
                    <input type="hidden" name="status" value="under_review">
                    <button type="submit" class="btn btn-outline-secondary w-100 py-2 btn-sm">
                        <i class="ti ti-rotate-2 me-1"></i>Reopen for Review
                    </button>
                </form>
                @endif
            @endif
        </div>

        {{-- Staff Assignment Card --}}
        @if(auth()->user()->canDo('requests.assign'))
        <div class="card-custom mb-3">
            <h6 class="fw-bold mb-3"><i class="ti ti-user-plus me-1"></i>Assign Staff</h6>
            <form method="POST" action="{{ route('admin.citizen-requests.assign', $citizenRequest) }}">
                @csrf
                <div class="mb-2">
                    <select name="assigned_to" class="form-select form-select-sm" required>
                        <option value="">Select staff to assign...</option>
                        @foreach($staff as $u)
                            <option value="{{ $u->id }}" {{ $citizenRequest->assigned_to == $u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ ucfirst($u->role) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-outline-navy btn-sm w-100">
                    <i class="ti ti-check me-1"></i>{{ $citizenRequest->assigned_to ? 'Reassign Staff' : 'Assign Staff' }}
                </button>
            </form>
        </div>
        @endif

        {{-- More Actions Card --}}
        @if(auth()->user()->canDo('requests.convert') || auth()->user()->canDo('requests.delete'))
        <div class="card-custom">
            <h6 class="fw-semibold mb-2">More Actions</h6>
            @if(auth()->user()->canDo('requests.convert'))
            <form method="POST" action="{{ route('admin.citizen-requests.convert', $citizenRequest) }}">
                @csrf
                <button type="submit" class="btn btn-outline-navy btn-sm w-100 mb-2">
                    <i class="ti ti-list-check me-1"></i>Convert to Blotter / Service Log
                </button>
            </form>
            @endif
            @if($citizenRequest->status !== 'closed' && auth()->user()->canDo('requests.delete'))
            <form method="POST" action="{{ route('admin.citizen-requests.destroy', $citizenRequest) }}" onsubmit="return confirm('Close this citizen report?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-outline-secondary btn-sm w-100">
                    <i class="ti ti-x me-1"></i>Close Request
                </button>
            </form>
            @endif
        </div>
        @endif
    </div>
</div>

<!-- Modal: Mark as Resolved with Note -->
<div class="modal fade" id="resolveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold"><i class="ti ti-circle-check text-success me-1"></i>Mark Case as Resolved</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.citizen-requests.status', $citizenRequest) }}">
                @csrf
                <input type="hidden" name="status" value="resolved">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Resolution Summary / Actions Taken *</label>
                        <textarea name="resolution_note" class="form-control" rows="4" placeholder="Explain the actions taken to resolve the issue (e.g. Streetlight repaired by barangay electricians; Garbage cleared by sanitary team)." required>{{ old('resolution_note', $citizenRequest->resolution_note) }}</textarea>
                        <div class="form-text">This resolution note will be visible to the resident when tracking their report.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="ti ti-check me-1"></i>Confirm Resolution
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
@if($citizenRequest->latitude && $citizenRequest->longitude)
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const lat = {{ $citizenRequest->latitude }};
    const lng = {{ $citizenRequest->longitude }};
    const map = L.map('request-map').setView([lat, lng], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    const marker = L.marker([lat, lng]).addTo(map);
    marker.bindPopup('<b>{{ addslashes($citizenRequest->location) }}</b><br>{{ addslashes(ucwords(str_replace("_"," ",$citizenRequest->request_type))) }}').openPopup();
});
</script>
@endif
@endpush
@endsection
