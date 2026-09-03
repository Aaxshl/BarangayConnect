@extends('layouts.admin')
@section('title','Settings')
@section('page-title','Settings & Permissions')
@section('content')

{{-- Tab Navigation --}}
<ul class="nav nav-pills mb-4" id="settingsTab" role="tablist" style="gap:8px">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-semibold" id="general-tab" data-bs-toggle="tab" data-bs-target="#general-pane" type="button" role="tab" style="font-size:13.5px;border-radius:8px;padding:8px 16px">
            <i class="ti ti-settings me-1"></i> Barangay Configuration
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-semibold" id="demographics-tab" data-bs-toggle="tab" data-bs-target="#demographics-pane" type="button" role="tab" style="font-size:13.5px;border-radius:8px;padding:8px 16px">
            <i class="ti ti-chart-pie me-1"></i> Demographics &amp; Age Brackets
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-semibold" id="permissions-tab" data-bs-toggle="tab" data-bs-target="#permissions-pane" type="button" role="tab" style="font-size:13.5px;border-radius:8px;padding:8px 16px">
            <i class="ti ti-shield-lock me-1"></i> Role Permissions Matrix
        </button>
    </li>
</ul>

<div class="tab-content" id="settingsTabContent">
    {{-- TAB 1: General & Barangay Configuration --}}
    <div class="tab-pane fade show active" id="general-pane" role="tabpanel">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="card-custom">
                                <h6 class="fw-semibold mb-3"><i class="ti ti-building-community me-1 text-primary"></i>Barangay information</h6>
                                <div class="mb-3"><label class="form-label">Barangay name</label><input type="text" name="barangay_name" class="form-control" value="{{ $settings['barangay_name'] ?? '' }}"></div>
                                <div class="mb-3"><label class="form-label">Address / Municipality</label><input type="text" name="barangay_address" class="form-control" value="{{ $settings['barangay_address'] ?? '' }}"></div>
                                <div class="mb-3"><label class="form-label">Contact number</label><input type="text" name="contact_number" class="form-control" value="{{ $settings['contact_number'] ?? '' }}"></div>
                                <div class="mb-0"><label class="form-label">Email address</label><input type="email" name="email" class="form-control" value="{{ $settings['email'] ?? '' }}"></div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="card-custom">
                                <h6 class="fw-semibold mb-3"><i class="ti ti-cpu me-1 text-primary"></i>System configuration</h6>
                                <div class="mb-3"><label class="form-label">System name</label><input type="text" name="system_name" class="form-control" value="{{ $settings['system_name'] ?? 'SmartBarangay' }}"></div>
                                <div class="mb-3"><label class="form-label">Report header</label><input type="text" name="report_header" class="form-control" value="{{ $settings['report_header'] ?? 'Republic of the Philippines' }}"></div>
                                <div class="mb-3"><label class="form-label">Barangay captain</label><input type="text" name="captain_name" class="form-control" value="{{ $settings['captain_name'] ?? '' }}"></div>
                                <div class="mb-0"><label class="form-label">Fiscal year</label><select name="fiscal_year" class="form-select"><option value="{{ date('Y') }}" {{ ($settings['fiscal_year'] ?? '') == date('Y') ? 'selected' : '' }}>{{ date('Y') }}</option><option value="{{ date('Y')-1 }}" {{ ($settings['fiscal_year'] ?? '') == date('Y')-1 ? 'selected' : '' }}>{{ date('Y')-1 }}</option></select></div>
                            </div>
                        </div>

                        {{-- System Maintenance Mode — STRICTLY for System Administrator only (excluded for Captain & Staff) --}}
                        @if(auth()->user()->isAdmin())
                        <div class="col-12">
                            <div class="card-custom border {{ ($settings['maintenance_mode'] ?? '0') == '1' ? 'border-warning' : 'border-light' }}" style="{{ ($settings['maintenance_mode'] ?? '0') == '1' ? 'background:#fffbf0' : '' }}">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                    <div>
                                        <h6 class="fw-bold mb-1 d-flex align-items-center gap-2">
                                            <i class="ti ti-tool {{ ($settings['maintenance_mode'] ?? '0') == '1' ? 'text-warning' : 'text-muted' }}" style="font-size:18px"></i>
                                            System Maintenance Mode
                                            <span class="badge bg-secondary" style="font-size:10px">Admin Only</span>
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
                        @endif
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-navy"><i class="ti ti-device-floppy me-1"></i>Save Configuration</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- TAB 2: Demographics & Age Brackets --}}
    <div class="tab-pane fade" id="demographics-pane" role="tabpanel">
        <div class="card-custom mb-3 p-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold mb-1"><i class="ti ti-chart-pie text-primary me-2"></i>Demographic Age Brackets</h5>
                    <div class="text-muted" style="font-size:13px">
                        Configure the age groups and demographic categorization displayed in the <strong>Barangay Kagawad (Councilor) Dashboard</strong>, Population Reports, and Analytics.
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <form method="POST" action="{{ route('admin.settings.brackets.reset') }}" onsubmit="return confirm('Reset demographic age brackets to standard defaults?');">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary btn-sm">
                            <i class="ti ti-refresh me-1"></i>Reset to Defaults
                        </button>
                    </form>
                    <button type="submit" form="bracketsForm" class="btn btn-navy btn-sm">
                        <i class="ti ti-device-floppy me-1"></i>Save Age Brackets
                    </button>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.brackets') }}" id="bracketsForm">
            @csrf @method('PUT')
            
            <div class="row g-3">
                @foreach($ageBrackets as $cIdx => $category)
                <div class="col-12">
                    <div class="card-custom">
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-navy px-2 py-1" style="font-size:12px">Category {{ $cIdx + 1 }}</span>
                                <input type="text" name="categories[{{ $cIdx }}][category]" class="form-control form-control-sm fw-bold" style="width:250px;font-size:14px" value="{{ $category['category'] ?? '' }}" required>
                            </div>
                            <span class="text-muted small">{{ count($category['brackets'] ?? []) }} Brackets</span>
                        </div>

                        <div class="table-responsive-custom">
                            <table class="table-custom align-middle">
                                <thead>
                                    <tr style="background:#f8fafc">
                                        <th style="width:15%">Min Age</th>
                                        <th style="width:15%">Max Age</th>
                                        <th style="width:70%">Demographic Description / Label</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($category['brackets'] ?? [] as $bIdx => $b)
                                    <tr>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <input type="number" min="0" max="150" name="categories[{{ $cIdx }}][brackets][{{ $bIdx }}][min]" class="form-control form-control-sm" value="{{ $b['min'] ?? 0 }}" required>
                                                <span class="input-group-text">yrs</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <input type="number" min="0" max="150" name="categories[{{ $cIdx }}][brackets][{{ $bIdx }}][max]" class="form-control form-control-sm" value="{{ $b['max'] !== null && $b['max'] !== '' ? $b['max'] : '' }}" placeholder="and over">
                                                <span class="input-group-text">yrs</span>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" name="categories[{{ $cIdx }}][brackets][{{ $bIdx }}][label]" class="form-control form-control-sm" value="{{ $b['label'] ?? '' }}" placeholder="e.g. Children (Infancy, Toddlerhood...)" required>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="card-custom mt-3 d-flex justify-content-between align-items-center p-3 flex-wrap gap-2">
                <div class="text-muted small">
                    <i class="ti ti-info-circle me-1 text-primary"></i> Leaving <strong>Max Age</strong> empty treats the bracket as <em>"and over"</em> (e.g. 65 and over for Senior Citizens).
                </div>
                <button type="submit" class="btn btn-navy">
                    <i class="ti ti-device-floppy me-1"></i>Save Age Brackets
                </button>
            </div>
        </form>
    </div>

    {{-- TAB 3: Role Permissions Matrix --}}
    <div class="tab-pane fade" id="permissions-pane" role="tabpanel">
        <div class="card-custom mb-3 p-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold mb-1"><i class="ti ti-shield-check text-success me-2"></i>Role Capabilities & Access Matrix</h5>
                    <div class="text-muted" style="font-size:13px">
                        Configure which pages, modules, and operational action buttons are available to each role in real-time.
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <form method="POST" action="{{ route('admin.settings.permissions.reset') }}" onsubmit="return confirm('Reset all role permissions to standard default configuration?');">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary btn-sm">
                            <i class="ti ti-refresh me-1"></i>Reset to Defaults
                        </button>
                    </form>
                    <button type="submit" form="permissionsMatrixForm" class="btn btn-navy btn-sm">
                        <i class="ti ti-device-floppy me-1"></i>Save Permissions
                    </button>
                </div>
            </div>
            
            <div class="alert alert-info d-flex align-items-center gap-2 py-2 mt-3 mb-0" style="font-size:12.5px">
                <i class="ti ti-info-circle fs-5"></i>
                <div>
                    <strong>Super-Admin Immunity:</strong> 👑 <strong>Punong Barangay</strong> and ⚙️ <strong>System Administrator</strong> accounts retain permanent 100% full access to all features and pages to prevent lockouts.
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.permissions') }}" id="permissionsMatrixForm">
            @csrf @method('PUT')
            
            <div class="table-responsive-custom">
                <table class="table-custom align-middle">
                    <thead>
                        <tr style="background:#f1f5f9">
                            <th style="width:38%;font-size:12px;font-weight:700">Module &amp; Action Capability</th>
                            @foreach($managedRoles as $roleKey => $roleTitle)
                                <th class="text-center" style="font-size:12px;min-width:120px">
                                    <div class="fw-bold">{{ $roleTitle }}</div>
                                    <div class="mt-1 d-flex justify-content-center gap-1">
                                        <button type="button" class="btn btn-xs btn-outline-primary py-0 px-1" style="font-size:10px" onclick="toggleColumn('{{ $roleKey }}', true)">All</button>
                                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1" style="font-size:10px" onclick="toggleColumn('{{ $roleKey }}', false)">None</button>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permissionCatalog as $category => $perms)
                            <tr style="background:#f8fafc;border-top:2px solid #e2e8f0">
                                <td colspan="{{ count($managedRoles) + 1 }}" class="fw-bold text-uppercase py-2" style="font-size:11.5px;color:#185fa5;letter-spacing:0.5px">
                                    <i class="ti ti-folder me-1"></i> {{ $category }}
                                </td>
                            </tr>
                            @foreach($perms as $permKey => $permLabel)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-medium" style="font-size:13px">{{ $permLabel }}</div>
                                        <div class="text-muted" style="font-family:monospace;font-size:11px">{{ $permKey }}</div>
                                    </td>
                                    @foreach($managedRoles as $roleKey => $roleTitle)
                                        @php
                                            $isChecked = !empty($permissionsMatrix[$roleKey][$permKey]);
                                        @endphp
                                        <td class="text-center">
                                            <div class="form-check form-check-inline m-0">
                                                <input class="form-check-input perm-check perm-role-{{ $roleKey }}" 
                                                    type="checkbox" 
                                                    name="permissions[{{ $roleKey }}][{{ $permKey }}]" 
                                                    value="1" 
                                                    {{ $isChecked ? 'checked' : '' }} 
                                                    style="cursor:pointer;width:1.25em;height:1.25em">
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-custom mt-3 d-flex justify-content-between align-items-center p-3 flex-wrap gap-2">
                <div class="text-muted small">
                    <i class="ti ti-check me-1 text-success"></i> Changes take effect immediately across all active user sessions upon saving.
                </div>
                <button type="submit" class="btn btn-navy">
                    <i class="ti ti-device-floppy me-1"></i>Save Permissions Matrix
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
@if(auth()->user()->isAdmin())
const maintenanceSwitch = document.getElementById('maintenanceSwitch');
if (maintenanceSwitch) {
    maintenanceSwitch.addEventListener('change', function() {
        this.nextElementSibling.textContent = this.checked ? 'ON' : 'OFF';
    });
}
@endif

function toggleColumn(role, state) {
    document.querySelectorAll('.perm-role-' + role).forEach(function(cb) {
        cb.checked = state;
    });
}
</script>
@endpush
@endsection
