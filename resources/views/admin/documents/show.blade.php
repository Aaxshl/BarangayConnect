@extends('layouts.admin')
@section('title','Document')
@section('page-title','Document Details')
@section('content')
<div class="mb-3">
    <a href="{{ route('admin.documents.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back to Documents
    </a>
</div>
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card-custom">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
                <div>
                    <div style="font-family:monospace;font-size:14px;font-weight:700;color:#185fa5">{{ $document->document_number }}</div>
                    <h5 class="mb-1 mt-1">{{ \App\Models\Document::TYPES[$document->document_type] ?? $document->document_type }}</h5>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge-status badge-{{ $document->status }}">{{ ucwords(str_replace('_',' ',$document->status)) }}</span>
                    <a href="{{ route('admin.documents.print',$document) }}" class="btn btn-navy btn-sm" target="_blank"><i class="ti ti-printer me-1"></i>Print</a>
                </div>
            </div>
            <div class="row g-3" style="font-size:13.5px">
                <div class="col-md-6"><span class="text-muted">Resident</span><div class="fw-medium">{{ optional($document->resident)->full_name }}</div></div>
                <div class="col-md-6"><span class="text-muted">Purpose</span><div>{{ $document->purpose }}</div></div>
                <div class="col-md-6"><span class="text-muted">Issued</span><div>{{ $document->issue_date->format('M d, Y') }}</div></div>
                <div class="col-md-6"><span class="text-muted">Copies</span><div>{{ $document->number_of_copies }}</div></div>
                <div class="col-md-6"><span class="text-muted">Issued by</span><div>{{ optional($document->issuedBy)->name ?: '—' }}</div></div>
                @if($document->remarks)<div class="col-12"><span class="text-muted">Remarks</span><div>{{ $document->remarks }}</div></div>@endif
            </div>
            <form method="POST" action="{{ route('admin.documents.update',$document) }}" class="mt-4 pt-3 border-top">
                @csrf @method('PUT')
                <div class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label">Update status</label>
                        <select name="status" class="form-select form-select-sm">
                            @foreach(\App\Models\Document::STATUSES as $s)
                            <option value="{{ $s }}" {{ $document->status==$s?'selected':'' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-navy btn-sm">Update status</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
