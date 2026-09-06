@extends('layouts.portal')
@section('title','Track — {{ $tracking }}')
@section('content')
<div class="container-fluid px-3 px-md-4 mt-4" style="max-width:680px">
    <a href="{{ route('portal.track') }}" style="font-size:13px;color:#1a3a6b;text-decoration:none;display:flex;align-items:center;gap:5px;margin-bottom:16px"><i class="ti ti-arrow-left"></i> Back to all requests</a>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="portal-card">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
            <div class="request-id">{{ $tracking }}</div>
            @if($item instanceof \App\Models\CitizenRequest)
                <div class="d-flex align-items-center gap-1">
                    {!! $item->priority_badge !!}
                </div>
            @endif
        </div>
        @if($item instanceof \App\Models\CitizenRequest)
            <h5 class="fw-bold mb-1">{{ ucwords(str_replace('_',' ',$item->request_type)) }}</h5>
            <div class="text-muted small mb-2"><i class="ti ti-calendar me-1"></i>Submitted on {{ $item->created_at->format('M d, Y \a\t g:i A') }}</div>
            <p style="font-size:13.5px;color:#555;line-height:1.6">{{ $item->description }}</p>
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 text-muted" style="font-size:13px">
                <div><i class="ti ti-map-pin text-danger"></i> {{ $item->location }}</div>
                @if($item->assignedTo)
                    <div><i class="ti ti-user-check text-primary me-1"></i>Assigned: <strong>{{ $item->assignedTo->name }}</strong></div>
                @endif
            </div>
            <div class="step-tracker mt-4">
                @php 
                    $statuses = ['pending','under_review','assigned','in_progress','resolved'];
                    $idx = array_search($item->status, $statuses);
                @endphp
                @foreach($statuses as $cur => $s)
                    @php
                        $state = '';
                        if ($item->status === 'resolved') {
                            $state = 'done';
                        } elseif ($idx !== false) {
                            if ($cur < $idx) {
                                $state = 'done';
                            } elseif ($cur === $idx) {
                                $state = 'current';
                            }
                        }
                    @endphp
                    <div class="step-item {{ $state }}">
                        <div class="step-dot {{ $state }}">
                            @if($state == 'done')
                                <i class="ti ti-check" style="font-size:9px"></i>
                            @elseif($state == 'current')
                                <i class="ti ti-clock" style="font-size:9px"></i>
                            @else
                                <span style="font-size:9px">{{ $cur + 1 }}</span>
                            @endif
                        </div>
                        <div class="step-label">{{ ucwords(str_replace('_',' ',$s)) }}</div>
                    </div>
                @endforeach
            </div>
            @if($item->resolution_note)
                <div class="mt-3 p-3 bg-success-subtle text-success-emphasis border border-success-subtle rounded" style="font-size:13px">
                    <strong><i class="ti ti-check-circle me-1"></i>Resolution Note:</strong> {{ $item->resolution_note }}
                </div>
            @endif
        @else
            <h5>{{ \App\Models\Document::TYPES[$item->document_type] ?? $item->document_type }}</h5>
            <div style="font-size:13.5px">Purpose: {{ $item->purpose }}</div>
            <div class="mt-3"><span class="badge-status badge-{{ $item->status }}">{{ ucwords(str_replace('_',' ',$item->status)) }}</span></div>
            @if(in_array($item->status, ['ready_for_pickup', 'pending_pickup']))
            <div class="info-banner mt-3"><i class="ti ti-building"></i>Ready for pickup at the Barangay Hall. Mon–Fri, 8 AM – 5 PM. Bring a valid ID.</div>
            @endif
        @endif
    </div>

    @if($item instanceof \App\Models\CitizenRequest)
    {{-- Resident & Barangay Communication Thread (Confidential to this Case) --}}
    <div class="portal-card mt-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h6 class="fw-bold mb-0">
                <i class="ti ti-messages me-2" style="color:#185fa5"></i>Case Discussion &amp; Direct Messaging
            </h6>
            <span class="badge bg-light text-dark border">{{ $item->comments->count() }}</span>
        </div>
        <div class="text-muted small mb-3">
            @if($item->assignedTo)
                <i class="ti ti-shield-check text-success me-1"></i>Private conversation with assigned officer: <strong>{{ $item->assignedTo->name }}</strong>
            @else
                <i class="ti ti-clock me-1 text-warning"></i>Awaiting officer assignment. Messages sent here will be received by the designated officer assigned to this case.
            @endif
        </div>

        <div class="mb-3" style="max-height: 400px; overflow-y: auto;">
            @forelse($item->comments as $c)
                <div class="p-3 mb-2 rounded border {{ $c->sender_type === 'resident' ? 'bg-primary-subtle border-primary-subtle text-dark' : 'bg-light border-light-subtle' }}">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong style="font-size:12.5px;color:{{ $c->sender_type === 'resident' ? '#0c4a6e' : '#1e3a8a' }}">
                            @if($c->sender_type === 'resident')
                                <i class="ti ti-user me-1"></i>You (Resident)
                            @else
                                <i class="ti ti-shield text-primary me-1"></i>Barangay Office ({{ optional($c->user)->name ?? 'Staff' }})
                            @endif
                        </strong>
                        <span class="text-muted" style="font-size:11px;">
                            {{ $c->created_at->diffForHumans() }}
                        </span>
                    </div>
                    <div style="font-size:13px; line-height: 1.5; white-space: pre-wrap;">{{ $c->message }}</div>
                    @if($c->attachment)
                        <div class="mt-2 pt-2 border-top small">
                            <a href="{{ asset('storage/'.$c->attachment) }}" target="_blank" style="color:#185fa5;text-decoration:none;font-weight:600">
                                <i class="ti ti-paperclip me-1"></i>View Attachment
                            </a>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-3 text-muted" style="font-size:13px;">
                    <i class="ti ti-message-dots mb-1 d-block" style="font-size:24px;opacity:0.6"></i>
                    No messages yet. Send a follow-up or provide more details below.
                </div>
            @endforelse
        </div>

        {{-- Reply Form (if not closed) --}}
        @if(!in_array($item->status, ['closed', 'rejected', 'cancelled']))
        <form method="POST" action="{{ route('portal.track.comment', $item->tracking_number) }}" enctype="multipart/form-data" class="pt-3 border-top">
            @csrf
            <div class="mb-2">
                <label class="form-label" style="font-size:12.5px;font-weight:600">Reply / Send Follow-up</label>
                <textarea name="message" class="form-control form-control-sm" rows="3" placeholder="Add details, ask questions, or respond to the barangay staff..." required></textarea>
            </div>
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div style="max-width:240px">
                    <input type="file" name="attachment" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                </div>
                <button type="submit" class="btn btn-navy btn-sm px-3">
                    <i class="ti ti-send me-1"></i>Send Reply
                </button>
            </div>
        </form>
        @endif
    </div>
    @endif
</div>
@endsection
