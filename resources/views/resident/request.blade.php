@extends('layouts.portal')
@section('title','Request a Document')
@section('content')
<div class="container-fluid px-3 px-md-4 mt-4" style="max-width:680px">
    <div class="mb-4">
        <h2 class="section-title mb-1">Request a Document</h2>
        <p class="text-muted small">Submit an official barangay certificate or clearance request online.</p>
    </div>

    <form method="POST" action="{{ route('portal.request.submit') }}">
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
                <select name="document_type" id="document_type" class="form-select @error('document_type') is-invalid @enderror" required>
                    <option value="">-- Select Document Type --</option>
                    @foreach(\App\Models\Document::TYPES as $k => $v)
                        <option value="{{ $k }}" {{ old('document_type') == $k ? 'selected' : '' }}>
                            {{ $v }}
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
                <select name="number_of_copies" id="number_of_copies" class="form-select" style="max-width: 200px;">
                    <option value="1" {{ old('number_of_copies', 1) == 1 ? 'selected' : '' }}>1 Copy</option>
                    <option value="2" {{ old('number_of_copies') == 2 ? 'selected' : '' }}>2 Copies</option>
                    <option value="3" {{ old('number_of_copies') == 3 ? 'selected' : '' }}>3 Copies</option>
                    <option value="4" {{ old('number_of_copies') == 4 ? 'selected' : '' }}>4 Copies</option>
                    <option value="5" {{ old('number_of_copies') == 5 ? 'selected' : '' }}>5 Copies</option>
                </select>
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
@endsection
