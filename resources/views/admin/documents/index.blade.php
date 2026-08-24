@extends('layouts.admin')
@section('title','Documents')
@section('page-title','Documents')
@section('content')
<div class="row g-3 mt-1 mb-3">
    <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-label">Issued today</div><div class="stat-value">{{ \App\Models\Document::whereDate('created_at',today())->count() }}</div></div></div>
    <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-label">Pending pickup</div><div class="stat-value">{{ \App\Models\Document::where('status','pending_pickup')->count() }}</div></div></div>
    <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-label">This month</div><div class="stat-value">{{ \App\Models\Document::whereMonth('created_at',now()->month)->count() }}</div></div></div>
    <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-label">Total all time</div><div class="stat-value">{{ \App\Models\Document::count() }}</div></div></div>
</div>
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <form method="GET" class="d-flex gap-2 flex-wrap">
        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search resident..." value="{{ request('search') }}" style="width:180px">
        <select name="type" class="form-select form-select-sm" style="width:180px">
            <option value="">All types</option>
            @foreach(\App\Models\Document::TYPES as $k => $v)
                <option value="{{ $k }}" {{ request('type')==$k?'selected':'' }}>{{ $v }}</option>
            @endforeach
        </select>
        <select name="status" class="form-select form-select-sm" style="width:150px">
            <option value="">All statuses</option>
            @foreach(\App\Models\Document::STATUSES as $s)
                <option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-navy btn-sm">Filter</button>
    </form>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.documents.templates.index') }}" class="btn btn-outline-navy btn-sm"><i class="ti ti-template me-1"></i>Document Templates</a>
        <a href="{{ route('admin.documents.create') }}" class="btn btn-navy btn-sm"><i class="ti ti-file-plus me-1"></i>Issue Document</a>
    </div>
</div>
<div class="table-responsive-custom">
    <table class="table-custom">
        <thead><tr><th>Doc No.</th><th>Resident</th><th>Type</th><th>Purpose</th><th>Issued</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
            @forelse($documents as $doc)
            <tr>
                <td style="font-family:monospace;font-size:12px">{{ $doc->document_number }}</td>
                <td><a href="{{ route('admin.residents.show',$doc->resident_id) }}" style="color:#1a3a6b">{{ optional($doc->resident)->full_name }}</a></td>
                <td>{{ \App\Models\Document::TYPES[$doc->document_type] ?? $doc->document_type }}</td>
                <td>{{ Str::limit($doc->purpose, 40) }}</td>
                <td>{{ $doc->issue_date->format('M d, Y') }}</td>
                <td><span class="badge-status badge-{{ $doc->status }}">{{ ucwords(str_replace('_',' ',$doc->status)) }}</span></td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.documents.show',$doc) }}" class="btn btn-sm btn-outline-secondary py-0 px-2"><i class="ti ti-eye"></i></a>
                        <a href="{{ route('admin.documents.print',$doc) }}" class="btn btn-sm btn-outline-navy py-0 px-2" target="_blank"><i class="ti ti-printer"></i></a>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center py-4 text-muted">No documents found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $documents->links() }}</div>
@endsection
