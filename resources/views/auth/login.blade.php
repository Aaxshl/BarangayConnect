<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — BarangayConnect</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
    <style>
        :root {
            --primary-navy: #1a3a6b;
            --primary-blue: #185fa5;
            --bg-light: #f4f6fa;
        }
        body { 
            background: var(--bg-light); 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }
        .login-card { 
            background: #fff; 
            border-radius: 18px; 
            padding: 36px 32px; 
            width: 100%; 
            max-width: 420px; 
            box-shadow: 0 10px 30px rgba(26,58,107,0.08); 
            border: 1px solid #e5e9f0;
        }
        .brand-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 24px;
        }
        .brand-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary-navy), var(--primary-blue));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 24px;
            box-shadow: 0 4px 12px rgba(24,95,165,0.25);
        }
        .brand-title {
            font-size: 20px;
            font-weight: 800;
            color: #1a1a2e;
            line-height: 1.2;
        }
        .brand-sub {
            font-size: 13px;
            color: #6c757d;
        }
        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }
        .input-group-custom {
            position: relative;
        }
        .input-group-custom .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 18px;
            pointer-events: none;
            z-index: 5;
        }
        .form-control-custom {
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            padding: 11px 14px 11px 42px;
            font-size: 14px;
            width: 100%;
            transition: all .2s ease;
        }
        .form-control-custom:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3.5px rgba(24,95,165,0.12);
            outline: none;
        }
        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 18px;
            padding: 0;
            z-index: 5;
        }
        .password-toggle:hover {
            color: var(--primary-navy);
        }
        .btn-signin {
            background: linear-gradient(135deg, var(--primary-navy), var(--primary-blue));
            color: #fff;
            border: none;
            border-radius: 10px;
            width: 100%;
            padding: 12px;
            font-size: 15px;
            font-weight: 600;
            transition: all .2s ease;
            box-shadow: 0 4px 12px rgba(26,58,107,0.18);
        }
        .btn-signin:hover {
            opacity: 0.95;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(26,58,107,0.24);
            color: #fff;
        }
        .hint-box {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 10px;
            padding: 12px;
            font-size: 12px;
            color: #475569;
            line-height: 1.5;
        }
    </style>
</head>
<body>

<div class="login-card">
    <!-- Brand Header -->
    <div class="brand-header">
        <div class="brand-icon">
            <i class="ti ti-building-community"></i>
        </div>
        <div>
            <div class="brand-title">BarangayConnect</div>
            <div class="brand-sub">Unified Portal Sign In</div>
        </div>
    </div>

    <!-- Flash & Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 px-3 mb-3 small" role="alert">
            <i class="ti ti-circle-check me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-3 small" role="alert">
            <i class="ti ti-alert-circle me-1"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger py-2 px-3 mb-3 small">
            <i class="ti ti-alert-circle me-1"></i>{{ $errors->first() }}
        </div>
    @endif

    <!-- Unified Login Form -->
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Login ID (Email or Mobile) -->
        <div class="mb-3">
            <label class="form-label" for="login_id">Email Address or Mobile Number</label>
            <div class="input-group-custom">
                <i class="ti ti-user input-icon"></i>
                <input type="text" 
                       id="login_id" 
                       name="login_id" 
                       class="form-control-custom" 
                       value="{{ old('login_id') }}" 
                       placeholder="e.g. 09123456789 or admin@brgy.gov.ph" 
                       required 
                       autofocus>
            </div>
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label class="form-label" for="password">Password</label>
            <div class="input-group-custom">
                <i class="ti ti-lock input-icon"></i>
                <input type="password" 
                       id="password" 
                       name="password" 
                       class="form-control-custom" 
                       placeholder="••••••••" 
                       style="padding-right: 42px;"
                       required>
                <button type="button" class="password-toggle" id="togglePassword" title="Show/Hide Password">
                    <i class="ti ti-eye" id="toggleIcon"></i>
                </button>
            </div>
        </div>

        <!-- Remember Me -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label small text-muted" for="remember">
                    Remember me
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-signin mb-3">
            <i class="ti ti-login me-1"></i> Sign In to Account
        </button>
    </form>

    <!-- Helper Tip -->
    <div class="hint-box mb-3">
        <div class="fw-semibold mb-1 text-dark"><i class="ti ti-info-circle text-primary me-1"></i>Sign In Guide:</div>
        <div>• <strong>Residents:</strong> Enter your registered Mobile Number (e.g. <code>09XXXXXXXXX</code>)</div>
        <div>• <strong>Barangay Staff / Admin:</strong> Enter your official Barangay Email</div>
    </div>

    <!-- Registration Link -->
    <div class="text-center pt-2" style="font-size: 13.5px;">
        <span class="text-muted">Not yet registered as a resident?</span><br>
        <a href="{{ route('portal.register') }}" class="fw-bold text-decoration-none" style="color: var(--primary-blue);">
            Create a Resident Account →
        </a>
    </div>

    <!-- Back to Public Portal -->
    <div class="text-center mt-3 pt-2 border-top" style="font-size: 12.5px;">
        <a href="{{ route('portal.home') }}" class="text-muted text-decoration-none">
            <i class="ti ti-arrow-left me-1"></i> Back to Public Portal
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('ti-eye');
        toggleIcon.classList.add('ti-eye-off');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('ti-eye-off');
        toggleIcon.classList.add('ti-eye');
    }
});
</script>
</body>
</html>
