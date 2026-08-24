@extends('layouts.admin')
@section('title','Service Logs')
@section('page-title','Service Logs')
@section('content')
<div class="d-flex justify-content-between align-items-center mt-2 mb-3 flex-wrap gap-2">
    <form method="GET" class="d-flex gap-2 flex-wrap">
        <select name="type" class="form-select form-select-sm" style="width:160px">
            <option value="">All types</option>
            @foreach(\App\Models\ServiceLog::TYPES as $t)<option value="{{ $t }}" {{ request('type')==$t?'selected':'' }}>{{ ucwords(str_replace('_',' ',$t)) }}</option>@endforeach
        </select>
        <select name="status" class="form-select form-select-sm" style="width:140px">
            <option value="">All statuses</option>
            @foreach(\App\Models\ServiceLog::STATUSES as $s)<option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>@endforeach
        </select>
        <button type="submit" class="btn btn-navy btn-sm">Filter</button>
    </form>
    <a href="{{ route('admin.service-logs.create') }}" class="btn btn-navy btn-sm"><i class="ti ti-plus me-1"></i>Create log</button></a>
</div>
<div class="table-responsive-custom">
    <table class="table-custom">
        <thead><tr><th>Log ID</th><th>Service type</th><th>Resident / Party</th><th>Date</th><th>Assigned to</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td style="font-family:monospace;font-size:12px">{{ $log->log_number }}</td>
                <td>{{ ucwords(str_replace('_',' ',$log->service_type)) }}</td>
                <td>{{ optional($log->resident)->full_name ?? '—' }}</td>
                <td>{{ $log->date_of_service->format('M d, Y') }}</td>
                <td>{{ optional($log->assignedTo)->name ?? '—' }}</td>
                <td><span class="badge-status badge-{{ str_replace(' ','_',$log->status) }}">{{ ucwords(str_replace('_',' ',$log->status)) }}</span></td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.service-logs.show',$log) }}" class="btn btn-sm btn-outline-secondary py-0 px-2"><i class="ti ti-eye"></i></a>
                        <a href="{{ route('admin.service-logs.edit',$log) }}" class="btn btn-sm btn-outline-navy py-0 px-2"><i class="ti ti-pencil"></i></a>
                    </div>
                </td>
            </tr>
            @empty<tr><td colspan="7" class="text-center py-4 text-muted">No service logs found.</td></tr>@endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $logs->links() }}</div>
@endsection
