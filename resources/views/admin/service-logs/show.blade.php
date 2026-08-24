@extends('layouts.admin')
@section('title','Service Log Details')
@section('page-title','Service Log Details')
@section('content')
<div class="mb-3">
    <a href="{{ route('admin.service-logs.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back to Service Logs
    </a>
</div>
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card-custom">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
                <div>
                    <div style="font-family:monospace;font-size:14px;font-weight:700;color:#185fa5">{{ $serviceLog->log_number }}</div>
                    <h5 class="mb-1 mt-1">{{ ucwords(str_replace('_',' ',$serviceLog->service_type)) }}</h5>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge-status badge-{{ $serviceLog->status }}">{{ ucwords(str_replace('_',' ',$serviceLog->status)) }}</span>
                    <a href="{{ route('admin.service-logs.edit',$serviceLog) }}" class="btn btn-navy btn-sm"><i class="ti ti-pencil me-1"></i>Edit</a>
                </div>
            </div>
            <div class="row g-3" style="font-size:13.5px">
                <div class="col-md-6"><span class="text-muted">Resident Concerned</span><div class="fw-medium">{{ optional($serviceLog->resident)->full_name ?: 'General / Unassigned' }}</div></div>
                <div class="col-md-6"><span class="text-muted">Date of Service</span><div>{{ optional($serviceLog->date_of_service)->format('M d, Y') }}</div></div>
                <div class="col-md-6"><span class="text-muted">Assigned Staff</span><div>{{ optional($serviceLog->assignedTo)->name ?: 'Unassigned' }}</div></div>
                <div class="col-md-6"><span class="text-muted">Created Date</span><div>{{ $serviceLog->created_at->format('M d, Y g:i A') }}</div></div>
                <div class="col-12"><span class="text-muted">Description</span><div class="p-2 bg-light rounded mt-1">{{ $serviceLog->description }}</div></div>
                @if($serviceLog->remarks)
                <div class="col-12"><span class="text-muted">Remarks</span><div class="p-2 bg-light rounded mt-1">{{ $serviceLog->remarks }}</div></div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
