@extends('admin.reports.exports.layout')

@section('title', 'Household Profiling Summary')
@section('report-heading', 'Barangay Household Profiling Summary Report')

@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th style="width: 20%;">Household No.</th>
            <th style="width: 30%;">Head of Household</th>
            <th style="width: 30%;">Address / Purok</th>
            <th style="width: 15%; text-align:center;">Total Members</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $i => $hh)
        <tr>
            <td style="text-align:center;">{{ $i + 1 }}</td>
            <td style="font-family:monospace;font-weight:bold;">{{ $hh->household_number }}</td>
            <td>{{ optional($hh->head)->full_name ?? 'Unassigned' }}</td>
            <td>{{ $hh->address }} @if($hh->purok) ({{ $hh->purok }}) @endif</td>
            <td style="text-align:center;">{{ $hh->members ? $hh->members->count() : 0 }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="5" style="text-align:center; padding: 15px; color: #94a3b8;">No household records found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
