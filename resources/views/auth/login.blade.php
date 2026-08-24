<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — SmartBarangay</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
    <style>
        body { background: #f4f6fa; font-family: 'Segoe UI', system-ui, sans-serif; }
        .login-wrap { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .login-card { background: #fff; border-radius: 16px; padding: 36px 32px; width: 100%; max-width: 400px; box-shadow: 0 4px 24px rgba(26,58,107,0.1); }
        .login-brand { display: flex; align-items: center; gap: 12px; margin-bottom: 28px; }
        .login-icon { width: 44px; height: 44px; background: #1a3a6b; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 22px; }
        .login-title { font-size: 18px; font-weight: 700; color: #1a1a2e; margin: 0; }
        .login-sub { font-size: 12.5px; color: #888; }
        .form-control { border-radius: 8px; border: 1px solid #dde2ec; padding: 10px 12px; font-size: 14px; }
        .form-control:focus { border-color: #1a3a6b; box-shadow: 0 0 0 3px rgba(26,58,107,0.1); }
        .btn-login { background: #1a3a6b; color: #fff; border: none; border-radius: 8px; width: 100%; padding: 12px; font-size: 14px; font-weight: 500; }
        .btn-login:hover { background: #15316a; color: #fff; }
        .portal-link { text-align: center; margin-top: 20px; font-size: 13px; color: #888; }
        .portal-link a { color: #1a3a6b; font-weight: 500; }
        .alert { border-radius: 8px; font-size: 13.5px; }
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="login-card">
        <div class="login-brand">
            <div class="login-icon"><i class="ti ti-building-community"></i></div>
            <div>
                <div class="login-title">SmartBarangay</div>
                <div class="login-sub">Admin Portal</div>
            </div>
        </div>
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-medium" style="font-size:13px">Email address</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="admin@brgy.gov.ph" required autofocus>
            </div>
            <div class="mb-4">
                <label class="form-label fw-medium" style="font-size:13px">Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-login">Sign in to admin panel</button>
        </form>
        <div class="portal-link">
            <a href="{{ route('portal.index') }}">← Back to Resident Portal</a>
        </div>
    </div>
</div>
</body>
</html>
