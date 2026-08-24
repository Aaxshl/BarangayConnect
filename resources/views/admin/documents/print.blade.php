<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $document->document_number }} — {{ $template->title }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11pt; margin: 30px; line-height: 1.6; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .header-table td { vertical-align: middle; }
        .logo-td { width: 90px; text-align: center; }
        .logo-img { max-height: 80px; max-width: 80px; }
        .header-text { text-align: center; white-space: pre-line; line-height: 1.4; font-size: 11pt; }
        .header-text h2 { font-size: 16pt; margin: 4px 0 0; text-transform: uppercase; }
        .divider { border-top: 2px solid #000; margin: 15px 0 25px; }
        .doc-title { text-align: center; text-transform: uppercase; font-size: 16pt; letter-spacing: 2px; margin: 20px 0; font-weight: bold; }
        .doc-body { white-space: pre-wrap; font-size: 11.5pt; text-align: justify; margin-bottom: 25px; }
        .details-table { width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 10.5pt; }
        .details-table td { padding: 4px 8px; }
        .label { font-weight: bold; width: 30%; }
        .footer-sig { margin-top: 50px; width: 100%; border-collapse: collapse; }
        .sig-box { text-align: center; width: 220px; float: right; }
        .sig-line { border-top: 1px solid #000; margin-top: 50px; margin-bottom: 4px; }
        .footer-note { margin-top: 40px; font-size: 9.5pt; color: #666; text-align: center; border-top: 1px dashed #ccc; padding-top: 8px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <!-- Header Section -->
    <table class="header-table">
        <tr>
            @if($logoUrl)
            <td class="logo-td">
                <img src="{{ $logoUrl }}" class="logo-img" alt="Barangay Logo">
            </td>
            @endif
            <td class="header-text">
                {!! nl2br(e($renderedHeader)) !!}
            </td>
            @if($logoUrl)
            <td class="logo-td">
                <!-- Optional right side seal placeholder -->
            </td>
            @endif
        </tr>
    </table>

    <div class="divider"></div>

    <!-- Document Title -->
    <div class="doc-title">{{ $template->title }}</div>

    <!-- Document Body Content -->
    <div class="doc-body">{!! nl2br(e($renderedBody)) !!}</div>

    <!-- Metadata Details Table -->
    <table class="details-table">
        <tr>
            <td class="label">Control / Document No.:</td>
            <td><strong>{{ $document->document_number }}</strong></td>
        </tr>
        <tr>
            <td class="label">Date Issued:</td>
            <td>{{ $document->issue_date->format('F d, Y') }}</td>
        </tr>
        <tr>
            <td class="label">Valid Until:</td>
            <td>{{ $document->issue_date->copy()->addMonths(6)->format('F d, Y') }}</td>
        </tr>
    </table>

    <!-- Signatory Section -->
    <div style="width: 100%; overflow: hidden; margin-top: 40px;">
        <div class="sig-box">
            <div class="sig-line"></div>
            <div style="font-weight: bold; font-size: 11pt; text-transform: uppercase;">
                {{ $template->signatory_name ?: ($settings['captain_name'] ?? 'Hon. Barangay Captain') }}
            </div>
            <div style="font-size: 10pt; color: #333;">{{ $template->signatory_title ?: 'Barangay Captain' }}</div>
        </div>
    </div>

    <!-- Footer Note -->
    @if($renderedFooter)
    <div class="footer-note">
        {{ $renderedFooter }}
    </div>
    @endif
</body>
</html>
