@extends('layouts.admin')
@section('title','Settings')
@section('page-title','Settings')
@section('content')
<div class="row mt-2 justify-content-center">
    <div class="col-12 col-lg-10">
        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <div class="card-custom">
                        <h6 class="fw-semibold mb-3">Barangay information</h6>
                        <div class="mb-3"><label class="form-label">Barangay name</label><input type="text" name="barangay_name" class="form-control" value="{{ $settings['barangay_name'] ?? '' }}"></div>
                        <div class="mb-3"><label class="form-label">Address / Municipality</label><input type="text" name="barangay_address" class="form-control" value="{{ $settings['barangay_address'] ?? '' }}"></div>
                        <div class="mb-3"><label class="form-label">Contact number</label><input type="text" name="contact_number" class="form-control" value="{{ $settings['contact_number'] ?? '' }}"></div>
                        <div class="mb-0"><label class="form-label">Email address</label><input type="email" name="email" class="form-control" value="{{ $settings['email'] ?? '' }}"></div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card-custom">
                        <h6 class="fw-semibold mb-3">System configuration</h6>
                        <div class="mb-3"><label class="form-label">System name</label><input type="text" name="system_name" class="form-control" value="{{ $settings['system_name'] ?? 'SmartBarangay' }}"></div>
                        <div class="mb-3"><label class="form-label">Report header</label><input type="text" name="report_header" class="form-control" value="{{ $settings['report_header'] ?? 'Republic of the Philippines' }}"></div>
                        <div class="mb-3"><label class="form-label">Barangay captain</label><input type="text" name="captain_name" class="form-control" value="{{ $settings['captain_name'] ?? '' }}"></div>
                        <div class="mb-0"><label class="form-label">Fiscal year</label><select name="fiscal_year" class="form-select"><option value="{{ date('Y') }}" {{ ($settings['fiscal_year'] ?? '') == date('Y') ? 'selected' : '' }}>{{ date('Y') }}</option><option value="{{ date('Y')-1 }}" {{ ($settings['fiscal_year'] ?? '') == date('Y')-1 ? 'selected' : '' }}>{{ date('Y')-1 }}</option></select></div>
                    </div>
                </div>

                {{-- System Maintenance Mode --}}
                <div class="col-12">
                    <div class="card-custom border {{ ($settings['maintenance_mode'] ?? '0') == '1' ? 'border-warning' : 'border-light' }}" style="{{ ($settings['maintenance_mode'] ?? '0') == '1' ? 'background:#fffbf0' : '' }}">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div>
                                <h6 class="fw-bold mb-1 d-flex align-items-center gap-2">
                                    <i class="ti ti-tool {{ ($settings['maintenance_mode'] ?? '0') == '1' ? 'text-warning' : 'text-muted' }}" style="font-size:18px"></i>
                                    System Maintenance Mode
                                </h6>
                                <div class="text-muted" style="font-size:13px">
                                    When <strong>ON</strong>, the resident portal will be temporarily unavailable and display a maintenance notice to users. Admin access remains unaffected.
                                </div>
                            </div>
                            <div class="form-check form-switch" style="transform:scale(1.3);transform-origin:right center">
                                <input class="form-check-input" type="checkbox" name="maintenance_mode" id="maintenanceSwitch" value="1"
                                    {{ ($settings['maintenance_mode'] ?? '0') == '1' ? 'checked' : '' }}
                                    style="cursor:pointer;width:3em;height:1.5em">
                                <label class="form-check-label fw-semibold ms-2" for="maintenanceSwitch" style="cursor:pointer">
                                    {{ ($settings['maintenance_mode'] ?? '0') == '1' ? 'ON' : 'OFF' }}
                                </label>
                            </div>
                        </div>
                        @if(($settings['maintenance_mode'] ?? '0') == '1')
                        <div class="alert alert-warning py-2 mb-0 mt-3 small">
                            <i class="ti ti-alert-triangle me-1"></i> <strong>Maintenance mode is currently active.</strong> Residents cannot access the portal until this is turned OFF.
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end mt-3">
                <button type="submit" class="btn btn-navy">Save settings</button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
document.getElementById('maintenanceSwitch').addEventListener('change', function() {
    this.nextElementSibling.textContent = this.checked ? 'ON' : 'OFF';
});
</script>
@endpush
@endsection

