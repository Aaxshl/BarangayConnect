@extends('admin.reports.exports.layout')

@section('title', 'Citizen Reports & Requests Summary')
@section('report-heading', 'Citizen Reports & Community Complaints Log')

@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th style="width: 15%;">Tracking No.</th>
            <th style="width: 18%;">Issue Type</th>
            <th style="width: 18%;">Resident / Complainant</th>
            <th style="width: 18%;">Location</th>
            <th style="width: 14%;">Assigned Staff</th>
            <th style="width: 12%;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $i => $req)
        <tr>
            <td style="text-align:center;">{{ $i + 1 }}</td>
            <td style="font-family:monospace;font-weight:bold;">{{ $req->tracking_number }}</td>
            <td>{{ ucwords(str_replace('_',' ',$req->request_type)) }}</td>
            <td>{{ optional($req->resident)->full_name ?? 'Anonymous / Walk-in' }}</td>
            <td>{{ $req->location }}</td>
            <td>{{ optional($req->assignedTo)->name ?? 'Unassigned' }}</td>
            <td>
                <span class="badge badge-{{ $req->status }}">
                    {{ ucwords(str_replace('_',' ',$req->status)) }}
                </span>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align:center; padding: 15px; color: #94a3b8;">No citizen requests found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
