<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','Dashboard') — SmartBarangay Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @stack('styles')
</head>
<body>

<!-- Mobile overlay -->
<div id="sidebar-overlay" class="sidebar-overlay d-lg-none" onclick="closeSidebar()"></div>

<!-- Sidebar -->
<nav id="sidebar" class="sidebar d-flex flex-column">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="ti ti-building-community"></i></div>
        <div>
            <div class="brand-name">{{ \App\Models\Setting::get('system_name', 'BarangayConnect') }}</div>
            <div class="brand-sub">{{ \App\Models\Setting::get('barangay_name', 'Barangay') }}</div>
        </div>
    </div>

    <div class="sidebar-section">Main</div>
    <a href="{{ route('admin.dashboard') }}" class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="ti ti-layout-dashboard"></i> Dashboard
    </a>

    @if(auth()->user()->canDo('nav.residents') || auth()->user()->canDo('nav.households') || auth()->user()->canDo('nav.documents'))
    <div class="sidebar-section">Records</div>
    @if(auth()->user()->canDo('nav.residents'))
    <a href="{{ route('admin.residents.index') }}" class="sidebar-item {{ request()->routeIs('admin.residents.*') ? 'active' : '' }}">
        <i class="ti ti-users"></i> Residents
    </a>
    @endif
    @if(auth()->user()->canDo('nav.households'))
    <a href="{{ route('admin.households.index') }}" class="sidebar-item {{ request()->routeIs('admin.households.*') ? 'active' : '' }}">
        <i class="ti ti-home"></i> Households
    </a>
    @endif
    @if(auth()->user()->canDo('nav.documents'))
    <a href="{{ route('admin.documents.index') }}" class="sidebar-item {{ request()->routeIs('admin.documents.*') ? 'active' : '' }}">
        <i class="ti ti-file-text"></i> Documents
    </a>
    @endif
    @endif

    @if(auth()->user()->canDo('nav.services') || auth()->user()->canDo('nav.requests') || auth()->user()->canDo('nav.mapping') || auth()->user()->canDo('nav.qr'))
    <div class="sidebar-section">Services</div>
    @if(auth()->user()->canDo('nav.services'))
    <a href="{{ route('admin.service-logs.index') }}" class="sidebar-item {{ request()->routeIs('admin.service-logs.*') ? 'active' : '' }}">
        <i class="ti ti-list-check"></i> Service Logs
    </a>
    @endif
    @if(auth()->user()->canDo('nav.requests'))
    <a href="{{ route('admin.citizen-requests.index') }}" class="sidebar-item {{ request()->routeIs('admin.citizen-requests.*') ? 'active' : '' }}">
        <i class="ti ti-message-report"></i> Citizen's Requests/Reports
        @php $unviewedCount = \App\Models\CitizenRequest::whereNull('viewed_at')->where('status','pending')->count() @endphp
        @if($unviewedCount > 0)
            <span class="badge bg-danger ms-auto">{{ $unviewedCount }}</span>
        @endif
    </a>
    @endif
    @if(auth()->user()->canDo('nav.mapping'))
    <a href="{{ route('admin.mapping.index') }}" class="sidebar-item {{ request()->routeIs('admin.mapping.*') ? 'active' : '' }}">
        <i class="ti ti-map-pin"></i> Issue Mapping
    </a>
    @endif
    @if(auth()->user()->canDo('nav.qr'))
    <a href="{{ route('admin.qr.index') }}" class="sidebar-item {{ request()->routeIs('admin.qr.*') ? 'active' : '' }}">
        <i class="ti ti-qrcode"></i> QR Verification
    </a>
    @endif
    @endif

    <div class="sidebar-section">Manage</div>
    @if(auth()->user()->canDo('nav.reports'))
    <a href="{{ route('admin.reports.index') }}" class="sidebar-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
        <i class="ti ti-chart-bar"></i> Reports
    </a>
    @endif
    @if(auth()->user()->canDo('nav.announcements'))
    <a href="{{ route('admin.announcements.index') }}" class="sidebar-item {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
        <i class="ti ti-speakerphone"></i> Announcements
    </a>
    @endif
    @if(auth()->user()->canManageUsers())
    <a href="{{ route('admin.users.index') }}" class="sidebar-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <i class="ti ti-shield"></i> Users
    </a>
    <a href="{{ route('admin.settings.index') }}" class="sidebar-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
        <i class="ti ti-settings"></i> Settings
    </a>
    @endif
    <a href="{{ route('admin.profile.index') }}" class="sidebar-item {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
        <i class="ti ti-user-circle"></i> My Profile
    </a>

    <div class="mt-auto p-3 border-top border-white border-opacity-10">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-sidebar-logout w-100">
                <i class="ti ti-logout"></i> Logout
            </button>
        </form>
    </div>
</nav>

<!-- Main content -->
<div class="main-content">
    <!-- Topbar -->
    <header class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn-hamburger d-lg-none" onclick="openSidebar()">
                <i class="ti ti-menu-2"></i>
            </button>
            <div>
                <h1 class="page-title">@yield('page-title','Dashboard')</h1>
                @hasSection('breadcrumb')
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">@yield('breadcrumb')</ol>
                </nav>
                @endif
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            {{-- Notification Bell Dropdown --}}
            @php
                $notifRequests = \App\Models\CitizenRequest::whereNull('viewed_at')
                    ->where('status','pending')
                    ->with('resident')
                    ->latest()
                    ->limit(8)
                    ->get();
                $notifCount = $notifRequests->count();
                $maintenanceMode = \App\Models\Setting::get('maintenance_mode','0');
            @endphp
            <div class="dropdown">
                <button class="topbar-icon position-relative border-0 bg-transparent" data-bs-toggle="dropdown" aria-expanded="false" id="notif-bell" style="cursor:pointer">
                    <i class="ti ti-bell" style="font-size:20px"></i>
                    @if($notifCount > 0)
                        <span class="notif-badge">{{ $notifCount > 9 ? '9+' : $notifCount }}</span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end p-0" style="min-width:320px;max-width:360px;border-radius:12px;overflow:hidden;box-shadow:0 8px 32px rgba(0,0,0,.12)">
                    <div class="px-3 py-2 border-bottom d-flex align-items-center justify-content-between" style="background:#f8f9fa">
                        <span class="fw-bold" style="font-size:13px">Notifications</span>
                        @if($notifCount > 0)
                            <span class="badge bg-danger">{{ $notifCount }} new</span>
                        @else
                            <span class="text-muted small">All caught up</span>
                        @endif
                    </div>
                    @if($notifCount > 0)
                        @foreach($notifRequests as $nr)
                        <a href="{{ route('admin.citizen-requests.show', $nr) }}" class="d-flex align-items-start gap-2 px-3 py-2 text-decoration-none text-dark border-bottom" style="font-size:12.5px;transition:background .15s" onmouseover="this.style.background='#f0f5ff'" onmouseout="this.style.background=''">
                            <div style="width:34px;height:34px;border-radius:50%;background:#e53935;color:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:15px;margin-top:2px">
                                <i class="ti ti-message-report"></i>
                            </div>
                            <div style="min-width:0">
                                <div class="fw-semibold text-truncate">{{ ucwords(str_replace('_',' ',$nr->request_type)) }}</div>
                                <div class="text-muted text-truncate">{{ optional($nr->resident)->full_name ?? 'Anonymous' }}</div>
                                <div class="text-muted" style="font-size:11px">{{ $nr->created_at->diffForHumans() }}</div>
                            </div>
                        </a>
                        @endforeach
                        <div class="text-center py-2">
                            <a href="{{ route('admin.citizen-requests.index') }}" class="text-primary small fw-semibold">View all requests →</a>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="ti ti-bell-off" style="font-size:28px;opacity:.4"></i>
                            <div class="small mt-1">No new notifications</div>
                        </div>
                    @endif
                    @if($maintenanceMode == '1')
                    <div class="px-3 py-2 border-top d-flex align-items-center gap-2" style="background:#fff3cd">
                        <i class="ti ti-tool text-warning"></i>
                        <span class="small text-warning fw-semibold">Maintenance Mode is ON</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="dropdown">
                <div class="topbar-user dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown" style="cursor:pointer">
                    <div class="user-avatar">{{ substr(auth()->user()->name,0,2) }}</div>
                    <div class="d-none d-md-block text-start">
                        <div class="fw-semibold lh-1" style="font-size:13px">{{ auth()->user()->name }}</div>
                        <span class="badge bg-primary bg-opacity-10 text-primary fw-medium px-1.5 py-0.5" style="font-size:10px">
                            {{ auth()->user()->role_label }}
                        </span>
                    </div>
                </div>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li class="px-3 py-1 d-md-none border-bottom">
                        <div class="fw-bold">{{ auth()->user()->name }}</div>
                        <div class="small text-muted">{{ auth()->user()->role_label }}</div>
                    </li>
                    <li><a class="dropdown-item" href="{{ route('admin.profile.index') }}"><i class="ti ti-user me-2"></i>Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <button type="submit" class="dropdown-item text-danger"><i class="ti ti-logout me-2"></i>Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Flash messages -->
    <div class="container-fluid px-4 pt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <!-- Page content -->
    <main class="container-fluid px-4 pb-4">
        @yield('content')
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/admin.js') }}"></script>
@stack('scripts')
</body>
</html>
