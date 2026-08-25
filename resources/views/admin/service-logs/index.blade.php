@extends('layouts.admin')
@section('title','Service Logs')
@section('page-title','Service Logs')
@section('content')

{{-- KPI Stats --}}
<div class="row g-3 mt-1 mb-3">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label">Pending / Recorded</div>
            <div class="stat-value">{{ \App\Models\ServiceLog::where('status','pending')->count() }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label">Assigned / Scheduled</div>
            <div class="stat-value">{{ \App\Models\ServiceLog::where('status','assigned')->count() }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label">In Progress</div>
            <div class="stat-value">{{ \App\Models\ServiceLog::where('status','in_progress')->count() }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label">Resolved This Month</div>
            <div class="stat-value">{{ \App\Models\ServiceLog::whereIn('status',['resolved','closed'])->whereMonth('updated_at',now()->month)->count() }}</div>
        </div>
    </div>
</div>

{{-- Filters & Actions --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <form method="GET" class="d-flex gap-2 flex-wrap" id="filterForm">
        <input type="text" name="search" class="form-control form-control-sm"
            placeholder="Search log or resident..." value="{{ request('search') }}"
            style="width:190px" onchange="this.form.submit()">
        <select name="type" class="form-select form-select-sm" style="width:170px" onchange="this.form.submit()">
            <option value="">All service types</option>
            @foreach(\App\Models\ServiceLog::TYPES as $t)
                <option value="{{ $t }}" {{ request('type') == $t ? 'selected' : '' }}>
                    {{ ucwords(str_replace('_',' ',$t)) }}
                </option>
            @endforeach
        </select>
        <select name="status" class="form-select form-select-sm" style="width:160px" onchange="this.form.submit()">
            <option value="">Active logs only</option>
            @foreach(\App\Models\ServiceLog::STATUSES as $s)
                <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>
                    {{ ucwords(str_replace('_',' ',$s)) }}
                </option>
            @endforeach
        </select>
    </form>
    <a href="{{ route('admin.service-logs.create') }}" class="btn btn-navy btn-sm">
        <i class="ti ti-plus me-1"></i>New Service Log
    </a>
</div>

{{-- Active Service Logs Table --}}
<div class="table-responsive-custom">
    <table class="table-custom">
        <thead>
            <tr>
                <th>Log ID</th>
                <th>Service Type</th>
                <th>Resident / Party</th>
                <th>Service Date</th>
                <th>Assigned Staff</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td style="font-family:monospace;font-size:12px;color:#185fa5;font-weight:600">{{ $log->log_number }}</td>
                <td style="font-size:13px;font-weight:500">{{ ucwords(str_replace('_',' ',$log->service_type)) }}</td>
                <td style="font-size:13px">
                    @if($log->resident)
                        <a href="{{ route('admin.residents.show', $log->resident_id) }}" style="color:#1a3a6b">
                            {{ $log->resident->full_name }}
                        </a>
                    @else
                        <span class="text-muted">General Service</span>
                    @endif
                </td>
                <td style="font-size:12.5px;color:#64748b">{{ optional($log->date_of_service)->format('M d, Y') }}</td>
                <td style="font-size:13px">
                    @if($log->assignedTo)
                        <i class="ti ti-user-check text-primary me-1"></i>{{ $log->assignedTo->name }}
                    @else
                        <span class="text-muted small">Unassigned</span>
                    @endif
                </td>
                <td>
                    <span class="badge-status badge-{{ $log->status }}">
                        {{ ucwords(str_replace('_',' ',$log->status)) }}
                    </span>
                </td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.service-logs.show', $log) }}" class="btn btn-sm btn-outline-secondary py-0 px-2" title="View">
                            <i class="ti ti-eye"></i>
                        </a>
                        <a href="{{ route('admin.service-logs.edit', $log) }}" class="btn btn-sm btn-outline-navy py-0 px-2" title="Edit">
                            <i class="ti ti-pencil"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-5 text-muted">
                    <i class="ti ti-notes-off" style="font-size:32px;display:block;margin-bottom:8px"></i>
                    No active service logs found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $logs->links() }}</div>

{{-- Completed / Closed / Cancelled Logs (Collapsible) --}}
@if(isset($completedLogs) && $completedLogs->count() > 0)
<div class="mt-5">
    <button class="btn btn-outline-secondary w-100 py-2 text-start" type="button"
        data-bs-toggle="collapse" data-bs-target="#completedLogs" style="font-size:13px;font-weight:500">
        <i class="ti ti-chevron-down me-2"></i>
        Resolved, Closed &amp; Cancelled Logs
        <span class="badge bg-secondary ms-1">{{ $completedLogs->count() }}</span>
    </button>
    <div class="collapse mt-2" id="completedLogs">
        <div class="table-responsive-custom">
            <table class="table-custom" style="opacity:0.85">
                <thead>
                    <tr>
                        <th>Log ID</th>
                        <th>Service Type</th>
                        <th>Resident / Party</th>
                        <th>Status</th>
                        <th>Completed Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($completedLogs as $log)
                    <tr>
                        <td style="font-family:monospace;font-size:12px">{{ $log->log_number }}</td>
                        <td style="font-size:13px">{{ ucwords(str_replace('_',' ',$log->service_type)) }}</td>
                        <td style="font-size:13px">{{ optional($log->resident)->full_name ?: 'General Service' }}</td>
                        <td>
                            <span class="badge-status badge-{{ $log->status }}">
                                {{ ucwords(str_replace('_',' ',$log->status)) }}
                            </span>
                        </td>
                        <td style="font-size:12px;color:#64748b">
                            {{ $log->resolved_at ? $log->resolved_at->format('M d, Y') : ($log->closed_at ? $log->closed_at->format('M d, Y') : $log->updated_at->format('M d, Y')) }}
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.service-logs.show', $log) }}" class="btn btn-sm btn-outline-secondary py-0 px-2">
                                    <i class="ti ti-eye"></i>
                                </a>
                                <a href="{{ route('admin.service-logs.edit', $log) }}" class="btn btn-sm btn-outline-navy py-0 px-2">
                                    <i class="ti ti-pencil"></i>
                                </a>
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
