<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SK Portal') — Sangguniang Kabataan</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap & Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
    
    <style>
        :root {
            --sk-primary: #0d9488;
            --sk-primary-dark: #0f766e;
            --sk-accent: #f59e0b;
            --sk-bg: #f8fafc;
            --sk-card-border: #e2e8f0;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: var(--sk-bg);
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Navbar */
        .sk-navbar {
            background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
            box-shadow: 0 4px 15px rgba(13, 148, 136, 0.2);
            padding: 0.75rem 0;
        }

        .sk-brand-badge {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(4px);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            padding: 3px 8px;
            border-radius: 6px;
            text-transform: uppercase;
        }

        .sk-nav-link {
            color: rgba(255, 255, 255, 0.85);
            font-weight: 600;
            font-size: 13.5px;
            padding: 6px 14px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .sk-nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.15);
        }

        .sk-nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.25);
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
        }

        /* Card custom */
        .card-custom {
            background: #fff;
            border: 1px solid var(--sk-card-border);
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card-custom:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }

        /* Stat Card */
        .stat-card-sk {
            background: #fff;
            border: 1px solid var(--sk-card-border);
            border-radius: 12px;
            padding: 1.25rem;
            position: relative;
            overflow: hidden;
        }

        .stat-card-sk::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--sk-primary);
        }

        .stat-label-sk {
            font-size: 12.5px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .stat-val-sk {
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            margin: 4px 0 2px;
        }

        .stat-sub-sk {
            font-size: 12px;
            color: #94a3b8;
        }

        /* Tables */
        .table-custom-sk th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 10px 14px;
            border-bottom: 2px solid #e2e8f0;
        }

        .table-custom-sk td {
            padding: 12px 14px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13.5px;
        }

        .badge-youth-bracket {
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 600;
        }

        /* Teal Palette Theme Overrides */
        .btn-primary {
            background-color: var(--sk-primary) !important;
            border-color: var(--sk-primary) !important;
            color: #ffffff !important;
        }
        .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
            background-color: var(--sk-primary-dark) !important;
            border-color: var(--sk-primary-dark) !important;
            color: #ffffff !important;
        }
        .btn-outline-primary {
            color: var(--sk-primary) !important;
            border-color: var(--sk-primary) !important;
        }
        .btn-outline-primary:hover, .btn-outline-primary:focus, .btn-outline-primary:active {
            background-color: var(--sk-primary) !important;
            border-color: var(--sk-primary) !important;
            color: #ffffff !important;
        }
        .text-primary {
            color: var(--sk-primary) !important;
        }
        .bg-primary {
            background-color: var(--sk-primary) !important;
        }
        .border-primary {
            border-color: var(--sk-primary) !important;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--sk-primary);
            box-shadow: 0 0 0 0.25rem rgba(13, 148, 136, 0.2);
        }
        .page-item.active .page-link {
            background-color: var(--sk-primary);
            border-color: var(--sk-primary);
            color: #ffffff;
        }
        .page-link {
            color: var(--sk-primary);
        }
    </style>
    @stack('styles')
</head>
<body>

    {{-- Main SK Navigation Bar --}}
    <nav class="sk-navbar sticky-top">
        <div class="container-fluid px-lg-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
            {{-- Brand / Logo --}}
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('sk.dashboard') }}" class="text-white text-decoration-none d-flex align-items-center gap-2">
                    <div style="width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,0.25);display:flex;align-items:center;justify-content:center;font-size:20px">
                        ⚡
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold fs-5 text-white tracking-wide">Sangguniang Kabataan</span>
                            <span class="sk-brand-badge">SK Portal</span>
                        </div>
                        <div style="font-size:11px;color:rgba(255,255,255,0.85)">Youth Development &amp; Governance Center</div>
                    </div>
                </a>
            </div>

            {{-- Nav Links --}}
            <div class="d-flex align-items-center gap-1 flex-wrap">
                <a href="{{ route('sk.dashboard') }}" class="sk-nav-link {{ request()->routeIs('sk.dashboard*') ? 'active' : '' }}">
                    <i class="ti ti-layout-dashboard"></i> Dashboard
                </a>
                <a href="{{ route('sk.youth-residents.index') }}" class="sk-nav-link {{ request()->routeIs('sk.youth-residents*') ? 'active' : '' }}">
                    <i class="ti ti-users"></i> Youth Roster (15–24)
                </a>
                <a href="{{ route('sk.programs.index') }}" class="sk-nav-link {{ request()->routeIs('sk.programs*') ? 'active' : '' }}">
                    <i class="ti ti-target"></i> Programs &amp; Projects
                </a>
                <a href="{{ route('sk.announcements.index') }}" class="sk-nav-link {{ request()->routeIs('sk.announcements*') ? 'active' : '' }}">
                    <i class="ti ti-speakerphone"></i> Announcements
                </a>
                @if(auth()->user()->canManageSkCouncilors())
                    <a href="{{ route('sk.councilors.index') }}" class="sk-nav-link {{ request()->routeIs('sk.councilors*') ? 'active' : '' }}">
                        <i class="ti ti-user-check"></i> SK Councilors
                    </a>
                @endif
            </div>

            {{-- User Dropdown & Admin Switch --}}
            <div class="d-flex align-items-center gap-2">
                @if(auth()->user()->isCapOrAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-warning btn-sm fw-bold d-flex align-items-center gap-1 text-dark" style="font-size:12px;border-radius:20px;padding:4px 12px" title="Return to Barangay Main Admin Panel">
                        <i class="ti ti-arrow-back-up"></i> Admin Panel
                    </a>
                @endif

                <div class="dropdown">
                    <button class="btn btn-outline-light btn-sm dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius:20px;padding:4px 12px">
                        <div style="width:24px;height:24px;border-radius:50%;background:rgba(255,255,255,0.3);display:flex;align-items:center;justify-content:center;font-size:12px">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <span class="fw-semibold" style="font-size:12.5px">{{ auth()->user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="font-size:13px;border-radius:10px">
                        <li class="px-3 py-2 border-bottom">
                            <div class="fw-bold">{{ auth()->user()->name }}</div>
                            <span class="badge bg-primary mt-1" style="font-size:10px">{{ auth()->user()->role_label }}</span>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2 py-2">
                                    <i class="ti ti-logout"></i> Sign Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    {{-- Main Body Content --}}
    <main class="container-fluid px-lg-4 my-4 flex-grow-1">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                <i class="ti ti-circle-check fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                <i class="ti ti-alert-triangle fs-5"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                <i class="ti ti-info-circle fs-5"></i>
                <div>{{ session('info') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="mt-auto py-3 bg-white border-top text-center text-muted" style="font-size:12px">
        <div class="container-fluid">
            <strong>Sangguniang Kabataan Office</strong> &bull; Empowering the Youth of the Barangay &bull; Katipunan ng Kabataan
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
