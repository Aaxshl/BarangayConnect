<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden — Access Denied</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .error-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            max-width: 480px;
            width: 100%;
            padding: 2.5rem;
            text-align: center;
        }
        .icon-box {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #fee2e2;
            color: #ef4444;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin: 0 auto 1.5rem;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="icon-box">
            <i class="ti ti-shield-lock"></i>
        </div>
        <h3 class="fw-bold text-dark mb-2">Access Restricted</h3>
        <p class="text-muted mb-4" style="font-size: 14px;">
            {{ $exception->getMessage() ?: 'Your account does not have sufficient permissions to access this feature or perform this action.' }}
        </p>
        <div class="d-flex justify-content-center gap-2">
            <button type="button" onclick="window.history.back()" class="btn btn-outline-secondary btn-sm px-3">
                <i class="ti ti-arrow-left me-1"></i> Go Back
            </button>
            @auth
                @if(auth()->user()->isSK())
                    <a href="{{ route('sk.dashboard') }}" class="btn btn-primary btn-sm px-3">
                        <i class="ti ti-layout-dashboard me-1"></i> SK Dashboard
                    </a>
                @else
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-sm px-3">
                        <i class="ti ti-layout-dashboard me-1"></i> Admin Dashboard
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn btn-primary btn-sm px-3">
                    <i class="ti ti-login me-1"></i> Log In
                </a>
            @endauth
        </div>
    </div>
</body>
</html>
