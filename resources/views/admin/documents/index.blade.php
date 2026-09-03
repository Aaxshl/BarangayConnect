@extends('layouts.admin')
@section('title','Documents')
@section('page-title','Documents')
@section('content')

{{-- KPI Stats --}}
<div class="row g-3 mt-1 mb-3">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label">Pending / Under Review</div>
            <div class="stat-value">{{ \App\Models\Document::whereIn('status',['pending','under_review'])->count() }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label">Processing</div>
            <div class="stat-value">{{ \App\Models\Document::where('status','processing')->count() }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label">Ready for Pickup</div>
            <div class="stat-value">{{ \App\Models\Document::where('status','ready_for_pickup')->count() }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label">Released This Month</div>
            <div class="stat-value">{{ \App\Models\Document::where('status','released')->whereMonth('released_at',now()->month)->count() }}</div>
        </div>
    </div>
</div>

{{-- Filters & Actions --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <form method="GET" class="d-flex gap-2 flex-wrap" id="filterForm">
        <input type="text" name="search" class="form-control form-control-sm"
            placeholder="Search resident..." value="{{ request('search') }}"
            style="width:180px" onchange="this.form.submit()">
        <select name="type" class="form-select form-select-sm" style="width:180px" onchange="this.form.submit()">
            <option value="">All document types</option>
            @foreach(\App\Models\Document::TYPES as $k => $v)
                <option value="{{ $k }}" {{ request('type') == $k ? 'selected' : '' }}>{{ $v }}</option>
            @endforeach
        </select>
        <select name="status" class="form-select form-select-sm" style="width:175px" onchange="this.form.submit()">
            <option value="">Active requests only</option>
            @foreach(\App\Models\Document::STATUSES as $s)
                <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>
                    {{ ucwords(str_replace('_',' ',$s)) }}
                </option>
            @endforeach
        </select>
    </form>
    <div class="d-flex gap-2">
        @if(auth()->user()->canDo('documents.templates'))
        <a href="{{ route('admin.documents.templates.index') }}" class="btn btn-outline-navy btn-sm">
            <i class="ti ti-template me-1"></i>Templates
        </a>
        @endif
        @if(auth()->user()->canDo('documents.create'))
        <a href="{{ route('admin.documents.create') }}" class="btn btn-navy btn-sm">
            <i class="ti ti-file-plus me-1"></i>Issue Document
        </a>
        @endif
    </div>
</div>

{{-- Active Documents --}}
<div class="table-responsive-custom">
    <table class="table-custom">
        <thead>
            <tr>
                <th>Doc No.</th>
                <th>Resident</th>
                <th>Type</th>
                <th>Purpose</th>
                <th>Requested</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($documents as $doc)
            <tr>
                <td style="font-family:monospace;font-size:12px;color:#185fa5;font-weight:600">{{ $doc->document_number }}</td>
                <td>
                    <a href="{{ route('admin.residents.show',$doc->resident_id) }}" style="color:#1a3a6b;font-weight:500">
                        {{ optional($doc->resident)->full_name }}
                    </a>
                </td>
                <td style="font-size:13px">{{ \App\Models\Document::TYPES[$doc->document_type] ?? $doc->document_type }}</td>
                <td style="font-size:13px">{{ Str::limit($doc->purpose, 35) }}</td>
                <td style="font-size:12px;color:#64748b">{{ $doc->issue_date->format('M d, Y') }}</td>
                <td>
                    <span class="badge-status badge-{{ $doc->status }}">
                        {{ ucwords(str_replace('_',' ',$doc->status)) }}
                    </span>
                </td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.documents.show',$doc) }}"
                            class="btn btn-sm btn-outline-secondary py-0 px-2" title="View">
                            <i class="ti ti-eye"></i>
                        </a>
                        @if(in_array($doc->status, ['processing','ready_for_pickup','released']) && auth()->user()->canDo('documents.print'))
                            <a href="{{ route('admin.documents.print',$doc) }}"
                                class="btn btn-sm btn-outline-navy py-0 px-2" target="_blank" title="Print">
                                <i class="ti ti-printer"></i>
                            </a>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-5 text-muted">
                    <i class="ti ti-file-off" style="font-size:32px;display:block;margin-bottom:8px"></i>
                    No active document requests found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $documents->links() }}</div>

{{-- Completed / Cancelled Documents — Collapsible --}}
@if(isset($completedDocuments) && $completedDocuments->count() > 0)
<div class="mt-5">
    <button class="btn btn-outline-secondary w-100 py-2 text-start" type="button"
        data-bs-toggle="collapse" data-bs-target="#completedDocs" style="font-size:13px;font-weight:500">
        <i class="ti ti-chevron-down me-2"></i>
        Completed &amp; Cancelled Documents
        <span class="badge bg-secondary ms-1">{{ $completedDocuments->count() }}</span>
    </button>
    <div class="collapse mt-2" id="completedDocs">
        <div class="table-responsive-custom">
            <table class="table-custom" style="opacity:0.8">
                <thead>
                    <tr>
                        <th>Doc No.</th>
                        <th>Resident</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Completed</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($completedDocuments as $doc)
                    <tr>
                        <td style="font-family:monospace;font-size:12px">{{ $doc->document_number }}</td>
                        <td style="font-size:13px">{{ optional($doc->resident)->full_name }}</td>
                        <td style="font-size:13px">{{ \App\Models\Document::TYPES[$doc->document_type] ?? $doc->document_type }}</td>
                        <td>
                            <span class="badge-status badge-{{ $doc->status }}">
                                {{ ucwords(str_replace('_',' ',$doc->status)) }}
                            </span>
                        </td>
                        <td style="font-size:12px;color:#64748b">
                            {{ $doc->released_at
                                ? $doc->released_at->format('M d, Y')
                                : $doc->updated_at->format('M d, Y') }}
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.documents.show',$doc) }}"
                                    class="btn btn-sm btn-outline-secondary py-0 px-2">
                                    <i class="ti ti-eye"></i>
                                </a>
                                @if($doc->status === 'released' && auth()->user()->canDo('documents.print'))
                                    <a href="{{ route('admin.documents.print',$doc) }}"
                                        class="btn btn-sm btn-outline-navy py-0 px-2" target="_blank">
                                        <i class="ti ti-printer"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@endsection
