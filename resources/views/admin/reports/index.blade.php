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
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-requests">Requests</button></li>
</ul>

<div class="tab-content">
    {{-- Population Tab --}}
    <div class="tab-pane fade show active" id="tab-population">
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <div class="card-custom">
                    <h6 class="fw-bold mb-3">Population Summary</h6>
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
                        <a href="{{ route('admin.reports.export',['type'=>'documents','format'=>'pdf']) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="ti ti-file-type-pdf me-1"></i>Export PDF
                        </a>
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
                    <h6 class="fw-bold mb-3">Request Resolution Summary</h6>
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
</div>

<div class="d-flex gap-2 mt-3">
    <a href="{{ route('admin.reports.export',['type'=>'residents','format'=>'pdf']) }}" class="btn btn-outline-secondary btn-sm"><i class="ti ti-file-type-pdf me-1"></i>Export Residents PDF</a>
    <a href="{{ route('admin.reports.export',['type'=>'residents','format'=>'excel']) }}" class="btn btn-outline-secondary btn-sm"><i class="ti ti-file-spreadsheet me-1"></i>Export Residents Excel</a>
</div>
@endsection
