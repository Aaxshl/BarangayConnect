@extends('layouts.admin')
@section('title','Reports')
@section('page-title','Reports & Analytics')
@section('content')

{{-- KPI Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3 col-xl">
        <div class="card-custom text-center py-3">
            <div style="font-size:28px;font-weight:800;color:#185fa5">{{ number_format($total_residents) }}</div>
            <div class="text-muted small mt-1"><i class="ti ti-users me-1"></i>Total Residents</div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-xl">
        <div class="card-custom text-center py-3">
            <div style="font-size:28px;font-weight:800;color:#0d9488">{{ number_format($total_docs) }}</div>
            <div class="text-muted small mt-1"><i class="ti ti-file-text me-1"></i>Documents Issued</div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-xl">
        <div class="card-custom text-center py-3">
            <div style="font-size:28px;font-weight:800;color:#7c3aed">
                {{ $total_requests > 0 ? round(($resolved_requests/$total_requests)*100,1) : 0 }}%
            </div>
            <div class="text-muted small mt-1"><i class="ti ti-check me-1"></i>Resolution Rate</div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-xl">
        <div class="card-custom text-center py-3">
            <div style="font-size:28px;font-weight:800;color:#d97706">{{ $avg_resolve_days ? round($avg_resolve_days,1).'d' : '—' }}</div>
            <div class="text-muted small mt-1"><i class="ti ti-clock me-1"></i>Avg. Resolve Time</div>
        </div>
    </div>
    @if(auth()->user()->canDo('reports.revenue'))
    <div class="col-6 col-md-3 col-xl">
        <div class="card-custom text-center py-3" style="border-left:3px solid #16a34a">
            <div style="font-size:28px;font-weight:800;color:#16a34a">₱{{ number_format($revenue_this_month, 0) }}</div>
            <div class="text-muted small mt-1">
                <i class="ti ti-cash me-1"></i>Revenue (This Month)
                @if($revenue_last_month > 0)
                    @php $change = (($revenue_this_month - $revenue_last_month) / $revenue_last_month) * 100; @endphp
                    <span class="{{ $change >= 0 ? 'text-success' : 'text-danger' }}" style="font-size:10.5px">
                        <i class="ti ti-trending-{{ $change >= 0 ? 'up' : 'down' }}"></i>
                        {{ $change >= 0 ? '+' : '' }}{{ round($change, 1) }}%
                    </span>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

{{-- Tabs --}}
<ul class="nav nav-tabs mb-3" id="reportTabs">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-population">Population</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-documents">Documents</button></li>
    @if(auth()->user()->canDo('reports.revenue'))
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-revenue">
            <i class="ti ti-cash me-1"></i>Revenue & Finance
        </button>
    </li>
    @endif
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-requests">Requests & Issues</button></li>
    @if(auth()->user()->canDo('reports.export_zip') || auth()->user()->canDo('reports.export_single'))
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-exports">
            <i class="ti ti-download me-1"></i>Export Hub
        </button>
    </li>
    @endif
</ul>

<div class="tab-content">
    {{-- Population Tab --}}
    <div class="tab-pane fade show active" id="tab-population">
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <div class="card-custom">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">Population Summary</h6>
                        @if(auth()->user()->canDo('reports.export_single'))
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.reports.export',['type'=>'residents','format'=>'pdf']) }}" class="btn btn-outline-secondary btn-sm py-1 px-2" title="Export PDF">
                                <i class="ti ti-file-type-pdf me-1 text-danger"></i>PDF
                            </a>
                            <a href="{{ route('admin.reports.export',['type'=>'residents','format'=>'excel']) }}" class="btn btn-outline-secondary btn-sm py-1 px-2" title="Export Excel">
                                <i class="ti ti-file-spreadsheet me-1 text-success"></i>Excel
                            </a>
                        </div>
                        @endif
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

    {{-- Documents Tab (Enhanced with Processing Time & Staff Productivity) --}}
    <div class="tab-pane fade" id="tab-documents">
        <div class="row g-3">
            {{-- Document Issuance by Type --}}
            <div class="col-12 col-md-6">
                <div class="card-custom">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">Document Issuance by Type</h6>
                        @if(auth()->user()->canDo('reports.export_single'))
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.reports.export',['type'=>'documents','format'=>'pdf']) }}" class="btn btn-outline-secondary btn-sm py-1 px-2">
                                <i class="ti ti-file-type-pdf me-1 text-danger"></i>PDF
                            </a>
                            <a href="{{ route('admin.reports.export',['type'=>'documents','format'=>'excel']) }}" class="btn btn-outline-secondary btn-sm py-1 px-2">
                                <i class="ti ti-file-spreadsheet me-1 text-success"></i>Excel
                            </a>
                        </div>
                        @endif
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

            {{-- Processing Time Analytics --}}
            <div class="col-12 col-md-6">
                <div class="card-custom">
                    <h6 class="fw-bold mb-1"><i class="ti ti-clock-hour-4 me-1 text-primary"></i>Processing Time Analytics</h6>
                    <p class="text-muted small mb-3">Average days from request to document release.</p>

                    {{-- Overall avg --}}
                    <div class="p-3 rounded mb-3 text-center" style="background:linear-gradient(135deg,#eff6ff,#f0fdf4);border:1px solid #bfdbfe">
                        <div style="font-size:32px;font-weight:800;color:#185fa5">
                            {{ $avg_processing_days ? $avg_processing_days . ' days' : '—' }}
                        </div>
                        <div class="text-muted small">Overall Average Processing Time</div>
                    </div>

                    {{-- Per document type --}}
                    @forelse($processing_by_type as $pt)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="fw-medium">{{ \App\Models\Document::TYPES[$pt->document_type] ?? $pt->document_type }}</span>
                            <span>
                                <span class="fw-bold" style="color:#185fa5">{{ round($pt->avg_days, 1) }}d</span>
                                <span class="text-muted ms-1">({{ $pt->total }} released)</span>
                            </span>
                        </div>
                        <div class="progress" style="height:10px;border-radius:8px;background:#e8eef4">
                            @php $maxDays = $processing_by_type->max('avg_days') ?: 1; @endphp
                            <div class="progress-bar" style="width:{{ round(($pt->avg_days / $maxDays)*100) }}%;background:{{ $pt->avg_days <= 2 ? '#16a34a' : ($pt->avg_days <= 5 ? '#d97706' : '#dc2626') }};border-radius:8px"></div>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center py-3 small">No released documents to analyze yet.</p>
                    @endforelse

                    @if($processing_by_type->isNotEmpty())
                    <div class="d-flex gap-3 mt-2 pt-2 border-top small text-muted">
                        <span><span class="d-inline-block rounded-circle me-1" style="width:8px;height:8px;background:#16a34a"></span>≤ 2 days (Fast)</span>
                        <span><span class="d-inline-block rounded-circle me-1" style="width:8px;height:8px;background:#d97706"></span>3–5 days</span>
                        <span><span class="d-inline-block rounded-circle me-1" style="width:8px;height:8px;background:#dc2626"></span>> 5 days (Slow)</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Staff Productivity --}}
            <div class="col-12">
                <div class="card-custom">
                    <h6 class="fw-bold mb-1"><i class="ti ti-chart-bar me-1 text-primary"></i>Staff Productivity Metrics</h6>
                    <p class="text-muted small mb-3">Document processing and payment verification activity per staff member.</p>

                    @if($staff_productivity->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0" style="font-size:13px">
                            <thead>
                                <tr style="background:#f1f5f9">
                                    <th>Staff Member</th>
                                    <th>Role</th>
                                    <th class="text-center">Docs Processed</th>
                                    <th class="text-center">Docs Released</th>
                                    <th class="text-center">Avg. Processing</th>
                                    <th class="text-center">Payments Verified</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($staff_productivity as $idx => $staff)
                                <tr>
                                    <td class="fw-semibold">
                                        {{ $staff['name'] }}
                                        @if($idx === 0 && $staff['processed'] > 0)
                                            <span class="badge bg-warning text-dark ms-1" style="font-size:9px"><i class="ti ti-star-filled me-1"></i>Top</span>
                                        @endif
                                    </td>
                                    <td class="text-muted">{{ $staff['role'] }}</td>
                                    <td class="text-center fw-semibold">{{ $staff['processed'] }}</td>
                                    <td class="text-center">
                                        <span class="fw-semibold" style="color:#16a34a">{{ $staff['released'] }}</span>
                                        @if($staff['processed'] > 0)
                                        <span class="text-muted small">({{ round(($staff['released']/$staff['processed'])*100) }}%)</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($staff['avg_days'])
                                            <span class="badge {{ $staff['avg_days'] <= 2 ? 'bg-success' : ($staff['avg_days'] <= 5 ? 'bg-warning text-dark' : 'bg-danger') }}" style="font-size:11px">
                                                {{ $staff['avg_days'] }} days
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center fw-semibold" style="color:#185fa5">{{ $staff['payments_verified'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted text-center py-4">No staff activity recorded yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════ REVENUE & FINANCE TAB ══════════ --}}
    @if(auth()->user()->canDo('reports.revenue'))
    <div class="tab-pane fade" id="tab-revenue">
        <div class="row g-3">
            {{-- Revenue Summary Cards --}}
            <div class="col-12">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="card-custom text-center py-3" style="border-left:3px solid #16a34a">
                            <div style="font-size:24px;font-weight:800;color:#16a34a">₱{{ number_format($revenue_total, 2) }}</div>
                            <div class="text-muted small mt-1"><i class="ti ti-coins me-1"></i>Total Revenue Collected</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card-custom text-center py-3" style="border-left:3px solid #2563eb">
                            <div style="font-size:24px;font-weight:800;color:#2563eb">₱{{ number_format($revenue_cash, 2) }}</div>
                            <div class="text-muted small mt-1"><i class="ti ti-cash me-1"></i>Cash Payments</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card-custom text-center py-3" style="border-left:3px solid #0891b2">
                            <div style="font-size:24px;font-weight:800;color:#0891b2">₱{{ number_format($revenue_gcash, 2) }}</div>
                            <div class="text-muted small mt-1"><i class="ti ti-device-mobile me-1"></i>GCash Payments</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card-custom text-center py-3" style="border-left:3px solid #d97706">
                            <div style="font-size:24px;font-weight:800;color:#d97706">₱{{ number_format($waived_total, 2) }}</div>
                            <div class="text-muted small mt-1"><i class="ti ti-receipt-off me-1"></i>Waived / Exempted</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Outstanding Payments Alert --}}
            @if($pending_payments > 0 || $unpaid_documents > 0)
            <div class="col-12">
                <div class="alert alert-warning py-2 px-3 d-flex justify-content-between align-items-center flex-wrap gap-2 mb-0" style="border-radius:10px;font-size:13px">
                    <div>
                        <i class="ti ti-alert-triangle me-1"></i>
                        <strong>Outstanding Payments:</strong>
                        @if($pending_payments > 0)
                            <span class="badge bg-info text-dark ms-1">{{ $pending_payments }} awaiting verification</span>
                        @endif
                        @if($unpaid_documents > 0)
                            <span class="badge bg-warning text-dark ms-1">{{ $unpaid_documents }} unpaid</span>
                        @endif
                    </div>
                    <a href="{{ route('admin.documents.index') }}?status=pending" class="btn btn-outline-warning btn-sm py-1 px-2" style="font-size:12px">
                        <i class="ti ti-external-link me-1"></i>View Documents
                    </a>
                </div>
            </div>
            @endif

            {{-- Cash vs GCash Breakdown --}}
            <div class="col-12 col-md-5">
                <div class="card-custom">
                    <h6 class="fw-bold mb-3"><i class="ti ti-chart-pie me-1 text-primary"></i>Payment Method Breakdown</h6>
                    @php $totalPaid = $revenue_cash + $revenue_gcash; @endphp
                    @if($totalPaid > 0)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span><i class="ti ti-cash me-1"></i>Cash (Over-the-Counter)</span>
                            <span class="fw-bold">₱{{ number_format($revenue_cash, 2) }} ({{ round(($revenue_cash/$totalPaid)*100) }}%)</span>
                        </div>
                        <div class="progress" style="height:16px;border-radius:8px">
                            <div class="progress-bar" style="width:{{ round(($revenue_cash/$totalPaid)*100) }}%;background:#2563eb;border-radius:8px"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span><i class="ti ti-device-mobile me-1"></i>GCash / Online Transfer</span>
                            <span class="fw-bold">₱{{ number_format($revenue_gcash, 2) }} ({{ round(($revenue_gcash/$totalPaid)*100) }}%)</span>
                        </div>
                        <div class="progress" style="height:16px;border-radius:8px">
                            <div class="progress-bar" style="width:{{ round(($revenue_gcash/$totalPaid)*100) }}%;background:#0891b2;border-radius:8px"></div>
                        </div>
                    </div>
                    @if($waived_total > 0)
                    <div class="mb-0">
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span><i class="ti ti-receipt-off me-1"></i>Waived / Exempted</span>
                            <span class="fw-bold">₱{{ number_format($waived_total, 2) }}</span>
                        </div>
                        <div class="progress" style="height:10px;border-radius:8px;background:#fef3c7">
                            <div class="progress-bar" style="width:100%;background:#d97706;border-radius:8px"></div>
                        </div>
                    </div>
                    @endif
                    @else
                    <p class="text-muted text-center py-4">No payments collected yet.</p>
                    @endif

                    {{-- Export Buttons --}}
                    @if(auth()->user()->canDo('reports.export_single'))
                    <div class="d-flex gap-2 mt-3 pt-3 border-top">
                        <a href="{{ route('admin.reports.revenue.export', ['format' => 'pdf']) }}" class="btn btn-outline-secondary btn-sm py-1 px-2">
                            <i class="ti ti-file-type-pdf me-1 text-danger"></i>Export PDF
                        </a>
                        <a href="{{ route('admin.reports.revenue.export', ['format' => 'csv']) }}" class="btn btn-outline-secondary btn-sm py-1 px-2">
                            <i class="ti ti-file-spreadsheet me-1 text-success"></i>Export CSV
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Revenue by Document Type --}}
            <div class="col-12 col-md-7">
                <div class="card-custom">
                    <h6 class="fw-bold mb-3"><i class="ti ti-receipt-2 me-1 text-primary"></i>Revenue by Document Type</h6>
                    @if($revenue_by_type->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0" style="font-size:13px">
                            <thead>
                                <tr style="background:#f1f5f9">
                                    <th>Document Type</th>
                                    <th class="text-center"># Issued</th>
                                    <th class="text-end">Total (₱)</th>
                                    <th class="text-end">Cash (₱)</th>
                                    <th class="text-end">GCash (₱)</th>
                                    <th class="text-end">Waived (₱)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($revenue_by_type as $rt)
                                <tr>
                                    <td class="fw-medium">{{ \App\Models\Document::TYPES[$rt->document_type] ?? $rt->document_type }}</td>
                                    <td class="text-center">{{ $rt->doc_count }}</td>
                                    <td class="text-end fw-bold" style="color:#16a34a">{{ number_format($rt->total_revenue, 2) }}</td>
                                    <td class="text-end">{{ number_format($rt->cash_revenue, 2) }}</td>
                                    <td class="text-end">{{ number_format($rt->gcash_revenue, 2) }}</td>
                                    <td class="text-end text-muted">{{ number_format($rt->waived_revenue, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="background:#f0fdf4;font-weight:bold;border-top:2px solid #16a34a">
                                    <td>Grand Total</td>
                                    <td class="text-center">{{ $revenue_by_type->sum('doc_count') }}</td>
                                    <td class="text-end" style="color:#16a34a">₱{{ number_format($revenue_by_type->sum('total_revenue'), 2) }}</td>
                                    <td class="text-end">₱{{ number_format($revenue_by_type->sum('cash_revenue'), 2) }}</td>
                                    <td class="text-end">₱{{ number_format($revenue_by_type->sum('gcash_revenue'), 2) }}</td>
                                    <td class="text-end text-muted">₱{{ number_format($revenue_by_type->sum('waived_revenue'), 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <p class="text-muted text-center py-4">No revenue data available.</p>
                    @endif
                </div>
            </div>

            {{-- Monthly Revenue Trend (CSS Bar Chart) --}}
            <div class="col-12">
                <div class="card-custom">
                    <h6 class="fw-bold mb-1"><i class="ti ti-chart-bar me-1 text-primary"></i>Monthly Revenue Trend (Last 12 Months)</h6>
                    <p class="text-muted small mb-3">Color-coded: <span class="badge bg-primary bg-opacity-75" style="font-size:10px">Cash</span> <span class="badge" style="font-size:10px;background:#0891b2;color:white">GCash</span> <span class="badge bg-warning text-dark" style="font-size:10px">Waived</span></p>

                    <div class="d-flex align-items-end gap-1" style="height:200px;padding-bottom:30px;position:relative">
                        {{-- Y-axis labels --}}
                        <div class="d-flex flex-column justify-content-between text-end text-muted pe-2" style="height:170px;font-size:10px;min-width:55px">
                            <span>₱{{ number_format($max_monthly_revenue, 0) }}</span>
                            <span>₱{{ number_format($max_monthly_revenue * 0.75, 0) }}</span>
                            <span>₱{{ number_format($max_monthly_revenue * 0.5, 0) }}</span>
                            <span>₱{{ number_format($max_monthly_revenue * 0.25, 0) }}</span>
                            <span>₱0</span>
                        </div>

                        {{-- Bars --}}
                        @foreach($revenue_by_month as $month)
                        @php
                            $barHeight = $max_monthly_revenue > 0 ? round(($month['total'] / $max_monthly_revenue) * 170) : 0;
                            $cashH = $month['total'] > 0 ? round(($month['cash'] / $month['total']) * $barHeight) : 0;
                            $gcashH = $month['total'] > 0 ? round(($month['gcash'] / $month['total']) * $barHeight) : 0;
                            $waivedH = max(0, $barHeight - $cashH - $gcashH);
                        @endphp
                        <div class="flex-fill d-flex flex-column align-items-center" style="min-width:0">
                            <div class="d-flex flex-column justify-content-end" style="height:170px;width:100%;max-width:50px">
                                @if($barHeight > 0)
                                @if($waivedH > 0)
                                <div style="height:{{ $waivedH }}px;background:#fbbf24;border-radius:{{ $gcashH == 0 && $cashH == 0 ? '4px 4px' : '0 0' }} 0 0;min-height:2px" title="Waived: ₱{{ number_format($month['waived'], 2) }}"></div>
                                @endif
                                @if($gcashH > 0)
                                <div style="height:{{ $gcashH }}px;background:#0891b2;min-height:2px" title="GCash: ₱{{ number_format($month['gcash'], 2) }}"></div>
                                @endif
                                @if($cashH > 0)
                                <div style="height:{{ $cashH }}px;background:#2563eb;border-radius:0 0 {{ $gcashH == 0 && $waivedH == 0 ? '4px 4px' : '0 0' }};min-height:2px" title="Cash: ₱{{ number_format($month['cash'], 2) }}"></div>
                                @endif
                                @else
                                <div style="height:2px;background:#e2e8f0;border-radius:2px;margin-top:auto"></div>
                                @endif
                            </div>
                            <div class="text-muted text-center mt-1" style="font-size:10px;white-space:nowrap">{{ $month['short'] }}</div>
                            @if($month['total'] > 0)
                            <div class="text-center fw-semibold" style="font-size:9px;color:#16a34a">₱{{ number_format($month['total'], 0) }}</div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Requests Tab --}}
    <div class="tab-pane fade" id="tab-requests">
        <div class="row g-3">
            <div class="col-12 col-md-5">
                <div class="card-custom">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">Request Summary</h6>
                        @if(auth()->user()->canDo('reports.export_single'))
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.reports.export',['type'=>'requests','format'=>'pdf']) }}" class="btn btn-outline-secondary btn-sm py-1 px-2">
                                <i class="ti ti-file-type-pdf me-1 text-danger"></i>PDF
                            </a>
                            <a href="{{ route('admin.reports.export',['type'=>'requests','format'=>'excel']) }}" class="btn btn-outline-secondary btn-sm py-1 px-2">
                                <i class="ti ti-file-spreadsheet me-1 text-success"></i>Excel
                            </a>
                        </div>
                        @endif
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
    @if(auth()->user()->canDo('reports.export_zip') || auth()->user()->canDo('reports.export_single'))
    <div class="tab-pane fade" id="tab-exports">
        {{-- Bulk ZIP Export Card --}}
        <div class="card-custom mb-4" style="border: 1px solid #c7d2fe; background: linear-gradient(to right, #f8faff, #ffffff);">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                <div>
                    <h5 class="fw-bold mb-1 text-primary"><i class="ti ti-package me-2"></i>Batch Export & Download Archive (.ZIP)</h5>
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
                                            <i class="ti ti-clipboard-list me-1 text-indigo"></i>Service Logs & Blotter
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
                            @if(auth()->user()->canDo('reports.revenue'))
                            <div class="col-12 col-sm-6">
                                <div class="p-2 border rounded bg-white" style="border-color:#16a34a !important">
                                    <div class="form-check mb-0">
                                        <input class="form-check-input report-checkbox" type="checkbox" name="reports[]" value="revenue" id="chk_revenue" checked>
                                        <label class="form-check-label fw-medium" for="chk_revenue" style="font-size:13px">
                                            <i class="ti ti-cash me-1 text-success"></i>Revenue & Financial Report
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @endif
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
    @endif
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
