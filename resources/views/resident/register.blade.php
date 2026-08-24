<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident Registration — {{ $settings['system_name'] ?? 'BarangayConnect' }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
    <style>
        :root {
            --brand-navy: #1a3a6b;
            --brand-blue: #185fa5;
        }
        body {
            background: #f4f6fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            min-height: 100vh;
            padding: 30px 16px;
        }
        .register-container {
            max-width: 680px;
            margin: 0 auto;
        }
        .register-card {
            background: #fff;
            border-radius: 18px;
            padding: 36px 32px;
            box-shadow: 0 10px 30px rgba(26,58,107,0.08);
            border: 1px solid #e2e8f0;
        }
        .section-divider {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--brand-blue);
            margin: 20px 0 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .section-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }
        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 5px;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            padding: 9px 12px;
            font-size: 13.5px;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--brand-blue);
            box-shadow: 0 0 0 3px rgba(24,95,165,0.12);
        }
        .btn-register {
            background: linear-gradient(135deg, var(--brand-navy), var(--brand-blue));
            color: #fff;
            border: none;
            border-radius: 10px;
            width: 100%;
            padding: 13px;
            font-size: 15px;
            font-weight: 600;
            transition: all .2s;
            box-shadow: 0 4px 12px rgba(26,58,107,0.2);
        }
        .btn-register:hover {
            opacity: 0.95;
            color: #fff;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

<div class="register-container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('portal.home') }}" style="color:var(--brand-navy);font-size:13.5px;text-decoration:none;display:flex;align-items:center;gap:6px;font-weight:500">
            <i class="ti ti-arrow-left"></i> Back to Portal
        </a>
        <span class="small text-muted">{{ $settings['barangay_name'] ?? 'Barangay San Jose' }}</span>
    </div>

    <div class="register-card">
        <!-- Header -->
        <div class="d-flex align-items-center gap-3 mb-4">
            <div style="width:46px;height:46px;background:linear-gradient(135deg,var(--brand-navy),var(--brand-blue));border-radius:12px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;box-shadow:0 4px 12px rgba(24,95,165,0.25)">
                <i class="ti ti-user-plus"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold" style="color:#1e293b;font-size:20px">Resident Registration</h4>
                <div style="font-size:12.5px;color:#64748b">Complete your resident profile with {{ $settings['barangay_name'] ?? 'our Barangay' }}</div>
            </div>
        </div>

        @if(isset($errors) && $errors->any())
            <div class="alert alert-danger py-2 px-3 mb-3 small" role="alert">
                <i class="ti ti-alert-circle me-1"></i> {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('portal.register') }}" enctype="multipart/form-data">
            @csrf

            <!-- Photo Upload Section -->
            <div class="mb-3 d-flex flex-column align-items-center gap-2 text-center pb-3 border-bottom">
                <div id="photo-preview-wrap" style="width:84px;height:84px;border-radius:50%;overflow:hidden;background:#f0f6fc;display:flex;align-items:center;justify-content:center;border:2px dashed #93c5fd;font-size:32px;color:var(--brand-blue)">
                    <i class="ti ti-user" id="photo-icon"></i>
                    <img id="photo-preview" src="" class="d-none" style="width:100%;height:100%;object-fit:cover">
                </div>
                <div>
                    <label for="photo" class="btn btn-outline-secondary btn-sm py-1 px-3" style="font-size:12px;cursor:pointer">
                        <i class="ti ti-camera me-1"></i> Upload Resident Photo (Optional)
                    </label>
                    <input type="file" name="photo" id="photo" accept="image/*" class="d-none" onchange="previewPhoto(this)">
                </div>
            </div>

            <!-- SECTION 1: Personal Information -->
            <div class="section-divider">
                <i class="ti ti-id"></i> 1. Personal Information
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">First Name *</label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" placeholder="e.g. Juan" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Middle Name</label>
                    <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name') }}" placeholder="e.g. Santos">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Last Name *</label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" placeholder="e.g. Dela Cruz" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Birthdate *</label>
                    <input type="date" name="birthdate" class="form-control" value="{{ old('birthdate') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Gender *</label>
                    <select name="gender" class="form-select" required>
                        <option value="">Select gender...</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Civil Status *</label>
                    <select name="civil_status" class="form-select" required>
                        <option value="">Select status...</option>
                        <option value="single" {{ old('civil_status') == 'single' ? 'selected' : '' }}>Single</option>
                        <option value="married" {{ old('civil_status') == 'married' ? 'selected' : '' }}>Married</option>
                        <option value="widowed" {{ old('civil_status') == 'widowed' ? 'selected' : '' }}>Widowed</option>
                        <option value="separated" {{ old('civil_status') == 'separated' ? 'selected' : '' }}>Separated</option>
                    </select>
                </div>
            </div>

            <!-- SECTION 2: Address & Locality -->
            <div class="section-divider">
                <i class="ti ti-map-pin"></i> 2. Address & Locality
            </div>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Street / House No. / Address *</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address') }}" placeholder="House No., Street Name, Subdivision" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Purok</label>
                    <input type="text" name="purok" class="form-control" value="{{ old('purok') }}" placeholder="e.g. Purok 1">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Zone</label>
                    <input type="text" name="zone" class="form-control" value="{{ old('zone') }}" placeholder="e.g. Zone 2">
                </div>
            </div>

            <!-- SECTION 3: Contact & Occupation -->
            <div class="section-divider">
                <i class="ti ti-phone"></i> 3. Contact & Occupation
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Mobile Number *</label>
                    <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number') }}" placeholder="09XXXXXXXXX" required>
                    <div class="form-text" style="font-size:11.5px">This will be your sign-in username.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Occupation / Source of Livelihood</label>
                    <input type="text" name="occupation" class="form-control" value="{{ old('occupation') }}" placeholder="e.g. Private Employee, Vendor, Student">
                </div>
            </div>

            <!-- SECTION 4: Account Password -->
            <div class="section-divider">
                <i class="ti ti-lock"></i> 4. Account Security
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Password *</label>
                    <input type="password" name="password" class="form-control" placeholder="Minimum 6 characters" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confirm Password *</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Re-type password" required>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-register">
                <i class="ti ti-check me-1"></i> Register Resident Account
            </button>
        </form>

        <div class="text-center mt-3 pt-3 border-top" style="font-size:13.5px;color:#64748b">
            Already have an account? <a href="{{ route('login') }}" style="color:var(--brand-blue);font-weight:600;text-decoration:none">Sign In here →</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('photo-icon').classList.add('d-none');
            const img = document.getElementById('photo-preview');
            img.src = e.target.result;
            img.classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</body>
</html>
