@extends('layouts.portal')
@section('title','Request a Document')
@section('content')
<div class="container-fluid px-3 px-md-4 mt-4" style="max-width:680px">
    <div class="mb-4">
        <h2 class="section-title mb-1">Request a Document</h2>
        <p class="text-muted small">Submit an official barangay certificate or clearance request online.</p>
    </div>

    <form method="POST" action="{{ route('portal.request.submit') }}" enctype="multipart/form-data" id="requestForm">
        @csrf
        
        @if($errors->any())
            <div class="alert alert-danger py-2 px-3 mb-3 small">
                <i class="ti ti-alert-circle me-1"></i>{{ $errors->first() }}
            </div>
        @endif

        <div class="portal-card mb-3">
            <div class="portal-card-title mb-3">Document Request Information</div>

            {{-- Document Type Dropdown --}}
            <div class="mb-3">
                <label class="form-label fw-semibold" for="document_type">Document Type *</label>
                <select name="document_type" id="document_type" class="form-select @error('document_type') is-invalid @enderror" required onchange="calculateFee()">
                    <option value="">-- Select Document Type --</option>
                    @foreach(\App\Models\Document::TYPES as $k => $v)
                        <option value="{{ $k }}" data-fee="{{ $fees[$k] ?? 50 }}" {{ old('document_type') == $k ? 'selected' : '' }}>
                            {{ $v }} — {{ ($fees[$k] ?? 50) > 0 ? '₱' . number_format($fees[$k] ?? 50, 2) : 'FREE' }}
                        </option>
                    @endforeach
                </select>
                @error('document_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Purpose --}}
            <div class="mb-3">
                <label class="form-label fw-semibold" for="purpose">Purpose *</label>
                <input type="text" name="purpose" id="purpose" class="form-control @error('purpose') is-invalid @enderror" 
                    placeholder="e.g. Employment requirement, Bank account opening, School enrollment, Scholarship application..." 
                    value="{{ old('purpose') }}" required>
                @error('purpose') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <div class="form-text" style="font-size:12px">State clearly where and why this document will be used.</div>
            </div>

            {{-- Number of Copies --}}
            <div class="mb-2">
                <label class="form-label fw-semibold" for="number_of_copies">Number of Copies *</label>
                <select name="number_of_copies" id="number_of_copies" class="form-select" style="max-width: 200px;" onchange="calculateFee()">
                    <option value="1" {{ old('number_of_copies', 1) == 1 ? 'selected' : '' }}>1 Copy</option>
                    <option value="2" {{ old('number_of_copies', 2) == 2 ? 'selected' : '' }}>2 Copies</option>
                    <option value="3" {{ old('number_of_copies', 3) == 3 ? 'selected' : '' }}>3 Copies</option>
                    <option value="4" {{ old('number_of_copies', 4) == 4 ? 'selected' : '' }}>4 Copies</option>
                    <option value="5" {{ old('number_of_copies', 5) == 5 ? 'selected' : '' }}>5 Copies</option>
                </select>
            </div>
        </div>

        {{-- Payment Method Card --}}
        <div class="portal-card mb-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="portal-card-title mb-0">Payment Summary &amp; Option</div>
                <div class="text-end">
                    <span class="text-muted small">Total Fee:</span>
                    <span class="fw-bold fs-5 text-primary ms-1" id="totalFeeDisplay">₱0.00</span>
                </div>
            </div>

            <div id="freeDocumentNotice" class="alert alert-info py-2 px-3 mb-0 d-none" style="font-size:13px">
                <i class="ti ti-discount-check me-1"></i> This document is <strong>FREE</strong> of charge. No payment required.
            </div>

            <div id="paymentOptionsContainer">
                <label class="form-label fw-semibold mb-2">Select Payment Method *</label>
                
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="payment-card d-block p-3 border rounded text-center cursor-pointer" style="cursor:pointer;transition:all .2s" id="label-cash">
                            <input type="radio" name="payment_method" value="cash" id="pay_cash" class="d-none" {{ old('payment_method', 'cash') === 'cash' ? 'checked' : '' }} onchange="updatePaymentMethodUI()">
                            <i class="ti ti-cash fs-3 text-secondary mb-1 d-block"></i>
                            <div class="fw-bold" style="font-size:13.5px">Cash on Pickup</div>
                            <div class="text-muted" style="font-size:11px">Pay at the Barangay Hall</div>
                        </label>
                    </div>
                    <div class="col-6">
                        <label class="payment-card d-block p-3 border rounded text-center cursor-pointer" style="cursor:pointer;transition:all .2s" id="label-gcash">
                            <input type="radio" name="payment_method" value="gcash" id="pay_gcash" class="d-none" {{ old('payment_method') === 'gcash' ? 'checked' : '' }} onchange="updatePaymentMethodUI()">
                            <i class="ti ti-device-mobile fs-3 text-primary mb-1 d-block"></i>
                            <div class="fw-bold" style="font-size:13.5px">GCash / Online</div>
                            <div class="text-muted" style="font-size:11px">Upload receipt screenshot</div>
                        </label>
                    </div>
                </div>

                {{-- GCash Payment Details Section --}}
                <div id="gcashDetailsSection" class="p-3 bg-light rounded border mb-2 d-none">
                    <div class="d-flex align-items-center justify-content-between pb-2 mb-3 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:30px;height:30px;border-radius:6px;background:#007dfe;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:14px">G</div>
                            <div>
                                <div class="fw-bold text-dark" style="font-size:13px">{{ $gcash['account_name'] ?? 'Barangay Treasury' }}</div>
                                <div class="text-muted" style="font-size:11.5px;font-family:monospace">{{ $gcash['account_number'] ?? '09XX-XXX-XXXX' }}</div>
                            </div>
                        </div>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Online Payment</span>
                    </div>

                    @if(!empty($gcash['qr_code']))
                        <div class="text-center mb-3">
                            <div class="p-2 bg-white d-inline-block border rounded shadow-sm">
                                <img src="{{ asset('storage/' . $gcash['qr_code']) }}" alt="Barangay GCash QR" style="max-height:160px;max-width:100%;object-fit:contain">
                            </div>
                            <div class="text-muted small mt-1">Scan using GCash app to send payment</div>
                        </div>
                    @endif

                    @if(!empty($gcash['instructions']))
                        <div class="alert alert-secondary py-2 px-3 small mb-3">
                            <i class="ti ti-info-circle me-1"></i> {{ $gcash['instructions'] }}
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="payment_reference">GCash Reference Number *</label>
                        <input type="text" name="payment_reference" id="payment_reference" class="form-control form-control-sm @error('payment_reference') is-invalid @enderror" placeholder="e.g. 100234892019" value="{{ old('payment_reference') }}">
                        <div class="form-text" style="font-size:11.5px">The 11 to 13-digit transaction reference number found on your GCash receipt.</div>
                    </div>

                    <div class="mb-1">
                        <label class="form-label fw-semibold" for="payment_proof">Upload Proof of Payment (Screenshot) *</label>
                        <input type="file" name="payment_proof" id="payment_proof" class="form-control form-control-sm @error('payment_proof') is-invalid @enderror" accept="image/*">
                        <div class="form-text" style="font-size:11.5px">Clear screenshot or photo of transaction receipt (PNG, JPG, max 5MB).</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Applicant Details Card --}}
        <div class="portal-card mb-4">
            <div class="portal-card-title mb-2">Applicant Profile Details</div>
            @php $res = \App\Models\Resident::find(session('resident_id')); @endphp
            <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:13.5px">
                <span class="text-muted">Full Name</span>
                <span class="fw-semibold">{{ $res->full_name }}</span>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:13.5px">
                <span class="text-muted">Registered Address</span>
                <span>{{ $res->address }}</span>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:13.5px">
                <span class="text-muted">Contact Number</span>
                <span>{{ $res->contact_number }}</span>
            </div>
            <div class="d-flex justify-content-between py-2" style="font-size:13.5px">
                <span class="text-muted">Resident ID</span>
                <span style="font-family:monospace;font-weight:600;color:#185fa5">RES-{{ str_pad($res->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
        </div>

        <button type="submit" class="btn-navy-full py-2.5 fw-bold" style="border-radius:10px;font-size:15px">
            <i class="ti ti-send me-1"></i> Submit Document Request
        </button>
    </form>
</div>

@push('scripts')
<script>
const feeCatalog = @json($fees);

function calculateFee() {
    const docSelect = document.getElementById('document_type');
    const copiesSelect = document.getElementById('number_of_copies');
    const feeDisplay = document.getElementById('totalFeeDisplay');
    const freeNotice = document.getElementById('freeDocumentNotice');
    const paymentContainer = document.getElementById('paymentOptionsContainer');

    const selectedType = docSelect.value;
    const copies = parseInt(copiesSelect.value) || 1;
    const unitFee = (selectedType && feeCatalog[selectedType] !== undefined) ? parseFloat(feeCatalog[selectedType]) : 50.00;
    const total = unitFee * copies;

    if (total <= 0) {
        feeDisplay.textContent = 'FREE';
        feeDisplay.className = 'fw-bold fs-5 text-success ms-1';
        freeNotice.classList.remove('d-none');
        paymentContainer.classList.add('d-none');
        // Uncheck required for gcash inputs when free
        document.getElementById('payment_reference').removeAttribute('required');
        document.getElementById('payment_proof').removeAttribute('required');
    } else {
        feeDisplay.textContent = '₱' + total.toFixed(2);
        feeDisplay.className = 'fw-bold fs-5 text-primary ms-1';
        freeNotice.classList.add('d-none');
        paymentContainer.classList.remove('d-none');
        updatePaymentMethodUI();
    }
}

function updatePaymentMethodUI() {
    const isGcash = document.getElementById('pay_gcash').checked;
    const gcashSection = document.getElementById('gcashDetailsSection');
    const labelCash = document.getElementById('label-cash');
    const labelGcash = document.getElementById('label-gcash');
    const refInput = document.getElementById('payment_reference');
    const proofInput = document.getElementById('payment_proof');

    if (isGcash) {
        gcashSection.classList.remove('d-none');
        labelGcash.style.borderColor = '#185fa5';
        labelGcash.style.background = '#eff6ff';
        labelCash.style.borderColor = '#e2e8f0';
        labelCash.style.background = '#fff';
        refInput.setAttribute('required', 'required');
        proofInput.setAttribute('required', 'required');
    } else {
        gcashSection.classList.add('d-none');
        labelCash.style.borderColor = '#185fa5';
        labelCash.style.background = '#eff6ff';
        labelGcash.style.borderColor = '#e2e8f0';
        labelGcash.style.background = '#fff';
        refInput.removeAttribute('required');
        proofInput.removeAttribute('required');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    calculateFee();
});
</script>
@endpush
@endsection
