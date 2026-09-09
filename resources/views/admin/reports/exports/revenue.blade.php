@extends('admin.reports.exports.layout')
@section('title', 'Revenue & Financial Report')
@section('report-heading', 'Revenue & Financial Collection Report')

@section('content')
@php
    $settings = \App\Models\Setting::all()->pluck('value','key');
@endphp

{{-- Summary Box --}}
<table style="width:100%;border-collapse:collapse;margin-bottom:15px;">
    <tr>
        <td style="width:25%;padding:8px;background:#f0fdf4;border:1px solid #bbf7d0;text-align:center;">
            <div style="font-size:7.5pt;color:#166534;text-transform:uppercase;font-weight:bold;">Total Revenue</div>
            <div style="font-size:14pt;font-weight:bold;color:#166534;">₱{{ number_format($summary['total'], 2) }}</div>
        </td>
        <td style="width:25%;padding:8px;background:#eff6ff;border:1px solid #bfdbfe;text-align:center;">
            <div style="font-size:7.5pt;color:#1e40af;text-transform:uppercase;font-weight:bold;">Cash Payments</div>
            <div style="font-size:12pt;font-weight:bold;color:#1e40af;">₱{{ number_format($summary['cash'], 2) }}</div>
        </td>
        <td style="width:25%;padding:8px;background:#f0f9ff;border:1px solid #bae6fd;text-align:center;">
            <div style="font-size:7.5pt;color:#0369a1;text-transform:uppercase;font-weight:bold;">GCash Payments</div>
            <div style="font-size:12pt;font-weight:bold;color:#0369a1;">₱{{ number_format($summary['gcash'], 2) }}</div>
        </td>
        <td style="width:25%;padding:8px;background:#fefce8;border:1px solid #fef08a;text-align:center;">
            <div style="font-size:7.5pt;color:#854d0e;text-transform:uppercase;font-weight:bold;">Waived Fees</div>
            <div style="font-size:12pt;font-weight:bold;color:#854d0e;">₱{{ number_format($summary['waived'], 2) }}</div>
        </td>
    </tr>
</table>

@if($summary['date_from'] || $summary['date_to'])
<div style="font-size:8pt;color:#64748b;margin-bottom:10px;">
    <strong>Period:</strong>
    {{ $summary['date_from'] ? \Carbon\Carbon::parse($summary['date_from'])->format('F d, Y') : 'Beginning' }}
    —
    {{ $summary['date_to'] ? \Carbon\Carbon::parse($summary['date_to'])->format('F d, Y') : 'Present' }}
</div>
@endif

{{-- Detailed Transactions Table --}}
<table class="data-table">
    <thead>
        <tr>
            <th style="width:4%">#</th>
            <th style="width:12%">Document No.</th>
            <th style="width:14%">Type</th>
            <th style="width:16%">Resident</th>
            <th style="width:9%;text-align:right">Fee (₱)</th>
            <th style="width:10%">Method</th>
            <th style="width:9%">Status</th>
            <th style="width:10%">Reference</th>
            <th style="width:8%">Requested</th>
            <th style="width:8%">Released</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $i => $doc)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td style="font-family:monospace;font-size:7.5pt">{{ $doc->document_number }}</td>
            <td>{{ \App\Models\Document::TYPES[$doc->document_type] ?? $doc->document_type }}</td>
            <td>{{ optional($doc->resident)->full_name ?? '—' }}</td>
            <td style="text-align:right;font-weight:bold">{{ number_format($doc->fee, 2) }}</td>
            <td>
                @if($doc->payment_method === 'cash')
                    <span class="badge" style="background:#dbeafe;color:#1e40af">CASH</span>
                @elseif($doc->payment_method === 'gcash')
                    <span class="badge" style="background:#e0f2fe;color:#0369a1">GCASH</span>
                @else
                    {{ ucfirst($doc->payment_method) }}
                @endif
            </td>
            <td>
                @if($doc->payment_status === 'verified')
                    <span class="badge badge-released">VERIFIED</span>
                @elseif($doc->payment_status === 'waived')
                    <span class="badge" style="background:#ecfdf5;color:#065f46">WAIVED</span>
                @endif
            </td>
            <td style="font-family:monospace;font-size:7pt">{{ $doc->payment_reference ?: '—' }}</td>
            <td>{{ optional($doc->issue_date)->format('m/d/Y') }}</td>
            <td>{{ optional($doc->released_at)->format('m/d/Y') ?: '—' }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background:#f1f5f9;font-weight:bold;border-top:2px solid #185fa5">
            <td colspan="4" style="text-align:right;padding:6px 8px;font-size:9pt">GRAND TOTAL:</td>
            <td style="text-align:right;padding:6px 8px;font-size:9pt;color:#166534">₱{{ number_format($summary['total'], 2) }}</td>
            <td colspan="5"></td>
        </tr>
    </tfoot>
</table>

{{-- Certification Note --}}
<div style="margin-top:15px;padding:8px 12px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:4px;font-size:8pt;color:#475569;">
    <strong>Certification:</strong> This financial report is an official system-generated summary of all verified and waived payment transactions
    recorded through the {{ $settings['system_name'] ?? 'BarangayConnect' }} document management system. All amounts are in Philippine Peso (₱).
</div>
@endsection
