<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','Home') — SmartBarangay Resident Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
    <style>
        /* Hide top navbar on desktop — navigation is sidebar/bottom-nav only on mobile */
        @media (min-width: 768px) {
            .portal-nav { display: none !important; }
            main { padding-top: 16px; }
        }
        /* On desktop, show a minimal top bar with brand + user only */
        .portal-desktop-topbar {
            display: none;
        }
        @media (min-width: 768px) {
            .portal-desktop-topbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 12px 32px;
                background: #fff;
                border-bottom: 1px solid #e8eef4;
                position: sticky;
                top: 0;
                z-index: 100;
                box-shadow: 0 1px 6px rgba(24,95,165,.06);
            }
            .portal-desktop-nav-links a {
                color: #1a3a6b;
                text-decoration: none;
                font-size: 14px;
                font-weight: 500;
                padding: 6px 14px;
                border-radius: 8px;
                transition: background .15s;
            }
            .portal-desktop-nav-links a:hover,
            .portal-desktop-nav-links a.active {
                background: #e6f1fb;
                color: #185fa5;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    {{-- Desktop Slim Topbar (hidden on mobile) --}}
    <div class="portal-desktop-topbar">
        <a href="{{ route('portal.index') }}" style="display:flex;align-items:center;gap:10px;text-decoration:none">
            <div style="width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,#1a3a6b,#185fa5);display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px">
                <i class="ti ti-building-community"></i>
            </div>
            <div>
                <div style="font-weight:700;font-size:14px;color:#1a3a6b;line-height:1.1">SmartBarangay</div>
                <div style="font-size:11px;color:#888">Resident Portal</div>
            </div>
        </a>
        <div class="portal-desktop-nav-links d-flex align-items-center gap-1">
            <a href="{{ route('portal.dashboard') }}" class="{{ request()->routeIs('portal.dashboard') ? 'active' : '' }}"><i class="ti ti-home me-1"></i>Home</a>
            <a href="{{ route('portal.request') }}" class="{{ request()->routeIs('portal.request*') ? 'active' : '' }}"><i class="ti ti-file-plus me-1"></i>Request Document</a>
            <a href="{{ route('portal.report') }}" class="{{ request()->routeIs('portal.report*') ? 'active' : '' }}"><i class="ti ti-message-report me-1"></i>Report Issue</a>
            <a href="{{ route('portal.track') }}" class="{{ request()->routeIs('portal.track*') ? 'active' : '' }}"><i class="ti ti-list-search me-1"></i>Track</a>
            <a href="{{ route('portal.announcements') }}" class="{{ request()->routeIs('portal.announcements') ? 'active' : '' }}"><i class="ti ti-speakerphone me-1"></i>Announcements</a>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if(session('resident_id'))
                @php $res = \App\Models\Resident::find(session('resident_id')); @endphp
                <div class="dropdown">
                    <div class="portal-user-btn dropdown-toggle" data-bs-toggle="dropdown" style="cursor:pointer;display:flex;align-items:center;gap:8px;padding:6px 12px;border-radius:10px;background:#f0f5ff">
                        <div class="portal-user-av" style="width:30px;height:30px;border-radius:50%;background:#185fa5;color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700">
                            {{ substr($res->first_name ?? 'R', 0, 1) }}{{ substr($res->last_name ?? '', 0, 1) }}
                        </div>
                        <span style="font-size:13px;font-weight:600;color:#1a3a6b">{{ $res->first_name ?? 'Resident' }}</span>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('portal.profile') }}"><i class="ti ti-user me-2"></i>My Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('portal.logout') }}">@csrf
                                <button type="submit" class="dropdown-item text-danger"><i class="ti ti-logout me-2"></i>Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <a href="{{ route('portal.login') }}" class="btn-portal-login">Login</a>
            @endif
        </div>
    </div>

    {{-- Mobile Top Navigation (visible only on mobile) --}}
    <nav class="portal-nav d-md-none">
        <div class="container-fluid px-3 px-md-4">
            <div class="d-flex align-items-center justify-content-between" style="height:56px">
                <a href="{{ route('portal.index') }}" class="portal-brand">
                    <div class="portal-brand-icon"><i class="ti ti-building-community"></i></div>
                    <div class="d-none d-sm-block">
                        <div class="portal-brand-name">SmartBarangay</div>
                        <div class="portal-brand-sub">Resident Portal</div>
                    </div>
                </a>
                <div class="d-flex align-items-center gap-2">
                    @if(session('resident_id'))
                        @php if(!isset($res)) $res = \App\Models\Resident::find(session('resident_id')); @endphp
                        <div class="dropdown">
                            <div class="portal-user-btn dropdown-toggle" data-bs-toggle="dropdown">
                                <div class="portal-user-av">
                                    {{ substr($res->first_name ?? 'R', 0, 1) }}{{ substr($res->last_name ?? '', 0, 1) }}
                                </div>
                            </div>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('portal.profile') }}"><i class="ti ti-user me-2"></i>My Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('portal.logout') }}">@csrf
                                        <button type="submit" class="dropdown-item text-danger"><i class="ti ti-logout me-2"></i>Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('portal.login') }}" class="btn-portal-login">Login</a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-3 mb-0" role="alert">
            <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-3 mb-0" role="alert">
            <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Page content — add bottom padding on mobile to avoid overlap with bottom nav -->
    <main style="padding-bottom: 80px">@yield('content')</main>

    <!-- Mobile bottom nav (mobile only) -->
    @if(session('resident_id'))
    <nav class="mobile-bottom-nav d-md-none">
        <a href="{{ route('portal.dashboard') }}" class="mobile-nav-item {{ request()->routeIs('portal.dashboard') ? 'active' : '' }}">
            <i class="ti ti-home"></i><span>Home</span>
        </a>
        <a href="{{ route('portal.request') }}" class="mobile-nav-item {{ request()->routeIs('portal.request*') ? 'active' : '' }}">
            <i class="ti ti-file-plus"></i><span>Request</span>
        </a>
        <a href="{{ route('portal.report') }}" class="mobile-nav-item {{ request()->routeIs('portal.report*') ? 'active' : '' }}">
            <i class="ti ti-message-report"></i><span>Report</span>
        </a>
        <a href="{{ route('portal.track') }}" class="mobile-nav-item {{ request()->routeIs('portal.track*') ? 'active' : '' }}">
            <i class="ti ti-list-search"></i><span>Track</span>
        </a>
        <a href="{{ route('portal.profile') }}" class="mobile-nav-item {{ request()->routeIs('portal.profile') ? 'active' : '' }}">
            <i class="ti ti-user-circle"></i><span>Profile</span>
        </a>
    </nav>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/portal.js') }}"></script>
    @stack('scripts')
</body>
</html>
