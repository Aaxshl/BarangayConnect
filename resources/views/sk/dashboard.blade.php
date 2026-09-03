<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SK Portal — Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
    <style>
        body { background: #f0f4f8; font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
        .sk-header { background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: white; padding: 2.5rem 0; }
        .card-custom { background: white; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); padding: 1.5rem; }
    </style>
</head>
<body>
    <header class="sk-header">
        <div class="container d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="badge bg-warning text-dark fw-bold mb-1">SK PORTAL</span>
                <h2 class="h4 mb-0 fw-bold">Sangguniang Kabataan Office</h2>
                <div class="small opacity-75">Welcome, {{ $user->name }} ({{ $user->role_label }})</div>
            </div>
            <div>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm"><i class="ti ti-logout me-1"></i>Logout</button>
                </form>
            </div>
        </div>
    </header>

    <main class="container my-4">
        @if(session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card-custom">
                    <div class="text-muted small text-uppercase fw-semibold">Youth Residents (15–30 yrs)</div>
                    <div class="fs-2 fw-bold text-primary">{{ $youthCount }}</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-custom">
                    <div class="text-muted small text-uppercase fw-semibold">SK Announcements & Events</div>
                    <div class="fs-2 fw-bold text-info">{{ $skAnnouncementsCount }}</div>
                </div>
            </div>
        </div>

        <div class="card-custom text-center py-5">
            <i class="ti ti-sparkles text-primary fs-1 mb-2"></i>
            <h5>Welcome to the dedicated Sangguniang Kabataan Portal</h5>
            <p class="text-muted mb-0">Phase 4 will build out full youth program management, event coordination, and SK announcement tools here.</p>
        </div>
    </main>
</body>
</html>
