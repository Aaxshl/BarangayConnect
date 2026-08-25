<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Barangay Report')</title>
    <style>
        @page {
            margin: 25px 25px 35px 25px;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            color: #1e293b;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .logo-td {
            width: 70px;
            text-align: center;
        }
        .logo-img {
            max-height: 60px;
            max-width: 60px;
        }
        .header-text {
            text-align: center;
            line-height: 1.3;
        }
        .header-text .republic {
            font-size: 8.5pt;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-text .brgy-name {
            font-size: 13pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            margin: 2px 0;
        }
        .header-text .sub-text {
            font-size: 8.5pt;
            color: #64748b;
        }
        .divider {
            border-top: 2px solid #185fa5;
            margin: 8px 0 14px;
        }
        .report-title-box {
            background: #f1f5f9;
            border-left: 4px solid #185fa5;
            padding: 8px 12px;
            margin-bottom: 14px;
        }
        .report-title {
            font-size: 12pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            margin: 0;
        }
        .report-meta {
            font-size: 8pt;
            color: #64748b;
            margin-top: 2px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.data-table th {
            background: #185fa5;
            color: #ffffff;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 8px;
            text-align: left;
            border: 1px solid #185fa5;
        }
        table.data-table td {
            padding: 5px 8px;
            font-size: 8pt;
            border: 1px solid #cbd5e1;
            vertical-align: top;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 7.5pt;
            font-weight: bold;
            border-radius: 3px;
            text-transform: uppercase;
        }
        .badge-active, .badge-released, .badge-resolved {
            background: #dcfce7;
            color: #166534;
        }
        .badge-pending, .badge-under_review {
            background: #fef3c7;
            color: #92400e;
        }
        .badge-processing, .badge-assigned, .badge-in_progress, .badge-ready_for_pickup {
            background: #e0e7ff;
            color: #3730a3;
        }
        .badge-cancelled, .badge-inactive, .badge-closed {
            background: #f1f5f9;
            color: #475569;
        }
        .footer-table {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
        }
        .footer-table td {
            vertical-align: top;
            font-size: 8.5pt;
        }
        .sig-box {
            text-align: center;
            width: 200px;
            float: right;
        }
        .sig-line {
            border-top: 1px solid #334155;
            margin-top: 40px;
            margin-bottom: 3px;
        }
        .page-footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7.5pt;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <table class="header-table">
        <tr>
            @php
                $settings = \App\Models\Setting::all()->pluck('value','key');
                $logoUrl = null;
                if (isset($settings['barangay_logo']) && file_exists(public_path('storage/' . $settings['barangay_logo']))) {
                    $logoUrl = public_path('storage/' . $settings['barangay_logo']);
                } elseif (file_exists(public_path('images/logo.png'))) {
                    $logoUrl = public_path('images/logo.png');
                }
            @endphp
            @if($logoUrl)
            <td class="logo-td">
                <img src="{{ $logoUrl }}" class="logo-img" alt="Logo">
            </td>
            @endif
            <td class="header-text">
                <div class="republic">Republic of the Philippines</div>
                <div class="brgy-name">{{ $settings['barangay_name'] ?? 'Barangay San Jose' }}</div>
                <div class="sub-text">{{ $settings['barangay_address'] ?? 'City of San Pedro, Laguna' }}</div>
            </td>
            @if($logoUrl)
            <td class="logo-td">
                <img src="{{ $logoUrl }}" class="logo-img" style="visibility:hidden" alt="">
            </td>
            @endif
        </tr>
    </table>

    <div class="divider"></div>

    {{-- Report Title Banner --}}
    <div class="report-title-box">
        <h1 class="report-title">@yield('report-heading', 'Official Barangay Report')</h1>
        <div class="report-meta">
            Generated on: <strong>{{ now()->format('F d, Y — h:i A') }}</strong> | 
            Total Records: <strong>{{ count($data) }}</strong> | 
            Prepared by: <strong>{{ auth()->user()->name ?? 'System Administrator' }}</strong>
        </div>
    </div>

    {{-- Content Table --}}
    @yield('content')

    {{-- Signatures --}}
    <table class="footer-table">
        <tr>
            <td style="width: 50%;">
                <div style="font-size: 8pt; color:#64748b;">
                    This document is an official system-generated report from <strong>{{ $settings['system_name'] ?? 'BarangayConnect' }}</strong>.
                </div>
            </td>
            <td style="width: 50%;">
                <div class="sig-box">
                    <div class="sig-line"></div>
                    <div style="font-weight: bold; text-transform: uppercase;">
                        {{ $settings['captain_name'] ?? 'Hon. Barangay Captain' }}
                    </div>
                    <div style="font-size: 7.5pt; color: #475569;">Punong Barangay</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="page-footer">
        {{ $settings['barangay_name'] ?? 'Barangay San Jose' }} • {{ $settings['system_name'] ?? 'BarangayConnect' }} Official Report
    </div>
</body>
</html>
