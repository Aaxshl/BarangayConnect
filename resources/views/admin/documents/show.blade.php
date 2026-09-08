@extends('layouts.admin')
@section('title','Document')
@section('page-title','Document Details')
@section('content')
<div class="mb-3">
    <a href="{{ route('admin.documents.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back to Documents
    </a>
</div>




{{-- Step Tracker --}}
@php
    $steps = [
        ['key' => 'pending',          'label' => 'Requested',        'icon' => 'ti-file-plus'],
        ['key' => 'under_review',     'label' => 'Under Review',     'icon' => 'ti-eye-check'],
        ['key' => 'processing',       'label' => 'Processing',       'icon' => 'ti-file-settings'],
        ['key' => 'ready_for_pickup', 'label' => 'Ready for Pickup', 'icon' => 'ti-package'],
        ['key' => 'released',         'label' => 'Released',         'icon' => 'ti-circle-check'],
    ];
    $statusOrder = array_column($steps, 'key');
    $currentIdx  = array_search($document->status, $statusOrder);
    if ($currentIdx === false) $currentIdx = -1;
    $isCancelled = $document->status === 'cancelled';
    $isReleased  = $document->status === 'released';
@endphp

@if($isCancelled)
<div class="alert alert-danger d-flex align-items-start gap-3 py-3 mb-3">
    <i class="ti ti-alert-triangle" style="font-size:22px;margin-top:2px"></i>
    <div>
        <div class="fw-bold mb-1">Document Request Rejected / Cancelled</div>
        @if($document->rejection_reason)
            <div style="font-size:13.5px">Reason: {{ $document->rejection_reason }}</div>
        @endif
    </div>
</div>
@else
{{-- 5-step progress tracker --}}
<div class="card-custom mb-3 py-4 px-3">
    <div class="d-flex align-items-start" style="position:relative">
        {{-- Progress line behind dots --}}
        <div style="position:absolute;top:17px;left:9%;right:9%;height:3px;z-index:0;
            background:linear-gradient(to right,
                {{ $isReleased ? '#059669' : '#059669' }} {{ $isReleased ? '100%' : (max(0, $currentIdx) / 4 * 100).'%' }},
                #e2e8f0 {{ $isReleased ? '0%' : (max(0, $currentIdx) / 4 * 100).'%' }});">
        </div>
        @foreach($steps as $i => $step)
            @php
                if ($isReleased) {
                    $state = 'done';
                } elseif ($i < $currentIdx) {
                    $state = 'done';
                } elseif ($i === $currentIdx) {
                    $state = 'current';
                } else {
                    $state = 'upcoming';
                }
            @endphp
            <div class="text-center flex-fill" style="position:relative;z-index:1">
                <div style="width:36px;height:36px;border-radius:50%;margin:0 auto 8px;
                    display:flex;align-items:center;justify-content:center;font-size:16px;
                    @if($state === 'done') background:#059669;color:#fff;
                    @elseif($state === 'current') background:#185fa5;color:#fff;box-shadow:0 0 0 4px rgba(24,95,165,0.18);
                    @else background:#e2e8f0;color:#94a3b8;
                    @endif">
                    <i class="ti {{ $state === 'done' ? 'ti-check' : $step['icon'] }}"></i>
                </div>
                <div style="font-size:11px;line-height:1.3;
                    font-weight:{{ $state === 'current' ? '700' : '500' }};
                    color:{{ $state === 'upcoming' ? '#94a3b8' : '#1e293b' }}">
                    {{ $step['label'] }}
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

<div class="row justify-content-center">
    <div class="col-12 col-lg-9">
        <div class="card-custom">
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
                <div>
                    <div style="font-family:monospace;font-size:14px;font-weight:700;color:#185fa5">{{ $document->document_number }}</div>
                    <h5 class="mb-0 mt-1">{{ \App\Models\Document::TYPES[$document->document_type] ?? $document->document_type }}</h5>
                </div>
                <div class="d-flex gap-2 align-items-center flex-wrap">
                    {!! $document->aging_badge !!}
                    <span class="badge-status badge-{{ $document->status }}">{{ ucwords(str_replace('_',' ',$document->status)) }}</span>
                    @if(in_array($document->status, ['processing','ready_for_pickup','released']) && auth()->user()->canDo('documents.print'))
                        <a href="{{ route('admin.documents.print',$document) }}" class="btn btn-navy btn-sm" target="_blank">
                            <i class="ti ti-printer me-1"></i>Print PDF
                        </a>
                    @endif
                </div>
            </div>

            {{-- Document Details --}}
            <div class="row g-3 pb-3 border-bottom" style="font-size:13.5px">
                <div class="col-md-6">
                    <span class="text-muted" style="font-size:12px">Resident</span>
                    <div class="fw-semibold">{{ optional($document->resident)->full_name }}</div>
                </div>
                <div class="col-md-6">
                    <span class="text-muted" style="font-size:12px">Document Type</span>
                    <div class="fw-semibold">{{ \App\Models\Document::TYPES[$document->document_type] ?? $document->document_type }}</div>
                </div>
                <div class="col-md-6">
                    <span class="text-muted" style="font-size:12px">Purpose</span>
                    <div>{{ $document->purpose }}</div>
                </div>
                <div class="col-md-3">
                    <span class="text-muted" style="font-size:12px">Copies Requested</span>
                    <div>{{ $document->number_of_copies }}</div>
                </div>
                <div class="col-md-3">
                    <span class="text-muted" style="font-size:12px">Requested Date</span>
                    <div>{{ $document->issue_date->format('M d, Y') }}</div>
                </div>
                @if($document->viewed_at)
                <div class="col-md-6">
                    <span class="text-muted" style="font-size:12px">Reviewed on</span>
                    <div>{{ $document->viewed_at->format('M d, Y g:i A') }}</div>
                </div>
                @endif
                @if($document->released_at)
                <div class="col-md-6">
                    <span class="text-muted" style="font-size:12px">Released on</span>
                    <div class="text-success fw-semibold">{{ $document->released_at->format('M d, Y g:i A') }}</div>
                </div>
                @endif
                @if(optional($document->issuedBy)->name)
                <div class="col-md-6">
                    <span class="text-muted" style="font-size:12px">Processed by</span>
                    <div>{{ $document->issuedBy->name }}</div>
                </div>
                @endif
                @if($document->remarks)
                <div class="col-12">
                    <span class="text-muted" style="font-size:12px">Remarks</span>
                    <div>{{ $document->remarks }}</div>
                </div>
                @endif
            </div>

            {{-- ═══ Payment Details & Verification Section ═══ --}}
            <div class="mt-4 pt-3 border-bottom pb-4">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <div>
                        <div class="fw-semibold" style="font-size:12.5px;color:#64748b;text-transform:uppercase;letter-spacing:0.5px">
                            <i class="ti ti-cash me-1 text-primary"></i> Payment Information &amp; Verification
                        </div>
                        <div class="text-muted small">Track processing fees, online GCash transactions, and counter payments.</div>
                    </div>
                    <div>
                        {!! $document->payment_status_badge !!}
                    </div>
                </div>

                <div class="row g-3 p-3 rounded border bg-light mb-3 align-items-center" style="font-size:13px">
                    <div class="col-6 col-md-3">
                        <span class="text-muted" style="font-size:11.5px">Total Fee Due</span>
                        <div class="fw-bold fs-6 {{ (float)$document->fee > 0 ? 'text-primary' : 'text-success' }}">
                            {{ (float)$document->fee > 0 ? '₱' . number_format($document->fee, 2) : 'FREE' }}
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <span class="text-muted" style="font-size:11.5px">Payment Method</span>
                        <div class="fw-semibold">
                            {{ \App\Models\Document::PAYMENT_METHODS[$document->payment_method] ?? ucfirst($document->payment_method) }}
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <span class="text-muted" style="font-size:11.5px">Transaction Reference</span>
                        <div class="font-monospace fw-bold text-dark">
                            {{ $document->payment_reference ?: '—' }}
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <span class="text-muted" style="font-size:11.5px">Proof Screenshot</span>
                        <div>
                            @if($document->payment_proof)
                                <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" data-bs-toggle="modal" data-bs-target="#viewProofModal">
                                    <i class="ti ti-eye me-1"></i> View Receipt
                                </button>
                            @else
                                <span class="text-muted">None uploaded</span>
                            @endif
                        </div>
                    </div>

                    {{-- Verification Audit / Declined Note --}}
                    @if($document->payment_verified_at)
                    <div class="col-12 pt-2 border-top">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 small text-muted">
                            <div>
                                <i class="ti ti-check-circle text-success me-1"></i>
                                Action recorded by <strong>{{ optional($document->paymentVerifiedBy)->name ?? 'Barangay Staff' }}</strong>
                                on {{ $document->payment_verified_at->format('M d, Y g:i A') }}
                            </div>
                            @if($document->payment_notes)
                                <div class="text-dark"><strong>Note:</strong> {{ $document->payment_notes }}</div>
                            @endif
                        </div>
                    </div>
                    @elseif($document->payment_notes)
                    <div class="col-12 pt-2 border-top">
                        <div class="small text-danger"><strong>Note:</strong> {{ $document->payment_notes }}</div>
                    </div>
                    @endif
                </div>

                {{-- Action Controls for Payment --}}
                @if(auth()->user()->canDo('documents.process'))
                <div class="d-flex gap-2 flex-wrap">
                    @if($document->payment_status !== 'verified')
                        {{-- Verify Payment (If GCash proof or reference provided) --}}
                        @if($document->payment_method === 'gcash' && ($document->payment_proof || $document->payment_reference))
                            <form method="POST" action="{{ route('admin.documents.payment', $document) }}">
                                @csrf
                                <input type="hidden" name="action" value="verify">
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="ti ti-check me-1"></i> Verify Payment
                                </button>
                            </form>
                        @endif

                        {{-- Collect Cash on Counter --}}
                        <form method="POST" action="{{ route('admin.documents.payment', $document) }}" onsubmit="return confirm('Confirm receipt of cash payment ₱{{ number_format($document->fee, 2) }} on counter?')">
                            @csrf
                            <input type="hidden" name="action" value="mark_cash">
                            <button type="submit" class="btn btn-outline-success btn-sm">
                                <i class="ti ti-cash me-1"></i> Confirm Counter Cash (₱{{ number_format($document->fee, 2) }})
                            </button>
                        </form>

                        {{-- Decline Payment Proof --}}
                        @if($document->payment_method === 'gcash' && ($document->payment_proof || $document->payment_reference))
                            <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#declinePaymentModal">
                                <i class="ti ti-x me-1"></i> Decline Proof
                            </button>
                        @endif

                        {{-- Waive Fee --}}
                        @if((float)$document->fee > 0 && $document->payment_status !== 'waived')
                            <form method="POST" action="{{ route('admin.documents.payment', $document) }}" onsubmit="return confirm('Waive document fee for this resident (e.g. Indigent exemption)?')">
                                @csrf
                                <input type="hidden" name="action" value="waive">
                                <button type="submit" class="btn btn-outline-info btn-sm">
                                    <i class="ti ti-discount-check me-1"></i> Waive Fee
                                </button>
                            </form>
                        @endif
                    @else
                        <div class="badge bg-success-subtle text-success border border-success-subtle py-2 px-3">
                            <i class="ti ti-circle-check me-1"></i> Payment verified &amp; cleared for release.
                        </div>
                    @endif
                </div>
                @endif
            </div>

            {{-- ═══ Progressive Action Buttons ═══ --}}
            @if(!in_array($document->status, ['released','cancelled']))
            <div class="mt-4">
                <div class="fw-semibold mb-3" style="font-size:12.5px;color:#64748b;text-transform:uppercase;letter-spacing:0.5px">
                    <i class="ti ti-player-play me-1 text-primary"></i> Next Action
                </div>
                <div class="d-flex gap-2 flex-wrap">

                    {{-- under_review → processing --}}
                    @if($document->status === 'under_review' && auth()->user()->canDo('documents.process'))
                        <form method="POST" action="{{ route('admin.documents.status', $document) }}">
                            @csrf
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="btn btn-success">
                                <i class="ti ti-check me-1"></i> Approve &amp; Prepare Document
                            </button>
                        </form>
                    @endif

                    {{-- processing → ready_for_pickup --}}
                    @if($document->status === 'processing' && auth()->user()->canDo('documents.process'))
                        <form method="POST" action="{{ route('admin.documents.status', $document) }}">
                            @csrf
                            <input type="hidden" name="action" value="mark_ready">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-package me-1"></i> Mark Ready for Pickup
                            </button>
                        </form>
                    @endif

                    {{-- ready_for_pickup → released --}}
                    @if($document->status === 'ready_for_pickup' && auth()->user()->canDo('documents.release'))
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#releaseModal">
                            <i class="ti ti-circle-check me-1"></i> Release to Resident
                        </button>
                    @endif

                    {{-- Express Direct Release for walk-in residents (from pending, under_review, or processing) --}}
                    @if(in_array($document->status, ['pending', 'under_review', 'processing']) && auth()->user()->canDo('documents.release'))
                        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#releaseModal" title="Release immediately without intermediate steps">
                            <i class="ti ti-bolt me-1"></i> Express Release
                        </button>
                    @endif

                    {{-- Reject button (always visible for authorized roles) --}}
                    @if(auth()->user()->canDo('documents.reject'))
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="ti ti-x me-1"></i> Reject / Cancel Request
                    </button>
                    @endif
                </div>
            </div>
            @endif

            {{-- Released: Reissue option --}}
            @if($document->status === 'released' && auth()->user()->canDo('documents.create'))
            <div class="mt-4 pt-3 border-top">
                <form method="POST" action="{{ route('admin.documents.reissue', $document) }}" onsubmit="return confirm('Reissue this document as a new request?')">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary btn-sm">
                        <i class="ti ti-refresh me-1"></i> Reissue as New Request
                    </button>
                </form>
            </div>
            @endif

            {{-- Cancelled: Reissue option --}}
            @if($document->status === 'cancelled' && auth()->user()->canDo('documents.create'))
            <div class="mt-4 pt-3 border-top">
                <form method="POST" action="{{ route('admin.documents.reissue', $document) }}" onsubmit="return confirm('Create a new request for this document?')">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary btn-sm">
                        <i class="ti ti-refresh me-1"></i> Create New Request for This Resident
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Release Modal --}}
<div class="modal fade" id="releaseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.documents.status', $document) }}">
                @csrf
                <input type="hidden" name="action" value="release">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">
                        <i class="ti ti-circle-check text-success me-2"></i>
                        {{ $document->status === 'ready_for_pickup' ? 'Release Document to Resident' : 'Express Release Document to Resident' }}
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if($document->status !== 'ready_for_pickup')
                        <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center gap-2" style="font-size:12.5px">
                            <i class="ti ti-bolt fs-5"></i>
                            <div><strong>Express Release:</strong> Immediately completes and marks this document as released to the resident.</div>
                        </div>
                    @endif
                    <p class="small text-muted mb-3">
                        Confirm that <strong>{{ optional($document->resident)->full_name }}</strong> has personally claimed
                        <strong>{{ \App\Models\Document::TYPES[$document->document_type] ?? $document->document_type }}</strong>
                        ({{ $document->document_number }}) with a valid ID.
                    </p>

                    {{-- Payment Warning in Release Modal if unpaid --}}
                    @if(!$document->isPaidOrWaived())
                        <div class="alert alert-warning py-2 px-3 mb-3" style="font-size:13px">
                            <div class="fw-bold mb-1"><i class="ti ti-cash me-1"></i> Outstanding Fee: ₱{{ number_format($document->fee, 2) }}</div>
                            <div class="form-check m-0">
                                <input class="form-check-input" type="checkbox" name="confirm_cash_payment" value="1" id="confirmCashPayment" required>
                                <label class="form-check-label fw-semibold" for="confirmCashPayment">
                                    I confirm that cash payment of ₱{{ number_format($document->fee, 2) }} was collected on the counter.
                                </label>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-success py-2 px-3 mb-3 d-flex align-items-center gap-2" style="font-size:12.5px">
                            <i class="ti ti-circle-check fs-5"></i>
                            <div>Payment verified &amp; cleared ({!! $document->payment_status_badge !!}).</div>
                        </div>
                    @endif

                    <label class="form-label" style="font-size:13px;font-weight:600">Release notes (optional)</label>
                    <textarea name="remarks" class="form-control" rows="2" placeholder="e.g. Claimed personally with PhilSys ID">{{ $document->remarks }}</textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="ti ti-circle-check me-1"></i>Confirm Release
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- View Proof Receipt Modal --}}
@if($document->payment_proof)
<div class="modal fade" id="viewProofModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold"><i class="ti ti-photo text-primary me-2"></i>Uploaded Payment Proof</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-3">
                <img src="{{ asset('storage/' . $document->payment_proof) }}" alt="Receipt" style="max-height:480px;max-width:100%;object-fit:contain" class="rounded border shadow-sm">
                @if($document->payment_reference)
                    <div class="mt-2 text-muted small">
                        Reference Number: <strong class="font-monospace text-dark">{{ $document->payment_reference }}</strong>
                    </div>
                @endif
            </div>
            <div class="modal-footer justify-content-between">
                <a href="{{ asset('storage/' . $document->payment_proof) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="ti ti-external-link me-1"></i>Open Full Image
                </a>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Decline Payment Proof Modal --}}
<div class="modal fade" id="declinePaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.documents.payment', $document) }}">
                @csrf
                <input type="hidden" name="action" value="decline">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold"><i class="ti ti-x text-danger me-2"></i>Decline Payment Proof</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-2">
                        State the reason why this payment proof is being declined. This explanation note will be immediately visible to the resident on their tracking portal so they can re-upload a valid proof.
                    </p>
                    <label class="form-label fw-semibold" style="font-size:13px">Reason / Explanation Note <span class="text-danger">*</span></label>
                    <textarea name="payment_notes" class="form-control" rows="3" required placeholder="e.g. Reference number does not match transaction records, blurred receipt screenshot, incomplete payment amount..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="ti ti-x me-1"></i>Decline Payment Proof
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.documents.status', $document) }}">
                @csrf
                <input type="hidden" name="action" value="reject">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold"><i class="ti ti-alert-triangle text-danger me-2"></i>Reject Document Request</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label fw-semibold" style="font-size:13px">Reason for rejection <span class="text-danger">*</span></label>
                    <textarea name="rejection_reason" class="form-control" rows="3" required
                        placeholder="e.g. Incomplete requirements, unresolved blotter, not a registered resident..."></textarea>
                    <div class="form-text mt-1">This reason will be visible to the resident on their tracking page.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm"><i class="ti ti-x me-1"></i>Confirm Rejection</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
