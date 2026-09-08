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
            <div style="font-size:13.5px" class="mb-2"><strong>Purpose:</strong> {{ $item->purpose }}</div>
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge-status badge-{{ $item->status }}">{{ ucwords(str_replace('_',' ',$item->status)) }}</span>
                {!! $item->payment_status_badge !!}
            </div>

            {{-- Payment Breakdown Box --}}
            <div class="p-3 rounded border mb-3 bg-light" style="font-size:13px">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Processing Fee ({{ $item->number_of_copies }} {{ Str::plural('copy', $item->number_of_copies) }}):</span>
                    <span class="fw-bold">{{ (float)$item->fee > 0 ? '₱' . number_format($item->fee, 2) : 'FREE' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Payment Method:</span>
                    <span>{{ \App\Models\Document::PAYMENT_METHODS[$item->payment_method] ?? ucfirst($item->payment_method) }}</span>
                </div>
                @if($item->payment_reference)
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Payment Reference:</span>
                    <span class="font-monospace fw-semibold">{{ $item->payment_reference }}</span>
                </div>
                @endif
                @if($item->payment_proof)
                <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                    <span class="text-muted">Proof Submitted:</span>
                    <a href="{{ asset('storage/' . $item->payment_proof) }}" target="_blank" class="text-primary fw-semibold" style="text-decoration:none">
                        <i class="ti ti-photo me-1"></i>View Uploaded Receipt
                    </a>
                </div>
                @endif
            </div>

            {{-- Declined Payment Alert with Note --}}
            @if($item->payment_status === 'declined')
                <div class="alert alert-danger py-3 px-3 mb-3" style="font-size:13px;border-radius:10px">
                    <div class="d-flex align-items-center gap-2 fw-bold text-danger mb-1">
                        <i class="ti ti-alert-triangle fs-5"></i> Payment Proof Declined by Barangay Staff
                    </div>
                    <div class="p-2.5 rounded bg-white text-danger border border-danger-subtle my-2" style="line-height:1.5">
                        <strong>Reason / Note from Staff:</strong><br>
                        {{ $item->payment_notes ?: 'Your payment proof or transaction reference could not be verified by the Barangay Office.' }}
                    </div>
                    <p class="mb-2 text-dark small">
                        Please review the explanation above, verify your transaction details, and upload a valid receipt screenshot below.
                    </p>
                    <div class="mt-2 pt-2 border-top border-danger-subtle">
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#uploadProofModal">
                            <i class="ti ti-upload me-1"></i> Submit Corrected Payment Proof
                        </button>
                    </div>
                </div>
            @elseif($item->payment_status === 'unpaid' && (float)$item->fee > 0)
                <div class="alert alert-warning py-3 px-3 mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2" style="font-size:13px">
                    <div>
                        <div class="fw-bold"><i class="ti ti-cash me-1"></i> Payment Required: ₱{{ number_format($item->fee, 2) }}</div>
                        <div class="text-muted small">You can pay via GCash online or settle cash upon claiming.</div>
                    </div>
                    <button type="button" class="btn btn-navy btn-sm" data-bs-toggle="modal" data-bs-target="#uploadProofModal">
                        <i class="ti ti-device-mobile me-1"></i> Pay with GCash
                    </button>
                </div>
            @endif

            @if(in_array($item->status, ['ready_for_pickup', 'pending_pickup']))
            <div class="info-banner mt-3">
                <i class="ti ti-building"></i>
                Ready for pickup at the Barangay Hall. Mon–Fri, 8 AM – 5 PM.
                @if(!$item->isPaidOrWaived())
                    <strong>Please prepare ₱{{ number_format($item->fee, 2) }} for payment upon pickup.</strong>
                @endif
                Bring a valid ID.
            </div>
            @endif
        @endif
    </div>

    {{-- Upload Payment Proof Modal for Document --}}
    @if(!$item instanceof \App\Models\CitizenRequest && (float)$item->fee > 0 && in_array($item->payment_status, ['unpaid', 'declined']))
    @php $gcash = \App\Models\Setting::getGcashSettings(); @endphp
    <div class="modal fade" id="uploadProofModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('portal.track.paymentProof', $item->document_number) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h6 class="modal-title fw-bold">
                            <i class="ti ti-device-mobile text-primary me-2"></i>
                            {{ $item->payment_status === 'declined' ? 'Re-upload GCash Payment Proof' : 'Submit GCash Payment Proof' }}
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" style="font-size:13px">
                        @if($item->payment_status === 'declined' && $item->payment_notes)
                        <div class="alert alert-danger py-2 px-3 mb-3 small" style="border-radius:8px">
                            <strong><i class="ti ti-alert-triangle me-1"></i>Staff Explanation Note:</strong>
                            <div class="mt-1" style="line-height:1.4">{{ $item->payment_notes }}</div>
                        </div>
                        @endif

                        <div class="p-3 bg-light rounded border mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Total Due:</span>
                                <span class="fw-bold fs-6 text-primary">₱{{ number_format($item->fee, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted">GCash Account Name:</span>
                                <span class="fw-semibold">{{ $gcash['account_name'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">GCash Number:</span>
                                <span class="font-monospace fw-bold">{{ $gcash['account_number'] }}</span>
                            </div>
                        </div>

                        @if(!empty($gcash['qr_code']))
                        <div class="text-center mb-3">
                            <img src="{{ asset('storage/' . $gcash['qr_code']) }}" alt="GCash QR" style="max-height:140px;object-fit:contain" class="border rounded p-2 bg-white shadow-sm">
                            <div class="text-muted small mt-1">Scan or save QR code</div>
                        </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="modal_payment_reference">GCash Reference Number *</label>
                            <input type="text" name="payment_reference" id="modal_payment_reference" class="form-control form-control-sm" placeholder="e.g. 100234892019" value="{{ old('payment_reference', $item->payment_reference) }}" required>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-semibold" for="modal_payment_proof">Proof of Payment (Screenshot) *</label>
                            <input type="file" name="payment_proof" id="modal_payment_proof" class="form-control form-control-sm" accept="image/*" required>
                            <div class="form-text" style="font-size:11.5px">Upload the official GCash transaction confirmation receipt image.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-navy btn-sm">
                            <i class="ti ti-upload me-1"></i> Submit Payment Proof
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

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
