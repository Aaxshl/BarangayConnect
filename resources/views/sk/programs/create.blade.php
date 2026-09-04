@extends('layouts.sk')
@section('title', 'Propose SK Program')

@section('content')
<div class="mb-3">
    <a href="{{ route('sk.programs.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back to Programs List
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-9">
        <div class="card-custom p-4">
            <h5 class="fw-bold mb-1 text-primary"><i class="ti ti-target me-2"></i>Propose New SK Program or Youth Project</h5>
            <div class="text-muted small mb-4">Complete the form below to propose a youth initiative for Sangguniang Kabataan review and execution.</div>

            <form method="POST" action="{{ route('sk.programs.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-md-8">
                        <label class="form-label fw-semibold">Program Title *</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" placeholder="e.g. Annual Inter-Purok Youth Basketball Tournament" value="{{ old('title') }}" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Category *</label>
                        <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                            <option value="">Select Category...</option>
                            @foreach(\App\Models\SkProgram::CATEGORIES as $key => $lbl)
                                <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                        @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Description &amp; Objectives *</label>
                        <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror" placeholder="Explain the project scope, activities, target beneficiaries, and expected outcomes..." required>{{ old('description') }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Event Venue / Location</label>
                        <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" placeholder="e.g. Barangay Covered Court / Multipurpose Hall" value="{{ old('location') }}">
                        @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label fw-semibold">Allocated Budget (₱)</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" step="0.01" min="0" name="budget" class="form-control @error('budget') is-invalid @enderror" placeholder="0.00" value="{{ old('budget') }}">
                        </div>
                        @error('budget') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label fw-semibold">Target Participants</label>
                        <input type="number" min="0" name="target_participants" class="form-control @error('target_participants') is-invalid @enderror" placeholder="e.g. 100" value="{{ old('target_participants') }}">
                        @error('target_participants') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Start Date *</label>
                        <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', date('Y-m-d')) }}" required>
                        @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">End Date (Optional)</label>
                        <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}">
                        @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Assigned Coordinator</label>
                        <select name="coordinator_id" class="form-select @error('coordinator_id') is-invalid @enderror">
                            <option value="">Select Coordinator...</option>
                            @foreach($coordinators as $c)
                                <option value="{{ $c->id }}" {{ old('coordinator_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->role_label }})
                                </option>
                            @endforeach
                        </select>
                        @error('coordinator_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('sk.programs.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="ti ti-device-floppy me-1"></i> Submit Program Proposal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
