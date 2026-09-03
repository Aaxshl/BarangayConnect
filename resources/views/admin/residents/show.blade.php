@extends('layouts.admin')
@section('title','Resident Profile')
@section('page-title','Resident Profile')
@section('content')
<div class="mb-3">
    <a href="{{ route('admin.residents.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back to Residents
    </a>
</div>
<div class="row">
    <div class="col-12 col-lg-4 mb-3">
        <div class="card-custom text-center">
            <div style="width:72px;height:72px;border-radius:50%;background:#e6f1fb;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700;color:#185fa5;margin:0 auto 12px">
                {{ substr($resident->first_name,0,1) }}{{ substr($resident->last_name,0,1) }}
            </div>
            <h5 class="mb-0">{{ $resident->full_name }}</h5>
            <div style="font-family:monospace;font-size:12px;color:#888;margin:4px 0">RES-{{ str_pad($resident->id,4,'0',STR_PAD_LEFT) }}</div>
            <span class="badge-status badge-{{ $resident->status }}">{{ ucfirst($resident->status) }}</span>
            <div class="d-flex gap-2 mt-3 justify-content-center flex-wrap">
                @if(auth()->user()->canDo('residents.create_edit'))
                <a href="{{ route('admin.residents.edit',$resident) }}" class="btn btn-navy btn-sm"><i class="ti ti-pencil me-1"></i>Edit</a>
                @endif
                <a href="{{ route('admin.residents.qr',$resident) }}" class="btn btn-outline-navy btn-sm"><i class="ti ti-qrcode me-1"></i>QR Code</a>
                @if(auth()->user()->canDo('documents.create'))
                <a href="{{ route('admin.documents.create') }}?resident={{ $resident->id }}" class="btn btn-outline-navy btn-sm"><i class="ti ti-file-plus me-1"></i>Issue Doc</a>
                @endif
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-8 mb-3">
        <div class="card-custom mb-3">
            <h6 class="fw-semibold mb-3">Personal information</h6>
            <div class="row g-2" style="font-size:13.5px">
                <div class="col-6"><span class="text-muted">Full name</span><div class="fw-medium">{{ $resident->full_name }}</div></div>
                <div class="col-6"><span class="text-muted">Birthdate</span><div class="fw-medium">{{ $resident->birthdate ? $resident->birthdate->format('M d, Y').' (age '.$resident->birthdate->age.')' : '—' }}</div></div>
                <div class="col-6"><span class="text-muted">Gender</span><div>{{ ucfirst($resident->gender) }}</div></div>
                <div class="col-6"><span class="text-muted">Civil status</span><div>{{ ucfirst($resident->civil_status) }}</div></div>
                <div class="col-6"><span class="text-muted">Occupation</span><div>{{ $resident->occupation ?: '—' }}</div></div>
                <div class="col-6"><span class="text-muted">Contact</span><div>{{ $resident->contact_number ?: '—' }}</div></div>
                <div class="col-12"><span class="text-muted">Address</span><div>{{ $resident->address }}{{ $resident->purok ? ', '.$resident->purok : '' }}{{ $resident->zone ? ', '.$resident->zone : '' }}</div></div>
                <div class="col-6"><span class="text-muted">Household</span><div>{{ optional($resident->household)->household_id ?: '—' }}</div></div>
                <div class="col-6"><span class="text-muted">Registered</span><div>{{ $resident->created_at->format('M d, Y') }}</div></div>
            </div>
        </div>
        <div class="card-custom">
            <h6 class="fw-semibold mb-3">Document history</h6>
            @forelse($resident->documents->take(5) as $doc)
            <div class="d-flex justify-content-between py-2 border-bottom border-light" style="font-size:13.5px">
                <div>
                    <span style="font-family:monospace;font-size:11px;color:#185fa5">{{ $doc->document_number }}</span>
                    <div>{{ \App\Models\Document::TYPES[$doc->document_type] ?? $doc->document_type }}</div>
                    <small class="text-muted">{{ $doc->issue_date->format('M d, Y') }}</small>
                </div>
                <span class="badge-status badge-{{ $doc->status }}" style="align-self:center">{{ ucwords(str_replace('_',' ',$doc->status)) }}</span>
            </div>
            @empty<p class="text-muted small">No documents on record.</p>@endforelse
        </div>
    </div>
</div>
@endsection
