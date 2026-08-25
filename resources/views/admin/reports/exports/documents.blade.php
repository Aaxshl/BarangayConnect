@extends('admin.reports.exports.layout')

@section('title', 'Document Issuance Report')
@section('report-heading', 'Official Document Issuance Summary Report')

@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th style="width: 15%;">Control No.</th>
            <th style="width: 22%;">Document Type</th>
            <th style="width: 20%;">Resident Name</th>
            <th style="width: 18%;">Purpose</th>
            <th style="width: 10%;">Date Issued</th>
            <th style="width: 10%;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $i => $doc)
        <tr>
            <td style="text-align:center;">{{ $i + 1 }}</td>
            <td style="font-family:monospace;font-weight:bold;">{{ $doc->document_number }}</td>
            <td>{{ \App\Models\Document::TYPES[$doc->document_type] ?? $doc->document_type }}</td>
            <td>{{ optional($doc->resident)->full_name ?? '—' }}</td>
            <td>{{ \Illuminate\Support\Str::limit($doc->purpose, 30) }}</td>
            <td>{{ optional($doc->issue_date)->format('M d, Y') }}</td>
            <td>
                <span class="badge badge-{{ $doc->status }}">
                    {{ ucwords(str_replace('_',' ',$doc->status)) }}
                </span>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align:center; padding: 15px; color: #94a3b8;">No document records found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
