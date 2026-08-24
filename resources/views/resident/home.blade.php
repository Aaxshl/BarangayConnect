<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartBarangay — Resident Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
</head>
<body style="padding-bottom:0">
<nav class="portal-nav">
    <div class="container px-3 px-md-4">
        <div class="d-flex align-items-center justify-content-between" style="height:56px">
            <div class="portal-brand">
                <div class="portal-brand-icon"><i class="ti ti-building-community"></i></div>
                <div class="d-none d-sm-block">
                    <div class="portal-brand-name">SmartBarangay</div>
                    <div class="portal-brand-sub">Resident Portal</div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('portal.login') }}" class="btn-portal-login">Login</a>
                <a href="{{ route('portal.register') }}" style="background:rgba(255,255,255,0.28);color:#fff;border:none;border-radius:8px;padding:6px 14px;font-size:13px;text-decoration:none">Register</a>
            </div>
        </div>
    </div>
</nav>

<!-- Hero -->
<div style="background:#1a3a6b;padding:60px 20px;text-align:center;color:#fff">
    <div style="font-size:13px;opacity:0.65;margin-bottom:10px;letter-spacing:0.5px;text-transform:uppercase">Barangay San Jose · San Pedro City, Laguna</div>
    <h1 style="font-size:clamp(28px,5vw,42px);font-weight:700;margin-bottom:12px">Your Barangay, Online</h1>
    <p style="font-size:16px;opacity:0.75;max-width:480px;margin:0 auto 28px;line-height:1.6">Request documents, report community issues, and track your requests — all from your phone.</p>
    <div class="d-flex gap-3 justify-content-center flex-wrap">
        <a href="{{ route('portal.register') }}" style="background:#fff;color:#1a3a6b;border:none;border-radius:10px;padding:12px 24px;font-size:14px;font-weight:600;text-decoration:none">Get started →</a>
        <a href="{{ route('portal.login') }}" style="background:rgba(255,255,255,0.15);color:#fff;border:none;border-radius:10px;padding:12px 24px;font-size:14px;font-weight:500;text-decoration:none">Already registered</a>
    </div>
</div>

<!-- Services -->
<div class="container px-3 px-md-4 py-5">
    <h2 style="font-size:22px;font-weight:700;text-align:center;margin-bottom:6px">Available services</h2>
    <p class="text-muted text-center mb-4" style="font-size:14px">Everything you need from your barangay, now online.</p>
    <div class="row g-3">
        <div class="col-6 col-md-4">
            <div class="portal-card text-center" style="height:100%">
                <i class="ti ti-file-certificate" style="font-size:32px;color:#1a3a6b;display:block;margin-bottom:10px"></i>
                <div style="font-weight:600;margin-bottom:4px">Barangay Clearance</div>
                <div style="font-size:12.5px;color:#888">For employment, travel, and other official requirements</div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="portal-card text-center" style="height:100%">
                <i class="ti ti-home-check" style="font-size:32px;color:#1a3a6b;display:block;margin-bottom:10px"></i>
                <div style="font-weight:600;margin-bottom:4px">Cert. of Residency</div>
                <div style="font-size:12.5px;color:#888">Proof of address for banking and government transactions</div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="portal-card text-center" style="height:100%">
                <i class="ti ti-message-report" style="font-size:32px;color:#1a3a6b;display:block;margin-bottom:10px"></i>
                <div style="font-weight:600;margin-bottom:4px">Report Issues</div>
                <div style="font-size:12.5px;color:#888">Streetlights, drainage, road damage, and more</div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="portal-card text-center" style="height:100%">
                <i class="ti ti-list-search" style="font-size:32px;color:#1a3a6b;display:block;margin-bottom:10px"></i>
                <div style="font-weight:600;margin-bottom:4px">Track Requests</div>
                <div style="font-size:12.5px;color:#888">Real-time status updates on all your submissions</div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="portal-card text-center" style="height:100%">
                <i class="ti ti-speakerphone" style="font-size:32px;color:#1a3a6b;display:block;margin-bottom:10px"></i>
                <div style="font-weight:600;margin-bottom:4px">Announcements</div>
                <div style="font-size:12.5px;color:#888">Stay informed with barangay news and advisories</div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="portal-card text-center" style="height:100%">
                <i class="ti ti-heart-handshake" style="font-size:32px;color:#1a3a6b;display:block;margin-bottom:10px"></i>
                <div style="font-weight:600;margin-bottom:4px">Cert. of Indigency</div>
                <div style="font-size:12.5px;color:#888">For government assistance and medical programs</div>
            </div>
        </div>
    </div>
</div>

<!-- Announcements -->
@if($announcements->count())
<div style="background:#f4f6fa;padding:40px 0">
    <div class="container px-3 px-md-4">
        <h2 style="font-size:20px;font-weight:700;margin-bottom:20px">Latest announcements</h2>
        <div class="row g-3">
            @foreach($announcements as $ann)
            <div class="col-12 col-md-6">
                <div class="announce-card">
                    <div class="announce-type">{{ ucwords(str_replace('_',' ',$ann->announcement_type)) }}</div>
                    <div class="announce-title">{{ $ann->title }}</div>
                    <p style="font-size:13px;color:#555;margin:5px 0 8px;line-height:1.5">{{ Str::limit($ann->body,120) }}</p>
                    <div class="announce-date">{{ $ann->published_at->format('M d, Y') }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<footer style="background:#1a3a6b;color:rgba(255,255,255,0.65);text-align:center;padding:20px;font-size:12.5px">
    © {{ date('Y') }} SmartBarangay — Barangay San Jose, San Pedro City, Laguna · <a href="{{ route('login') }}" style="color:rgba(255,255,255,0.5)">Staff login</a>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
