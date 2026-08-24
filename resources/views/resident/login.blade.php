<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident Login — SmartBarangay</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
</head>
<body>
<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;background:#f4f6fa">
    <div style="background:#fff;border-radius:16px;padding:32px;width:100%;max-width:380px;box-shadow:0 4px 24px rgba(26,58,107,0.1)">
        <div class="d-flex align-items-center gap-3 mb-4">
            <div style="width:40px;height:40px;background:#1a3a6b;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px"><i class="ti ti-building-community"></i></div>
            <div><div style="font-size:16px;font-weight:700;color:#1a1a2e">SmartBarangay</div><div style="font-size:12px;color:#888">Resident Portal</div></div>
        </div>
        @if($errors->any())<div class="alert alert-danger py-2" style="font-size:13.5px">{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('portal.login') }}">
            @csrf
            <div class="mb-3"><label class="form-label" style="font-size:13px;font-weight:500">Mobile number</label><input type="text" name="contact_number" class="form-control" placeholder="09XXXXXXXXX" value="{{ old('contact_number') }}" required autofocus></div>
            <div class="mb-4"><label class="form-label" style="font-size:13px;font-weight:500">Password</label><input type="password" name="password" class="form-control" placeholder="••••••••" required></div>
            <button type="submit" class="btn-navy-full mb-3">Sign in</button>
        </form>
        <div class="text-center" style="font-size:13px;color:#888">
            Don't have an account? <a href="{{ route('portal.register') }}" style="color:#1a3a6b;font-weight:500">Register here</a>
        </div>
        <div class="text-center mt-2" style="font-size:12px">
            <a href="{{ route('login') }}" style="color:#888">Staff / Admin login →</a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
