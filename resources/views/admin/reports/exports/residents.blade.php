@extends('admin.reports.exports.layout')

@section('title', 'Masterlist of Registered Residents')
@section('report-heading', 'Masterlist of Registered Barangay Residents')

@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th style="width: 25%;">Full Name</th>
            <th style="width: 8%;">Age / Sex</th>
            <th style="width: 12%;">Civil Status</th>
            <th style="width: 26%;">Address / Purok</th>
            <th style="width: 14%;">Contact</th>
            <th style="width: 10%;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $i => $resident)
        <tr>
            <td style="text-align:center;">{{ $i + 1 }}</td>
            <td>
                <strong>{{ $resident->last_name }}, {{ $resident->first_name }} {{ $resident->middle_name }}</strong>
            </td>
            <td>{{ $resident->age ?? '—' }} / {{ ucfirst(substr($resident->gender ?? '—', 0, 1)) }}</td>
            <td>{{ ucfirst($resident->civil_status ?? '—') }}</td>
            <td>
                {{ $resident->address }}
                @if($resident->purok) ({{ $resident->purok }}) @endif
            </td>
            <td>{{ $resident->contact_number ?: '—' }}</td>
            <td>
                <span class="badge badge-{{ $resident->status }}">{{ ucfirst($resident->status) }}</span>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align:center; padding: 15px; color: #94a3b8;">No resident records found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
