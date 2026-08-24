<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings['system_name'] ?? 'BarangayConnect' }} — {{ $settings['barangay_name'] ?? 'Resident Portal' }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
    <style>
        :root {
            --brand-navy: #1a3a6b;
            --brand-blue: #185fa5;
        }
        /* Navbar */
        .portal-nav {
            background: #fff;
            border-bottom: 1px solid #e8eef4;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .portal-brand-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--brand-navy), var(--brand-blue));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 20px;
        }
        .portal-brand-name {
            font-weight: 800;
            font-size: 15px;
            color: var(--brand-navy);
            line-height: 1.1;
        }
        .portal-brand-sub {
            font-size: 11.5px;
            color: #64748b;
        }

        /* Hero */
        .portal-hero {
            background: linear-gradient(135deg, #132a4e 0%, #1a3a6b 60%, #185fa5 100%);
            padding: 56px 20px 48px;
            text-align: center;
            color: #fff;
            position: relative;
        }
        .hero-location-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 16px;
            letter-spacing: 0.3px;
        }
        .hero-title {
            font-size: clamp(26px, 4.5vw, 40px);
            font-weight: 800;
            margin-bottom: 12px;
            line-height: 1.2;
        }
        .hero-desc {
            font-size: 15.5px;
            opacity: 0.85;
            max-width: 540px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* Announcement Section (Bigger & Prominent) */
        .announcement-card-large {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            overflow: hidden;
            transition: transform .2s ease, box-shadow .2s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .announcement-card-large:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(24,95,165,0.1);
        }
        .announcement-img {
            width: 100%;
            height: 190px;
            object-fit: cover;
            background: #f1f5f9;
        }
        .announcement-body {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .announcement-badge {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding: 4px 10px;
            border-radius: 6px;
            background: #e0f2fe;
            color: #0369a1;
            margin-bottom: 10px;
            align-self: flex-start;
        }
        .announcement-badge.event { background: #fef3c7; color: #92400e; }
        .announcement-badge.advisory { background: #fee2e2; color: #991b1b; }
        .announcement-badge.emergency { background: #fecdd3; color: #9f1239; }
        .announcement-title-large {
            font-size: 17px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
            line-height: 1.35;
        }
        .announcement-text-large {
            font-size: 13.5px;
            color: #475569;
            line-height: 1.6;
            margin-bottom: 14px;
            flex-grow: 1;
        }
        .announcement-date {
            font-size: 12px;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 5px;
            padding-top: 12px;
            border-top: 1px solid #f1f5f9;
        }

        /* Available Services (Smaller & Compact) */
        .service-card-compact {
            background: #fff;
            border: 1px solid #e5e9f0;
            border-radius: 12px;
            padding: 16px 14px;
            text-align: center;
            transition: all .2s ease;
            height: 100%;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }
        .service-card-compact:hover {
            border-color: var(--brand-blue);
            box-shadow: 0 4px 14px rgba(24,95,165,0.08);
            transform: translateY(-2px);
        }
        .service-icon-compact {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: #f0f6fc;
            color: var(--brand-blue);
            font-size: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
        }
        .service-title-compact {
            font-size: 13.5px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 3px;
        }
        .service-desc-compact {
            font-size: 11.5px;
            color: #64748b;
            line-height: 1.4;
        }

        /* Footer */
        .portal-footer {
            background: #11223f;
            color: #94a3b8;
            text-align: center;
            padding: 24px 20px;
            font-size: 13px;
            border-top: 1px solid #1e3a5f;
        }
    </style>
</head>
<body style="padding-bottom:0;background:#f8fafc">

<!-- Navbar -->
<nav class="portal-nav">
    <div class="container px-3 px-md-4">
        <div class="d-flex align-items-center justify-content-between" style="height:62px">
            <div class="d-flex align-items-center gap-2">
                <div class="portal-brand-icon">
                    <i class="ti ti-building-community"></i>
                </div>
                <div>
                    <div class="portal-brand-name">{{ $settings['system_name'] ?? 'BarangayConnect' }}</div>
                    <div class="portal-brand-sub">{{ $settings['barangay_name'] ?? 'Barangay San Jose' }}</div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary fw-semibold px-3 py-1.5" style="border-radius:8px;font-size:13px">
                    <i class="ti ti-login me-1"></i>Sign In
                </a>
                <a href="{{ route('portal.register') }}" class="btn btn-sm btn-primary fw-semibold px-3 py-1.5" style="background:var(--brand-navy);border-color:var(--brand-navy);border-radius:8px;font-size:13px">
                    <i class="ti ti-user-plus me-1"></i>Register
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Hero (No Get Started / Sign In Buttons) -->
<div class="portal-hero">
    <div class="hero-location-badge">
        <i class="ti ti-map-pin" style="font-size:14px"></i>
        <span>{{ $settings['barangay_name'] ?? 'Barangay San Jose' }} · {{ $settings['barangay_address'] ?? 'San Pedro City, Laguna' }}</span>
    </div>
    <h1 class="hero-title">Official Citizen Resident Portal</h1>
    <p class="hero-desc">
        Welcome to {{ $settings['barangay_name'] ?? 'Barangay San Jose' }}. Request official certificates, report community incidents with GPS, and track the status of your barangay requests online.
    </p>
</div>

<!-- SECTION 1: LATEST ANNOUNCEMENTS (Bigger & Prominent - Position 1) -->
@if($announcements->count() > 0)
<div class="py-5" style="background:#fff;border-bottom:1px solid #e8eef4">
    <div class="container px-3 px-md-4">
        <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
            <div>
                <div class="text-uppercase fw-bold small text-primary" style="letter-spacing:0.8px">Stay Informed</div>
                <h2 style="font-size:24px;font-weight:800;color:#1e293b;margin:0">Latest Barangay Announcements</h2>
            </div>
            <div class="text-muted small">
                <i class="ti ti-bell-ringing me-1 text-primary"></i>Official Updates & Advisories
            </div>
        </div>

        <div class="row g-4">
            @foreach($announcements as $ann)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="announcement-card-large">
                    @if($ann->image)
                        <img src="{{ asset('storage/'.$ann->image) }}" class="announcement-img" alt="{{ $ann->title }}">
                    @else
                        <div class="announcement-img d-flex align-items-center justify-content-center" style="background:linear-gradient(135deg,#e0f2fe,#dbeafe);color:#0284c7">
                            <i class="ti ti-speakerphone" style="font-size:42px;opacity:0.7"></i>
                        </div>
                    @endif
                    <div class="announcement-body">
                        <span class="announcement-badge {{ strtolower($ann->announcement_type) }}">
                            {{ ucwords(str_replace('_',' ',$ann->announcement_type)) }}
                        </span>
                        <h5 class="announcement-title-large">{{ $ann->title }}</h5>
                        <p class="announcement-text-large">{{ Str::limit($ann->body, 130) }}</p>
                        <div class="announcement-date">
                            <i class="ti ti-calendar" style="font-size:14px"></i>
                            <span>{{ $ann->published_at ? $ann->published_at->format('F d, Y') : $ann->created_at->format('F d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- SECTION 2: AVAILABLE SERVICES (Smaller & Compact - Position 2) -->
<div class="py-5" style="background:#f8fafc">
    <div class="container px-3 px-md-4">
        <div class="text-center mb-4">
            <div class="text-uppercase fw-bold text-muted small" style="letter-spacing:0.8px;font-size:11px">Online Services</div>
            <h3 style="font-size:20px;font-weight:800;color:#1e293b;margin-bottom:4px">Barangay E-Services</h3>
            <p class="text-muted small mb-0">Fast, accessible citizen services at your fingertips.</p>
        </div>

        <div class="row g-3 justify-content-center">
            <!-- 1. Barangay Clearance -->
            <div class="col-6 col-md-4 col-lg-2">
                <div class="service-card-compact">
                    <div class="service-icon-compact">
                        <i class="ti ti-file-certificate"></i>
                    </div>
                    <div class="service-title-compact">Barangay Clearance</div>
                    <div class="service-desc-compact">For jobs, postal, ID & official use</div>
                </div>
            </div>

            <!-- 2. Certificate of Residency -->
            <div class="col-6 col-md-4 col-lg-2">
                <div class="service-card-compact">
                    <div class="service-icon-compact">
                        <i class="ti ti-home-check"></i>
                    </div>
                    <div class="service-title-compact">Residency Cert.</div>
                    <div class="service-desc-compact">Proof of address & residency</div>
                </div>
            </div>

            <!-- 3. Certificate of Indigency -->
            <div class="col-6 col-md-4 col-lg-2">
                <div class="service-card-compact">
                    <div class="service-icon-compact">
                        <i class="ti ti-heart-handshake"></i>
                    </div>
                    <div class="service-title-compact">Cert. of Indigency</div>
                    <div class="service-desc-compact">For medical & scholarship aid</div>
                </div>
            </div>

            <!-- 4. Report Community Issue -->
            <div class="col-6 col-md-4 col-lg-2">
                <div class="service-card-compact">
                    <div class="service-icon-compact" style="background:#fee2e2;color:#dc2626">
                        <i class="ti ti-message-report"></i>
                    </div>
                    <div class="service-title-compact">Incident Report</div>
                    <div class="service-desc-compact">Report damage, lights & waste</div>
                </div>
            </div>

            <!-- 5. Track Status -->
            <div class="col-6 col-md-4 col-lg-2">
                <div class="service-card-compact">
                    <div class="service-icon-compact" style="background:#ecfdf5;color:#059669">
                        <i class="ti ti-list-search"></i>
                    </div>
                    <div class="service-title-compact">Track Request</div>
                    <div class="service-desc-compact">Real-time status tracking</div>
                </div>
            </div>

            <!-- 6. Digital QR Verification -->
            <div class="col-6 col-md-4 col-lg-2">
                <div class="service-card-compact">
                    <div class="service-icon-compact" style="background:#f5f3ff;color:#7c3aed">
                        <i class="ti ti-qrcode"></i>
                    </div>
                    <div class="service-title-compact">QR Verification</div>
                    <div class="service-desc-compact">Official resident ID check</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Clean Footer (No Staff Login) -->
<footer class="portal-footer">
    <div class="container px-3">
        <div class="fw-bold text-white mb-2" style="font-size: 15px; letter-spacing: 0.3px;">
            {{ $settings['barangay_name'] ?? 'Barangay San Jose' }}
        </div>
        <div class="mb-2" style="color: #e2e8f0; font-size: 13px;">
            <span><i class="ti ti-map-pin me-1 text-info"></i>{{ $settings['barangay_address'] ?? 'San Pedro City, Laguna' }}</span>
            @if(!empty($settings['contact_number']))
                <span class="mx-2" style="opacity: 0.5">•</span>
                <span><i class="ti ti-phone me-1 text-info"></i>{{ $settings['contact_number'] }}</span>
            @endif
            @if(!empty($settings['email']))
                <span class="mx-2" style="opacity: 0.5">•</span>
                <span><i class="ti ti-mail me-1 text-info"></i>{{ $settings['email'] }}</span>
            @endif
        </div>
        <div style="font-size: 12px; color: #94a3b8; padding-top: 4px;">
            © {{ date('Y') }} {{ $settings['system_name'] ?? 'BarangayConnect' }}. All rights reserved.
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
