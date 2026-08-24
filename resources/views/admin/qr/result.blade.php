@extends('layouts.admin')
@section('title','QR Result')
@section('page-title','Verification Result')
@section('content')
<div class="row mt-2 justify-content-center">
    <div class="col-12 col-lg-7">
        <div class="card-custom">
            <div class="d-flex align-items-center gap-3 mb-4 p-3 rounded-3" style="background:#e6f1fb">
                <div style="width:52px;height:52px;border-radius:50%;background:#185fa5;display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px;font-weight:700">
                    {{ substr($resident->first_name,0,1) }}{{ substr($resident->last_name,0,1) }}
                </div>
                <div>
                    <h5 class="mb-0">{{ $resident->full_name }}</h5>
                    <small style="font-family:monospace;color:#185fa5">RES-{{ str_pad($resident->id,4,'0',STR_PAD_LEFT) }}</small>
                    <span class="badge-status badge-{{ $resident->status }} ms-2">{{ ucfirst($resident->status) }}</span>
                </div>
                <div class="ms-auto">
                    <i class="ti ti-circle-check" style="font-size:36px;color:#3b6d11"></i>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <h6 class="fw-semibold mb-2">Personal details</h6>
                    <table style="font-size:13.5px;width:100%">
                        <tr><td class="text-muted" style="width:40%;padding:4px 0">Age</td><td>{{ $resident->birthdate ? $resident->birthdate->age : '—' }}</td></tr>
                        <tr><td class="text-muted" style="padding:4px 0">Gender</td><td>{{ ucfirst($resident->gender) }}</td></tr>
                        <tr><td class="text-muted" style="padding:4px 0">Civil status</td><td>{{ ucfirst($resident->civil_status) }}</td></tr>
                        <tr><td class="text-muted" style="padding:4px 0">Address</td><td>{{ $resident->address }}</td></tr>
                        <tr><td class="text-muted" style="padding:4px 0">Contact</td><td>{{ $resident->contact_number ?: '—' }}</td></tr>
                        <tr><td class="text-muted" style="padding:4px 0">Household</td><td>{{ optional($resident->household)->household_id ?: '—' }}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-semibold mb-2">Last 5 documents</h6>
                    @forelse($resident->documents as $doc)
                    <div class="d-flex justify-content-between py-2 border-bottom border-light" style="font-size:12.5px">
                        <div>
                            <div style="font-family:monospace;font-size:11px;color:#185fa5">{{ $doc->document_number }}</div>
                            <div>{{ \App\Models\Document::TYPES[$doc->document_type] ?? $doc->document_type }}</div>
                        </div>
                        <span class="badge-status badge-{{ $doc->status }}" style="align-self:center">{{ ucwords(str_replace('_',' ',$doc->status)) }}</span>
                    </div>
                    @empty<p class="text-muted small">No documents on record.</p>@endforelse
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <a href="{{ route('admin.residents.show',$resident) }}" class="btn btn-navy btn-sm"><i class="ti ti-eye me-1"></i>Full profile</a>
                <a href="{{ route('admin.documents.create') }}?resident={{ $resident->id }}" class="btn btn-outline-navy btn-sm"><i class="ti ti-file-plus me-1"></i>Issue document</a>
                <a href="{{ route('admin.qr.index') }}" class="btn btn-outline-secondary btn-sm ms-auto">Verify another</a>
            </div>
        </div>
    </div>
</div>
@endsection
