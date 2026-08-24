@extends('layouts.admin')
@section('title','New Service Log')
@section('page-title','New Service Log')
@section('content')
<div class="mb-3">
    <a href="{{ route('admin.service-logs.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back to Service Logs
    </a>
</div>
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card-custom">
            <form method="POST" action="{{ route('admin.service-logs.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Service Type *</label>
                    <select name="service_type" class="form-select @error('service_type') is-invalid @enderror" required>
                        <option value="">Select type...</option>
                        @foreach(\App\Models\ServiceLog::TYPES as $t)
                        <option value="{{ $t }}" {{ old('service_type')==$t?'selected':'' }}>{{ ucwords(str_replace('_',' ',$t)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Resident Concern (Optional)</label>
                    <select name="resident_id" class="form-select">
                        <option value="">General Service / Not Specific</option>
                        @foreach($residents as $r)
                        <option value="{{ $r->id }}" {{ old('resident_id')==$r->id?'selected':'' }}>{{ $r->full_name }} — {{ $r->address }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Date of Service *</label>
                    <input type="date" name="date_of_service" class="form-control" value="{{ old('date_of_service', date('Y-m-d')) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Assigned Staff</label>
                    <select name="assigned_to" class="form-select">
                        <option value="">Unassigned</option>
                        @foreach($staff as $s)
                        <option value="{{ $s->id }}" {{ old('assigned_to')==$s->id?'selected':'' }}>{{ $s->name }} ({{ ucfirst($s->role) }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description *</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Provide details about the service or blotter entry..." required>{{ old('description') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Remarks</label>
                    <textarea name="remarks" class="form-control" rows="2" placeholder="Additional notes...">{{ old('remarks') }}</textarea>
                </div>
                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-navy"><i class="ti ti-plus me-1"></i>Create Service Log</button>
                    <a href="{{ route('admin.service-logs.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
