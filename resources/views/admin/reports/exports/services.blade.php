@extends('admin.reports.exports.layout')

@section('title', 'Service Logs & Blotter Summary Report')
@section('report-heading', 'Barangay Service Logs & Blotter Case Records')

@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th style="width: 14%;">Log ID</th>
            <th style="width: 18%;">Service Type</th>
            <th style="width: 20%;">Resident / Party</th>
            <th style="width: 12%;">Service Date</th>
            <th style="width: 18%;">Assigned Staff</th>
            <th style="width: 13%;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $i => $log)
        <tr>
            <td style="text-align:center;">{{ $i + 1 }}</td>
            <td style="font-family:monospace;font-weight:bold;">{{ $log->log_number }}</td>
            <td>{{ ucwords(str_replace('_',' ',$log->service_type)) }}</td>
            <td>{{ optional($log->resident)->full_name ?? 'General Service' }}</td>
            <td>{{ optional($log->date_of_service)->format('M d, Y') }}</td>
            <td>{{ optional($log->assignedTo)->name ?? 'Unassigned' }}</td>
            <td>
                <span class="badge badge-{{ $log->status }}">
                    {{ ucwords(str_replace('_',' ',$log->status)) }}
                </span>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align:center; padding: 15px; color: #94a3b8;">No service logs found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
