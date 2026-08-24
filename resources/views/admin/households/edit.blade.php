@extends('layouts.admin')
@section('title','Edit Household')
@section('page-title','Edit Household — ' . $household->household_id)
@section('content')
<div class="mb-3">
    <a href="{{ route('admin.households.show', $household) }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back to Household
    </a>
</div>
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card-custom">
            <form method="POST" action="{{ route('admin.households.update', $household) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Household ID Number</label>
                    <input type="text" class="form-control" value="{{ $household->household_id }}" disabled readonly>
                    <div class="form-text">Household ID is unique and cannot be modified.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Street Address *</label>
                    <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $household->address) }}" required>
                    <div class="invalid-feedback">{{ $errors->first('address') }}</div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Purok</label>
                        <input type="text" name="purok" class="form-control" value="{{ old('purok', $household->purok) }}" placeholder="e.g. Purok 1">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Zone</label>
                        <input type="text" name="zone" class="form-control" value="{{ old('zone', $household->zone) }}" placeholder="e.g. Zone A">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contact Number</label>
                    <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number', $household->contact_number) }}" placeholder="09XXXXXXXXX">
                </div>
                <div class="mb-3">
                    <label class="form-label">Head of Household</label>
                    <select name="head_resident_id" class="form-select">
                        <option value="">Select Head of Household...</option>
                        @foreach($residents as $r)
                        <option value="{{ $r->id }}" {{ old('head_resident_id', $household->head_resident_id)==$r->id?'selected':'' }}>
                            {{ $r->full_name }} — {{ $r->address }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-navy"><i class="ti ti-check me-1"></i>Update Household</button>
                    <a href="{{ route('admin.households.show', $household) }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
