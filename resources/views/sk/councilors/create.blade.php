@extends('layouts.sk')
@section('title', 'Add SK Councilor')

@section('content')
<div class="mb-3">
    <a href="{{ route('sk.councilors.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back to SK Councilors
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-7">
        <div class="card-custom p-4">
            <h5 class="fw-bold mb-1 text-primary"><i class="ti ti-user-plus me-2"></i>Register New SK Councilor</h5>
            <div class="text-muted small mb-4">Create an official portal account for an elected or appointed Sangguniang Kabataan Councilor.</div>

            <form method="POST" action="{{ route('sk.councilors.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Full Name *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Maria Angela Reyes" value="{{ old('name') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Official Email Address *</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="e.g. angela.sk@brgy.gov.ph" value="{{ old('email') }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Contact Number</label>
                        <input type="text" name="contact_number" class="form-control @error('contact_number') is-invalid @enderror" placeholder="0917XXXXXXX" value="{{ old('contact_number') }}">
                        @error('contact_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Password *</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Min. 8 characters" required>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Confirm Password *</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Re-type password" required>
                    </div>

                    <div class="col-12">
                        <div class="p-2 border rounded bg-light small text-muted">
                            <i class="ti ti-info-circle me-1 text-primary"></i> The newly created user will be automatically assigned the <strong>SK Councilor</strong> role and granted access to the SK Portal.
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('sk.councilors.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="ti ti-user-plus me-1"></i> Register SK Councilor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
