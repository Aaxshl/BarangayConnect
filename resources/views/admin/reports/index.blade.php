@extends('layouts.admin')
@section('title','Reports')
@section('page-title','Reports & Analytics')
@section('content')

{{-- KPI Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card-custom text-center py-3">
            <div style="font-size:28px;font-weight:800;color:#185fa5">{{ number_format($total_residents) }}</div>
            <div class="text-muted small mt-1"><i class="ti ti-users me-1"></i>Total Residents</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-custom text-center py-3">
            <div style="font-size:28px;font-weight:800;color:#0d9488">{{ number_format($total_docs) }}</div>
            <div class="text-muted small mt-1"><i class="ti ti-file-text me-1"></i>Documents Issued</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-custom text-center py-3">
            <div style="font-size:28px;font-weight:800;color:#7c3aed">
                {{ $total_requests > 0 ? round(($resolved_requests/$total_requests)*100,1) : 0 }}%
            </div>
            <div class="text-muted small mt-1"><i class="ti ti-check me-1"></i>Resolution Rate</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-custom text-center py-3">
            <div style="font-size:28px;font-weight:800;color:#d97706">{{ $avg_resolve_days ? round($avg_resolve_days,1).'d' : '—' }}</div>
            <div class="text-muted small mt-1"><i class="ti ti-clock me-1"></i>Avg. Resolve Time</div>
        </div>
    </div>
</div>

{{-- Tabs --}}
<ul class="nav nav-tabs mb-3" id="reportTabs">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-population">Population</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-documents">Documents</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-requests">Requests &amp; Issues</button></li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-exports">
            <i class="ti ti-download me-1"></i>Export Hub
        </button>
    </li>
</ul>

<div class="tab-content">
    {{-- Population Tab --}}
    <div class="tab-pane fade show active" id="tab-population">
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <div class="card-custom">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">Population Summary</h6>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.reports.export',['type'=>'residents','format'=>'pdf']) }}" class="btn btn-outline-secondary btn-sm py-1 px-2" title="Export PDF">
                                <i class="ti ti-file-type-pdf me-1 text-danger"></i>PDF
                            </a>
                            <a href="{{ route('admin.reports.export',['type'=>'residents','format'=>'excel']) }}" class="btn btn-outline-secondary btn-sm py-1 px-2" title="Export Excel">
                                <i class="ti ti-file-spreadsheet me-1 text-success"></i>Excel
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0" style="font-size:13.5px">
                            <tbody>
                                <tr><td class="text-muted">Total Registered</td><td class="fw-semibold text-end">{{ number_format($total_residents) }}</td></tr>
                                <tr><td class="text-muted">Active Residents</td><td class="fw-semibold text-end" style="color:#15803d">{{ number_format(\App\Models\Resident::where('status','active')->count()) }}</td></tr>
                                <tr><td class="text-muted">Inactive / Archived</td><td class="fw-semibold text-end text-muted">{{ number_format(\App\Models\Resident::where('status','inactive')->count()) }}</td></tr>
                                <tr><td class="text-muted">Male</td><td class="fw-semibold text-end">{{ number_format($male_count) }} <span class="text-muted">({{ $total_residents > 0 ? round(($male_count/$total_residents)*100) : 0 }}%)</span></td></tr>
                                <tr><td class="text-muted">Female</td><td class="fw-semibold text-end">{{ number_format($female_count) }} <span class="text-muted">({{ $total_residents > 0 ? round(($female_count/$total_residents)*100) : 0 }}%)</span></td></tr>
                                <tr><td class="text-muted">Minors (Under 18)</td><td class="fw-semibold text-end">{{ number_format($minor_count) }}</td></tr>
                                <tr><td class="text-muted">Senior Citizens (60+)</td><td class="fw-semibold text-end">{{ number_format($senior_count) }}</td></tr>
                                <tr><td class="text-muted">Total Households</td><td class="fw-semibold text-end">{{ number_format($total_households) }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card-custom">
                    <h6 class="fw-bold mb-3">Gender Distribution</h6>
                    @if($total_residents > 0)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small text-muted mb-1"><span>Male</span><span>{{ number_format($male_count) }}</span></div>
                        <div class="progress" style="height:14px;border-radius:8px">
                            <div class="progress-bar" role="progressbar" style="width:{{ round(($male_count/$total_residents)*100) }}%;background:#185fa5;border-radius:8px" title="Male"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small text-muted mb-1"><span>Female</span><span>{{ number_format($female_count) }}</span></div>
                        <div class="progress" style="height:14px;border-radius:8px">
                            <div class="progress-bar" role="progressbar" style="width:{{ round(($female_count/$total_residents)*100) }}%;background:#e91e8c;border-radius:8px" title="Female"></div>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between small text-muted mb-1"><span>Minors (0–17)</span><span>{{ number_format($minor_count) }}</span></div>
                        <div class="progress" style="height:10px;border-radius:8px">
                            <div class="progress-bar bg-warning" role="progressbar" style="width:{{ $total_residents > 0 ? round(($minor_count/$total_residents)*100) : 0 }}%;border-radius:8px"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between small text-muted mb-1"><span>Senior Citizens (60+)</span><span>{{ number_format($senior_count) }}</span></div>
                        <div class="progress" style="height:10px;border-radius:8px">
                            <div class="progress-bar bg-success" role="progressbar" style="width:{{ $total_residents > 0 ? round(($senior_count/$total_residents)*100) : 0 }}%;border-radius:8px"></div>
                        </div>
                    </div>
                    @else
                    <p class="text-muted text-center py-4">No resident data available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Documents Tab --}}
    <div class="tab-pane fade" id="tab-documents">
        <div class="row g-3">
            <div class="col-12">
                <div class="card-custom">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">Document Issuance by Type</h6>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.reports.export',['type'=>'documents','format'=>'pdf']) }}" class="btn btn-outline-secondary btn-sm py-1 px-2">
                                <i class="ti ti-file-type-pdf me-1 text-danger"></i>Export PDF
                            </a>
                            <a href="{{ route('admin.reports.export',['type'=>'documents','format'=>'excel']) }}" class="btn btn-outline-secondary btn-sm py-1 px-2">
                                <i class="ti ti-file-spreadsheet me-1 text-success"></i>Export Excel
                            </a>
                        </div>
                    </div>
                    @foreach($doc_by_type as $type => $count)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="fw-medium">{{ \App\Models\Document::TYPES[$type] ?? $type }}</span>
                            <span class="text-muted">{{ $count }} issued</span>
                        </div>
                        <div class="progress" style="height:12px;border-radius:8px;background:#e8eef4">
                            <div class="progress-bar" style="width:{{ $doc_by_type->max() > 0 ? round(($count/$doc_by_type->max())*100) : 0 }}%;background:#185fa5;border-radius:8px;transition:width .6s"></div>
                        </div>
                    </div>
                    @endforeach
                    @if($doc_by_type->isEmpty())
                    <p class="text-muted text-center py-4">No documents issued yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Requests Tab --}}
    <div class="tab-pane fade" id="tab-requests">
        <div class="row g-3">
            <div class="col-12 col-md-5">
                <div class="card-custom">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">Request Summary</h6>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.reports.export',['type'=>'requests','format'=>'pdf']) }}" class="btn btn-outline-secondary btn-sm py-1 px-2">
                                <i class="ti ti-file-type-pdf me-1 text-danger"></i>PDF
                            </a>
                            <a href="{{ route('admin.reports.export',['type'=>'requests','format'=>'excel']) }}" class="btn btn-outline-secondary btn-sm py-1 px-2">
                                <i class="ti ti-file-spreadsheet me-1 text-success"></i>Excel
                            </a>
                        </div>
                    </div>
                    <table class="table table-sm table-hover mb-0" style="font-size:13.5px">
                        <tbody>
                            <tr><td class="text-muted">Total Submitted</td><td class="fw-semibold text-end">{{ number_format($total_requests) }}</td></tr>
                            <tr><td class="text-muted">Resolved</td><td class="fw-semibold text-end" style="color:#15803d">{{ number_format($resolved_requests) }}</td></tr>
                            <tr><td class="text-muted">Pending</td><td class="fw-semibold text-end" style="color:#d97706">{{ number_format($pending_requests) }}</td></tr>
                            <tr><td class="text-muted">Resolution Rate</td><td class="fw-semibold text-end">{{ $total_requests > 0 ? round(($resolved_requests/$total_requests)*100,1) : 0 }}%</td></tr>
                            <tr><td class="text-muted">Avg. Resolution Time</td><td class="fw-semibold text-end">{{ $avg_resolve_days ? round($avg_resolve_days,1).' days' : 'N/A' }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-12 col-md-7">
                <div class="card-custom">
                    <h6 class="fw-bold mb-3">Top Community Issues Reported</h6>
                    @foreach($issues_by_type as $issue)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="fw-medium">{{ ucwords(str_replace('_',' ',$issue->request_type)) }}</span>
                            <span class="text-muted">{{ $issue->total }} reports</span>
                        </div>
                        <div class="progress" style="height:12px;border-radius:8px;background:#e8eef4">
                            <div class="progress-bar bg-danger" style="width:{{ $issues_by_type->max('total') > 0 ? round(($issue->total/$issues_by_type->max('total'))*100) : 0 }}%;border-radius:8px;transition:width .6s"></div>
                        </div>
                    </div>
                    @endforeach
                    @if($issues_by_type->isEmpty())
                    <p class="text-muted text-center py-4">No community issues reported yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Export Hub Tab --}}
    <div class="tab-pane fade" id="tab-exports">
        {{-- Bulk ZIP Export Card --}}
        <div class="card-custom mb-4" style="border: 1px solid #c7d2fe; background: linear-gradient(to right, #f8faff, #ffffff);">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                <div>
                    <h5 class="fw-bold mb-1 text-primary"><i class="ti ti-package me-2"></i>Batch Export &amp; Download Archive (.ZIP)</h5>
                    <p class="text-muted small mb-0">Select the report categories you need and download them all bundled in a single ZIP archive.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.reports.export.zip') }}">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-12 col-lg-8">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold small text-muted text-uppercase" style="letter-spacing:0.5px">Select Reports to Include</span>
                            <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none" id="toggleAllBtn" style="font-size:12px">Select All</button>
                        </div>
                        <div class="row g-2">
                            <div class="col-12 col-sm-6">
                                <div class="p-2 border rounded bg-white">
                                    <div class="form-check mb-0">
                                        <input class="form-check-input report-checkbox" type="checkbox" name="reports[]" value="residents" id="chk_residents" checked>
                                        <label class="form-check-label fw-medium" for="chk_residents" style="font-size:13px">
                                            <i class="ti ti-users me-1 text-primary"></i>Residents Masterlist
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="p-2 border rounded bg-white">
                                    <div class="form-check mb-0">
                                        <input class="form-check-input report-checkbox" type="checkbox" name="reports[]" value="documents" id="chk_documents" checked>
                                        <label class="form-check-label fw-medium" for="chk_documents" style="font-size:13px">
                                            <i class="ti ti-file-certificate me-1 text-teal"></i>Document Issuances
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="p-2 border rounded bg-white">
                                    <div class="form-check mb-0">
                                        <input class="form-check-input report-checkbox" type="checkbox" name="reports[]" value="requests" id="chk_requests" checked>
                                        <label class="form-check-label fw-medium" for="chk_requests" style="font-size:13px">
                                            <i class="ti ti-message-report me-1 text-warning"></i>Citizen Complaints
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="p-2 border rounded bg-white">
                                    <div class="form-check mb-0">
                                        <input class="form-check-input report-checkbox" type="checkbox" name="reports[]" value="services" id="chk_services" checked>
                                        <label class="form-check-label fw-medium" for="chk_services" style="font-size:13px">
                                            <i class="ti ti-clipboard-list me-1 text-indigo"></i>Service Logs &amp; Blotter
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="p-2 border rounded bg-white">
                                    <div class="form-check mb-0">
                                        <input class="form-check-input report-checkbox" type="checkbox" name="reports[]" value="households" id="chk_households" checked>
                                        <label class="form-check-label fw-medium" for="chk_households" style="font-size:13px">
                                            <i class="ti ti-home me-1 text-purple"></i>Households Profiling
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-4">
                        <div class="mb-2">
                            <span class="fw-semibold small text-muted text-uppercase" style="letter-spacing:0.5px">Archive Format</span>
                        </div>
                        <div class="d-flex flex-column gap-2">
                            <div class="p-2 border rounded bg-white">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="radio" name="format" id="fmt_both" value="both" checked>
                                    <label class="form-check-label fw-medium" for="fmt_both" style="font-size:13px">
                                        <i class="ti ti-folders me-1 text-primary"></i>Complete Package (PDF + Excel)
                                    </label>
                                </div>
                            </div>
                            <div class="p-2 border rounded bg-white">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="radio" name="format" id="fmt_pdf" value="pdf">
                                    <label class="form-check-label fw-medium" for="fmt_pdf" style="font-size:13px">
                                        <i class="ti ti-file-type-pdf me-1 text-danger"></i>PDF Documents Only (.pdf)
                                    </label>
                                </div>
                            </div>
                            <div class="p-2 border rounded bg-white">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="radio" name="format" id="fmt_excel" value="excel">
                                    <label class="form-check-label fw-medium" for="fmt_excel" style="font-size:13px">
                                        <i class="ti ti-file-spreadsheet me-1 text-success"></i>Excel Spreadsheets Only (.csv)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span class="small text-muted"><i class="ti ti-info-circle me-1"></i>Generates a consolidated ZIP package with official letterheads and formatted spreadsheets.</span>
                    <button type="submit" class="btn btn-navy">
                        <i class="ti ti-download me-1"></i>Download Archive (.ZIP)
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('toggleAllBtn')?.addEventListener('click', function() {
    const checkboxes = document.querySelectorAll('.report-checkbox');
    const allChecked = Array.from(checkboxes).every(c => c.checked);
    checkboxes.forEach(c => c.checked = !allChecked);
    this.textContent = allChecked ? 'Select All' : 'Deselect All';
});
</script>
@endpush
@endsection
