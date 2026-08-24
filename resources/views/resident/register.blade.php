<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — SmartBarangay</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
</head>
<body style="padding-bottom:0">
<div style="min-height:100vh;background:#f4f6fa;padding:30px 16px">
    <div style="max-width:520px;margin:0 auto">
        <a href="{{ route('portal.index') }}" style="color:#1a3a6b;font-size:13px;text-decoration:none;display:flex;align-items:center;gap:5px;margin-bottom:20px"><i class="ti ti-arrow-left"></i> Back to portal</a>
        <div style="background:#fff;border-radius:16px;padding:28px;box-shadow:0 4px 24px rgba(26,58,107,0.08)">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div style="width:40px;height:40px;background:#1a3a6b;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px"><i class="ti ti-building-community"></i></div>
                <div><div style="font-size:16px;font-weight:700">Register as resident</div><div style="font-size:12px;color:#888">Create your SmartBarangay account</div></div>
            </div>
            @if($errors->any())<div class="alert alert-danger py-2" style="font-size:13.5px">{{ $errors->first() }}</div>@endif
            <form method="POST" action="{{ route('portal.register') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-6"><label class="form-label" style="font-size:13px;font-weight:500">First name *</label><input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required></div>
                    <div class="col-6"><label class="form-label" style="font-size:13px;font-weight:500">Last name *</label><input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required></div>
                    <div class="col-6"><label class="form-label" style="font-size:13px;font-weight:500">Birthdate *</label><input type="date" name="birthdate" class="form-control" value="{{ old('birthdate') }}" required></div>
                    <div class="col-6"><label class="form-label" style="font-size:13px;font-weight:500">Gender *</label><select name="gender" class="form-select" required><option value="">Select...</option><option value="male">Male</option><option value="female">Female</option></select></div>
                    <div class="col-12"><label class="form-label" style="font-size:13px;font-weight:500">Civil status *</label><select name="civil_status" class="form-select" required><option value="">Select...</option><option value="single">Single</option><option value="married">Married</option><option value="widowed">Widowed</option><option value="separated">Separated</option></select></div>
                    <div class="col-12"><label class="form-label" style="font-size:13px;font-weight:500">Home address *</label><input type="text" name="address" class="form-control" placeholder="Purok, Zone, Street" value="{{ old('address') }}" required></div>
                    <div class="col-12"><label class="form-label" style="font-size:13px;font-weight:500">Mobile number *</label><input type="text" name="contact_number" class="form-control" placeholder="09XXXXXXXXX" value="{{ old('contact_number') }}" required></div>
                    <div class="col-12"><label class="form-label" style="font-size:13px;font-weight:500">Password *</label><input type="password" name="password" class="form-control" required></div>
                    <div class="col-12"><label class="form-label" style="font-size:13px;font-weight:500">Confirm password *</label><input type="password" name="password_confirmation" class="form-control" required></div>
                </div>
                <button type="submit" class="btn-navy-full mt-4">Create account</button>
            </form>
            <div class="text-center mt-3" style="font-size:13px;color:#888">Already have an account? <a href="{{ route('portal.login') }}" style="color:#1a3a6b;font-weight:500">Login here</a></div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
