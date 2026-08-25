@extends('layouts.admin')
@section('title','Edit Service Log')
@section('page-title','Edit Service Log')
@section('content')
<div class="mb-3">
    <a href="{{ route('admin.service-logs.show', $serviceLog) }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back to Details
    </a>
</div>
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card-custom">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0 fw-bold">Edit Record: <span style="font-family:monospace;color:#185fa5">{{ $serviceLog->log_number }}</span></h5>
                <span class="badge-status badge-{{ $serviceLog->status }}">{{ ucwords(str_replace('_',' ',$serviceLog->status)) }}</span>
            </div>
            <form method="POST" action="{{ route('admin.service-logs.update', $serviceLog) }}">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Service Type *</label>
                        <select name="service_type" class="form-select" required>
                            @foreach(\App\Models\ServiceLog::TYPES as $t)
                            <option value="{{ $t }}" {{ old('service_type',$serviceLog->service_type)==$t?'selected':'' }}>{{ ucwords(str_replace('_',' ',$t)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Resident / Party Involved</label>
                        <select name="resident_id" class="form-select">
                            <option value="">General Service / Unassigned</option>
                            @foreach($residents as $r)
                            <option value="{{ $r->id }}" {{ old('resident_id',$serviceLog->resident_id)==$r->id?'selected':'' }}>{{ $r->full_name }} ({{ $r->address }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Date of Service *</label>
                        <input type="date" name="date_of_service" class="form-control" value="{{ old('date_of_service', optional($serviceLog->date_of_service)->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Assigned Staff / Officer</label>
                        <select name="assigned_to" class="form-select">
                            <option value="">Unassigned</option>
                            @foreach($staff as $s)
                            <option value="{{ $s->id }}" {{ old('assigned_to',$serviceLog->assigned_to)==$s->id?'selected':'' }}>{{ $s->name }} ({{ ucfirst($s->role) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description / Narrative *</label>
                        <textarea name="description" class="form-control" rows="3" required>{{ old('description', $serviceLog->description) }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Staff Remarks / Notes</label>
                        <textarea name="remarks" class="form-control" rows="2">{{ old('remarks', $serviceLog->remarks) }}</textarea>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-4 pt-2 border-top">
                    <button type="submit" class="btn btn-navy"><i class="ti ti-check me-1"></i>Save Changes</button>
                    <a href="{{ route('admin.service-logs.show', $serviceLog) }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
