@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- 1. Personalized Greeting Banner (All Roles) --}}
<div class="card-custom mb-3 p-3" style="background:linear-gradient(135deg, #1a3a6b 0%, #1e4b8a 100%);color:#fff;border:none">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
            <div style="width:48px;height:48px;border-radius:12px;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;font-size:24px">
                @if(auth()->user()->isCaptain())
                    👑
                @elseif(auth()->user()->isAdmin())
                    ⚙️
                @elseif(auth()->user()->isSecretary())
                    📋
                @elseif(auth()->user()->role === 'councilor')
                    🏛️
                @elseif(auth()->user()->isStaff())
                    🛡️
                @else
                    👋
                @endif
            </div>
            <div>
                <h5 class="fw-bold mb-1" style="color:#fff">{{ $banner['greeting'] }}</h5>
                <div style="font-size:13px;opacity:0.9">
                    <span class="badge bg-light text-dark fw-semibold me-2">{{ $banner['role_label'] }}</span>
                    <span><i class="ti ti-calendar me-1"></i>{{ $banner['date_str'] }}</span>
                </div>
            </div>
        </div>
        <div>
            @if(auth()->user()->isCaptain())
                <span class="badge" style="background:rgba(255,255,255,0.2);font-size:12px;padding:6px 12px">
                    <i class="ti ti-star me-1"></i> Executive Command Center
                </span>
            @elseif(auth()->user()->isAdmin())
                <span class="badge" style="background:rgba(255,255,255,0.2);font-size:12px;padding:6px 12px">
                    <i class="ti ti-cpu me-1"></i> System &amp; IT Management
                </span>
            @elseif(auth()->user()->isSecretary())
                <span class="badge" style="background:rgba(255,255,255,0.2);font-size:12px;padding:6px 12px">
                    <i class="ti ti-clipboard-list me-1"></i> Operations &amp; Issuance Center
                </span>
            @elseif(auth()->user()->role === 'councilor')
                <span class="badge" style="background:rgba(255,255,255,0.2);font-size:12px;padding:6px 12px">
                    <i class="ti ti-scale me-1"></i> Legislative Oversight &amp; Demographics
                </span>
            @elseif(auth()->user()->isStaff())
                <span class="badge" style="background:rgba(255,255,255,0.2);font-size:12px;padding:6px 12px">
                    <i class="ti ti-shield-check me-1"></i> Field Operations &amp; Task Queue
                </span>
            @endif
        </div>
    </div>
</div>

{{-- 2. ROLE-SPECIFIC DASHBOARD PANELS --}}

{{-- ======================================================== --}}
{{-- 👑 2A. PUNONG BARANGAY (CAPTAIN) EXECUTIVE COMMAND CENTER --}}
{{-- ======================================================== --}}
@if(auth()->user()->isCaptain())
<div class="row g-3 mb-3">
    <div class="col-6 col-md-4 col-lg">
        <div class="stat-card">
            <div class="stat-label"><i class="ti ti-users me-1 text-primary"></i>Total Residents</div>
            <div class="stat-value">{{ number_format($total_residents) }}</div>
            <div class="stat-sub">Active population</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <div class="stat-card">
            <div class="stat-label"><i class="ti ti-home me-1 text-teal"></i>Households</div>
            <div class="stat-value">{{ number_format($total_households) }}</div>
            <div class="stat-sub">Registered units</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <div class="stat-card">
            <div class="stat-label"><i class="ti ti-file-text me-1 text-success"></i>Docs Issued</div>
            <div class="stat-value">{{ number_format($docs_this_month) }}</div>
            <div class="stat-sub">This month</div>
        </div>
    </div>
    <div class="col-6 col-md-6 col-lg">
        <div class="stat-card">
            <div class="stat-label"><i class="ti ti-message-report me-1 text-warning"></i>Pending Requests</div>
            <div class="stat-value">{{ $pending_requests }}</div>
            @if($urgent_requests > 0)
                <div class="stat-sub text-danger fw-semibold">{{ $urgent_requests }} urgent (3+ days)</div>
            @else
                <div class="stat-sub">Awaiting action</div>
            @endif
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg">
        <div class="stat-card">
            <div class="stat-label"><i class="ti ti-clipboard-list me-1 text-indigo"></i>Active Blotter</div>
            <div class="stat-value">{{ $active_services }}</div>
            <div class="stat-sub">Pending / in progress</div>
        </div>
    </div>
</div>

{{-- Captain Quick Actions --}}
<div class="row g-2 mb-3">
    <div class="col-12">
        <div class="card-custom d-flex gap-2 flex-wrap">
            @if(auth()->user()->canDo('residents.create_edit'))
                <a href="{{ route('admin.residents.create') }}" class="btn btn-navy btn-sm"><i class="ti ti-user-plus me-1"></i>Add Resident</a>
            @endif
            @if(auth()->user()->canDo('documents.create'))
                <a href="{{ route('admin.documents.create') }}" class="btn btn-navy btn-sm"><i class="ti ti-file-plus me-1"></i>Issue Document</a>
            @endif
            @if(auth()->user()->canDo('announcements.create'))
                <a href="{{ route('admin.announcements.create') }}" class="btn btn-outline-navy btn-sm"><i class="ti ti-speakerphone me-1"></i>New Announcement</a>
            @endif
            @if(auth()->user()->canDo('reports.view'))
                <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-navy btn-sm"><i class="ti ti-chart-bar me-1"></i>Reports &amp; Analytics</a>
            @endif
            <a href="{{ route('admin.citizen-requests.index') }}" class="btn btn-outline-secondary btn-sm"><i class="ti ti-message-report me-1"></i>Citizen Requests</a>
            <a href="{{ route('admin.service-logs.index') }}" class="btn btn-outline-secondary btn-sm"><i class="ti ti-clipboard-list me-1"></i>Blotter Records</a>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Left: Recent Activity Feed --}}
    <div class="col-12 col-lg-5">
        <div class="card-custom h-100">
            <h6 class="fw-semibold mb-3"><i class="ti ti-activity me-1 text-primary"></i>Barangay Real-Time Activity</h6>
            @forelse($recent_activity as $item)
            <div class="d-flex align-items-start gap-2 py-2 border-bottom border-light">
                <div style="width:8px;height:8px;border-radius:50%;background:
                    {{ $item['type'] === 'document' ? '#1a3a6b' : ($item['type'] === 'request' ? '#d97706' : '#2563eb') }};
                    flex-shrink:0;margin-top:6px"></div>
                <div class="flex-grow-1">
                    <a href="{{ $item['url'] }}" class="text-dark text-decoration-none fw-medium d-block" style="font-size:13px">
                        {{ $item['text'] }}
                    </a>
                    <div style="font-size:11px;color:#888">{{ \Carbon\Carbon::parse($item['time'])->diffForHumans() }}</div>
                </div>
            </div>
            @empty
            <p class="text-muted small">No recent activity recorded.</p>
            @endforelse
        </div>
    </div>

    {{-- Right: Document Issuances & Top Issues --}}
    <div class="col-12 col-lg-7">
        <div class="card-custom mb-3">
            <h6 class="fw-semibold mb-3"><i class="ti ti-file-certificate me-1 text-teal"></i>Document Issuances — {{ now()->format('F Y') }}</h6>
            @forelse($doc_type_counts as $type => $count)
            <div class="bar-chart-row">
                <div class="bar-chart-label">{{ \App\Models\Document::TYPES[$type] ?? $type }}</div>
                <div class="bar-chart-track">
                    <div class="bar-chart-fill" style="width:{{ $doc_type_counts->max() > 0 ? round(($count / $doc_type_counts->max()) * 100) : 0 }}%">
                        <span class="bar-chart-val">{{ $count }}</span>
                    </div>
                </div>
            </div>
            @empty
            <p class="text-muted small mb-0">No documents issued this month.</p>
            @endforelse
        </div>

        <div class="card-custom mb-3">
            <h6 class="fw-semibold mb-3"><i class="ti ti-alert-triangle me-1 text-warning"></i>Top Reported Citizen Complaints</h6>
            @forelse($top_issues as $issue)
            <div style="font-size:12.5px;color:#475569;margin-bottom:8px">
                <div class="d-flex justify-content-between mb-1">
                    <span class="fw-medium">{{ ucwords(str_replace('_',' ',$issue->request_type)) }}</span>
                    <span class="text-muted">{{ $issue->total }} cases</span>
                </div>
                <div style="height:7px;border-radius:4px;background:#f0f2f5;overflow:hidden">
                    <div style="height:100%;background:#1a3a6b;width:{{ $top_issues->max('total') > 0 ? round(($issue->total / $top_issues->max('total')) * 100) : 0 }}%;border-radius:4px"></div>
                </div>
            </div>
            @empty
            <p class="text-muted small mb-0">No complaints filed yet.</p>
            @endforelse
        </div>

        {{-- Live Announcements --}}
        <div class="card-custom">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-semibold mb-0"><i class="ti ti-speakerphone me-1 text-primary"></i>Live Public Announcements</h6>
                <a href="{{ route('admin.announcements.index') }}" class="btn btn-link btn-sm p-0 text-decoration-none" style="font-size:12px">View All</a>
            </div>
            @forelse($announcements as $ann)
            <div class="py-2 border-bottom border-light">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="fw-semibold" style="font-size:13px">{{ $ann->title }}</div>
                    <span class="badge bg-secondary" style="font-size:10px">{{ ucwords(str_replace('_',' ',$ann->announcement_type)) }}</span>
                </div>
                <div class="text-muted mt-1" style="font-size:11.5px">
                    {{ Str::limit(strip_tags($ann->body), 80) }}
                </div>
            </div>
            @empty
            <p class="text-muted small mb-0">No active announcements.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- ======================================================= --}}
{{-- ⚙️ 2B. SYSTEM ADMINISTRATOR (IT) HEALTH & ACCESS PANEL   --}}
{{-- ======================================================= --}}
@elseif(auth()->user()->isAdmin())
<div class="row g-3 mb-3">
    <div class="col-6 col-md-4 col-lg">
        <div class="stat-card">
            <div class="stat-label"><i class="ti ti-users-group me-1 text-primary"></i>Total Users</div>
            <div class="stat-value">{{ $total_users }}</div>
            <div class="stat-sub">{{ $active_users }} active accounts</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <div class="stat-card">
            <div class="stat-label"><i class="ti ti-users me-1 text-teal"></i>Residents</div>
            <div class="stat-value">{{ number_format($total_residents) }}</div>
            <div class="stat-sub">Active population</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg">
        <div class="stat-card">
            <div class="stat-label"><i class="ti ti-home me-1 text-success"></i>Households</div>
            <div class="stat-value">{{ number_format($total_households) }}</div>
            <div class="stat-sub">Registered units</div>
        </div>
    </div>
    <div class="col-6 col-md-6 col-lg">
        <div class="stat-card">
            <div class="stat-label"><i class="ti ti-tool me-1 text-warning"></i>Maintenance</div>
            <div class="stat-value" style="font-size:18px;margin-top:6px">
                @if($maintenance_mode)
                    <span class="badge bg-warning text-dark px-2 py-1">ACTIVE</span>
                @else
                    <span class="badge bg-success px-2 py-1">NORMAL</span>
                @endif
            </div>
            <div class="stat-sub">{{ $maintenance_mode ? 'Portal locked' : 'Resident portal online' }}</div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg">
        <div class="stat-card">
            <div class="stat-label"><i class="ti ti-database me-1 text-indigo"></i>Database Engine</div>
            <div class="stat-value" style="font-size:18px;margin-top:6px">{{ $db_driver }}</div>
            <div class="stat-sub">PHP {{ $php_version }}</div>
        </div>
    </div>
</div>

{{-- Admin Quick Actions --}}
<div class="row g-2 mb-3">
    <div class="col-12">
        <div class="card-custom d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.users.index') }}" class="btn btn-navy btn-sm"><i class="ti ti-user-cog me-1"></i>Manage Users</a>
            <a href="{{ route('admin.settings.index') }}" class="btn btn-navy btn-sm"><i class="ti ti-settings me-1"></i>Barangay Configuration</a>
            <a href="{{ route('admin.settings.index') }}#permissions-pane" class="btn btn-outline-navy btn-sm"><i class="ti ti-shield-lock me-1"></i>Role Permissions Matrix</a>
            <a href="{{ route('admin.settings.index') }}#demographics-pane" class="btn btn-outline-navy btn-sm"><i class="ti ti-chart-pie me-1"></i>Age Brackets Configuration</a>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm"><i class="ti ti-archive me-1"></i>Export Hub &amp; Backups</a>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Left: User Role Accounts Distribution --}}
    <div class="col-12 col-lg-6">
        <div class="card-custom mb-3">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-semibold mb-0"><i class="ti ti-user-shield me-1 text-primary"></i>System User Accounts by Role</h6>
                <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-outline-navy" style="font-size:12px"><i class="ti ti-user-plus me-1"></i>New User</a>
            </div>
            <div class="table-responsive-custom">
                <table class="table-custom align-middle">
                    <thead>
                        <tr>
                            <th>Role Title</th>
                            <th>Role Key</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Active</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(\App\Models\User::ROLE_LABELS as $rKey => $rLabel)
                        @php
                            $rData = $user_counts[$rKey] ?? null;
                            $count = $rData ? $rData->total : 0;
                            $active = $rData ? $rData->active_total : 0;
                        @endphp
                        <tr>
                            <td>
                                <span class="fw-semibold" style="font-size:13px">{{ $rLabel }}</span>
                            </td>
                            <td><code style="font-size:11px">{{ $rKey }}</code></td>
                            <td class="text-center fw-bold">{{ $count }}</td>
                            <td class="text-center">
                                <span class="badge {{ $active > 0 ? 'bg-success' : 'bg-secondary' }}" style="font-size:10px">{{ $active }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Maintenance Notice Card --}}
        <div class="card-custom {{ $maintenance_mode ? 'border border-warning' : '' }}">
            <h6 class="fw-semibold mb-2"><i class="ti ti-tool me-1 text-warning"></i>Maintenance Mode Control</h6>
            <p class="text-muted small mb-2">
                System maintenance allows administrators to perform database backups or schema alterations while displaying a friendly downtime banner to residents.
            </p>
            <div class="d-flex align-items-center justify-content-between">
                <span class="badge {{ $maintenance_mode ? 'bg-warning text-dark' : 'bg-success' }} py-1 px-2">
                    {{ $maintenance_mode ? 'Maintenance Mode is currently ON' : 'System is Operating Normally' }}
                </span>
                <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary btn-sm" style="font-size:12px">
                    Change Switch
                </a>
            </div>
        </div>
    </div>

    {{-- Right: System Environment & Audit Activity --}}
    <div class="col-12 col-lg-6">
        <div class="card-custom mb-3">
            <h6 class="fw-semibold mb-3"><i class="ti ti-server me-1 text-indigo"></i>Application &amp; Server Environment</h6>
            <div class="row g-2" style="font-size:13px">
                <div class="col-6"><span class="text-muted">Framework:</span> <span class="fw-bold">Laravel {{ app()->version() }}</span></div>
                <div class="col-6"><span class="text-muted">PHP Version:</span> <span class="fw-bold">{{ PHP_VERSION }}</span></div>
                <div class="col-6"><span class="text-muted">Environment:</span> <span class="fw-bold text-uppercase">{{ app()->environment() }}</span></div>
                <div class="col-6"><span class="text-muted">Database:</span> <span class="fw-bold">{{ $db_driver }}</span></div>
                <div class="col-12"><span class="text-muted">Public Storage:</span> <span class="fw-medium">storage/app/public (Linked)</span></div>
            </div>
        </div>

        <div class="card-custom">
            <h6 class="fw-semibold mb-3"><i class="ti ti-history me-1 text-primary"></i>Recent System Activity</h6>
            @forelse($recent_activity as $item)
            <div class="d-flex align-items-start gap-2 py-2 border-bottom border-light">
                <div style="width:8px;height:8px;border-radius:50%;background:#1a3a6b;flex-shrink:0;margin-top:6px"></div>
                <div class="flex-grow-1">
                    <a href="{{ $item['url'] }}" class="text-dark text-decoration-none fw-medium d-block" style="font-size:13px">
                        {{ $item['text'] }}
                    </a>
                    <div style="font-size:11px;color:#888">{{ \Carbon\Carbon::parse($item['time'])->diffForHumans() }}</div>
                </div>
            </div>
            @empty
            <p class="text-muted small mb-0">No system events logged.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- ======================================================== --}}
{{-- 📋 2C. BARANGAY SECRETARY OPERATIONAL WORKLIST          --}}
{{-- ======================================================== --}}
@elseif(auth()->user()->isSecretary())
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label"><i class="ti ti-file-plus me-1 text-primary"></i>Pending Docs</div>
            <div class="stat-value">{{ $pending_docs }}</div>
            <div class="stat-sub">Awaiting verification</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label"><i class="ti ti-file-settings me-1 text-warning"></i>In Review / Process</div>
            <div class="stat-value">{{ $under_review_docs + $processing_docs }}</div>
            <div class="stat-sub">Being prepared</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label"><i class="ti ti-package me-1 text-success"></i>Ready for Pickup</div>
            <div class="stat-value">{{ $ready_pickup_docs }}</div>
            <div class="stat-sub">Awaiting resident</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label"><i class="ti ti-message-report me-1 text-danger"></i>Open Requests</div>
            <div class="stat-value">{{ $pending_requests }}</div>
            @if($urgent_requests > 0)
                <div class="stat-sub text-danger fw-semibold">{{ $urgent_requests }} urgent cases</div>
            @else
                <div class="stat-sub">Citizen inquiries</div>
            @endif
        </div>
    </div>
</div>

{{-- Secretary Quick Actions --}}
<div class="row g-2 mb-3">
    <div class="col-12">
        <div class="card-custom d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.documents.create') }}" class="btn btn-navy btn-sm"><i class="ti ti-file-plus me-1"></i>Issue New Document</a>
            <a href="{{ route('admin.documents.index') }}" class="btn btn-outline-navy btn-sm"><i class="ti ti-file-certificate me-1"></i>View All Documents</a>
            <a href="{{ route('admin.citizen-requests.index') }}" class="btn btn-outline-navy btn-sm"><i class="ti ti-message-report me-1"></i>Manage Requests</a>
            <a href="{{ route('admin.announcements.create') }}" class="btn btn-outline-secondary btn-sm"><i class="ti ti-speakerphone me-1"></i>Draft Announcement</a>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Left: Document Requests Needing Action --}}
    <div class="col-12 col-lg-7">
        <div class="card-custom mb-3">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-semibold mb-0"><i class="ti ti-clock-check me-1 text-primary"></i>Documents Requiring Action</h6>
                <a href="{{ route('admin.documents.index') }}" class="btn btn-link btn-sm p-0 text-decoration-none" style="font-size:12px">View All</a>
            </div>
            <div class="table-responsive-custom">
                <table class="table-custom align-middle">
                    <thead>
                        <tr>
                            <th>Doc No.</th>
                            <th>Resident</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($actionable_docs as $d)
                        <tr>
                            <td style="font-family:monospace;font-size:12px">{{ $d->document_number }}</td>
                            <td><span class="fw-medium">{{ optional($d->resident)->full_name }}</span></td>
                            <td>{{ \App\Models\Document::TYPES[$d->document_type] ?? $d->document_type }}</td>
                            <td><span class="badge-status badge-{{ $d->status }}" style="font-size:10px">{{ ucwords(str_replace('_',' ',$d->status)) }}</span></td>
                            <td>
                                <a href="{{ route('admin.documents.show', $d->id) }}" class="btn btn-xs btn-navy py-1 px-2" style="font-size:11px">
                                    <i class="ti ti-arrow-right me-1"></i>Process
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-3 text-muted small">No pending documents requiring attention.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Ready for Pickup --}}
        <div class="card-custom">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h6 class="fw-semibold mb-0"><i class="ti ti-package me-1 text-success"></i>Ready for Resident Pickup</h6>
                <span class="badge bg-success" style="font-size:11px">{{ count($ready_pickup_list) }} in queue</span>
            </div>
            @forelse($ready_pickup_list as $rd)
            <div class="d-flex align-items-center justify-content-between py-2 border-bottom border-light">
                <div>
                    <span class="fw-semibold" style="font-size:13px">{{ optional($rd->resident)->full_name }}</span>
                    <span class="text-muted ms-1" style="font-size:12px">({{ \App\Models\Document::TYPES[$rd->document_type] ?? $rd->document_type }})</span>
                    <div style="font-family:monospace;font-size:11px;color:#185fa5">{{ $rd->document_number }}</div>
                </div>
                <a href="{{ route('admin.documents.show', $rd->id) }}" class="btn btn-outline-success btn-xs py-1 px-2" style="font-size:11px">
                    <i class="ti ti-circle-check me-1"></i>Release
                </a>
            </div>
            @empty
            <p class="text-muted small mb-0 mt-2">No documents currently awaiting pickup.</p>
            @endforelse
        </div>
    </div>

    {{-- Right: Citizen Requests Needing Attention --}}
    <div class="col-12 col-lg-5">
        <div class="card-custom">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-semibold mb-0"><i class="ti ti-message-report me-1 text-warning"></i>Active Citizen Inquiries</h6>
                <a href="{{ route('admin.citizen-requests.index') }}" class="btn btn-link btn-sm p-0 text-decoration-none" style="font-size:12px">View All</a>
            </div>
            <div class="table-responsive-custom">
                <table class="table-custom align-middle">
                    <thead>
                        <tr>
                            <th>Tracking</th>
                            <th>Complaint Type</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($actionable_requests as $req)
                        <tr>
                            <td style="font-family:monospace;font-size:11.5px">{{ $req->tracking_number }}</td>
                            <td><span class="fw-medium" style="font-size:12.5px">{{ ucwords(str_replace('_',' ',$req->request_type)) }}</span></td>
                            <td><span class="badge-status badge-{{ $req->status }}" style="font-size:10px">{{ ucfirst($req->status) }}</span></td>
                            <td>
                                <a href="{{ route('admin.citizen-requests.show', $req->id) }}" class="btn btn-xs btn-outline-secondary py-1 px-2" style="font-size:11px">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-3 text-muted small">No active citizen requests.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ======================================================== --}}
{{-- 🏛️ 2D. BARANGAY COUNCILOR (KAGAWAD) LEGISLATIVE OVERVIEW --}}
{{-- ======================================================== --}}
@elseif(auth()->user()->role === 'councilor')
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label"><i class="ti ti-users me-1 text-primary"></i>Total Population</div>
            <div class="stat-value">{{ number_format($total_residents) }}</div>
            <div class="stat-sub">Registered residents</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label"><i class="ti ti-home me-1 text-teal"></i>Households</div>
            <div class="stat-value">{{ number_format($total_households) }}</div>
            <div class="stat-sub">Registered families</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label"><i class="ti ti-message-report me-1 text-warning"></i>Complaints ({{ now()->format('M') }})</div>
            <div class="stat-value">{{ $complaints_month }}</div>
            <div class="stat-sub">Reported this month</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label"><i class="ti ti-circle-check me-1 text-success"></i>Cases Resolved</div>
            <div class="stat-value">{{ $resolved_month }}</div>
            <div class="stat-sub">Resolved this month</div>
        </div>
    </div>
</div>

{{-- Note: No action buttons for Councilor (Legislative Read-Only oversight) --}}

<div class="row g-3">
    {{-- Left: Demographic Age Groups & Gender Breakdown --}}
    <div class="col-12 col-lg-7">
        {{-- Gender Split Card --}}
        <div class="card-custom mb-3">
            <h6 class="fw-semibold mb-3"><i class="ti ti-gender-intergender me-1 text-primary"></i>Gender Demographic Split</h6>
            <div class="d-flex justify-content-between mb-1" style="font-size:13px">
                <span class="text-primary fw-bold"><i class="ti ti-gender-male me-1"></i>Male: {{ number_format($males) }} ({{ $male_pct }}%)</span>
                <span class="text-danger fw-bold"><i class="ti ti-gender-female me-1"></i>Female: {{ number_format($females) }} ({{ $female_pct }}%)</span>
            </div>
            <div class="progress" style="height:12px;border-radius:6px">
                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $male_pct }}%" title="Male {{ $male_pct }}%"></div>
                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $female_pct }}%" title="Female {{ $female_pct }}%"></div>
            </div>
        </div>

        {{-- Age Group Brackets --}}
        <div class="card-custom">
            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                <div>
                    <h6 class="fw-semibold mb-0"><i class="ti ti-chart-bar me-1 text-indigo"></i>Population by Age Bracket &amp; Life Stage</h6>
                    <div class="text-muted" style="font-size:11.5px">Standard demographic breakdown for policy planning</div>
                </div>
                <span class="badge bg-navy px-2 py-1" style="font-size:11px">{{ number_format($total_residents) }} Residents</span>
            </div>

            @foreach($demographics as $cat)
            <div class="mb-4">
                <div class="fw-bold text-uppercase mb-2" style="font-size:12px;color:#185fa5;letter-spacing:0.5px">
                    <i class="ti ti-folder me-1"></i>{{ $cat['category'] }}
                </div>

                @foreach($cat['brackets'] as $b)
                <div class="mb-2 p-2 rounded" style="background:#f8fafc">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div>
                            <span class="badge bg-secondary me-1" style="font-size:11px">
                                @if($b['max'] !== null)
                                    {{ $b['min'] }} – {{ $b['max'] }} yrs
                                @else
                                    {{ $b['min'] }}+ yrs
                                @endif
                            </span>
                            <span class="fw-semibold" style="font-size:12.5px">{{ $b['label'] }}</span>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold" style="font-size:13px">{{ number_format($b['count']) }}</span>
                            <span class="text-muted small ms-1">({{ $b['percentage'] }}%)</span>
                        </div>
                    </div>
                    <div class="progress" style="height:6px;border-radius:3px">
                        <div class="progress-bar" role="progressbar" style="width: {{ $b['percentage'] }}%;background:#185fa5"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
    </div>

    {{-- Right: Top Issues & Active Announcements --}}
    <div class="col-12 col-lg-5">
        <div class="card-custom mb-3">
            <h6 class="fw-semibold mb-3"><i class="ti ti-alert-triangle me-1 text-warning"></i>Top Reported Community Issues</h6>
            @forelse($top_issues as $issue)
            <div style="font-size:12.5px;color:#475569;margin-bottom:8px">
                <div class="d-flex justify-content-between mb-1">
                    <span class="fw-medium">{{ ucwords(str_replace('_',' ',$issue->request_type)) }}</span>
                    <span class="text-muted">{{ $issue->total }} cases</span>
                </div>
                <div style="height:7px;border-radius:4px;background:#f0f2f5;overflow:hidden">
                    <div style="height:100%;background:#1a3a6b;width:{{ $top_issues->max('total') > 0 ? round(($issue->total / $top_issues->max('total')) * 100) : 0 }}%;border-radius:4px"></div>
                </div>
            </div>
            @empty
            <p class="text-muted small mb-0">No complaints filed.</p>
            @endforelse
        </div>

        <div class="card-custom">
            <h6 class="fw-semibold mb-3"><i class="ti ti-speakerphone me-1 text-primary"></i>Barangay Notices &amp; Advisories</h6>
            @forelse($announcements as $ann)
            <div class="py-2 border-bottom border-light">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-semibold" style="font-size:13px">{{ $ann->title }}</span>
                    <span class="badge bg-light text-dark" style="font-size:10px">{{ \Carbon\Carbon::parse($ann->published_at)->format('M d') }}</span>
                </div>
                <div class="text-muted mt-1" style="font-size:11.5px">
                    {{ Str::limit(strip_tags($ann->body), 85) }}
                </div>
            </div>
            @empty
            <p class="text-muted small mb-0">No active notices.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- ======================================================== --}}
{{-- 🛡️ 2E. BARANGAY STAFF / TANOD PERSONAL TASK QUEUE       --}}
{{-- ======================================================== --}}
@elseif(auth()->user()->isStaff())
<div class="row g-3 mb-3">
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="stat-label"><i class="ti ti-clipboard-list me-1 text-primary"></i>My Assigned Logs</div>
            <div class="stat-value">{{ $my_active_logs }}</div>
            <div class="stat-sub">Active field assignments</div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="stat-label"><i class="ti ti-message-report me-1 text-warning"></i>My Assigned Requests</div>
            <div class="stat-value">{{ $my_active_requests }}</div>
            <div class="stat-sub">Complaints to investigate</div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="stat-label"><i class="ti ti-circle-check me-1 text-success"></i>Completed This Month</div>
            <div class="stat-value">{{ $completed_month }}</div>
            <div class="stat-sub">Successfully resolved</div>
        </div>
    </div>
</div>

{{-- Staff Assigned Tasks Tables --}}
<div class="row g-3">
    {{-- Left: My Assigned Service Logs --}}
    <div class="col-12 col-lg-6">
        <div class="card-custom h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-semibold mb-0"><i class="ti ti-clipboard-check me-1 text-primary"></i>My Active Service &amp; Blotter Assignments</h6>
                <span class="badge bg-primary" style="font-size:11px">{{ count($assigned_logs) }} logs</span>
            </div>
            <div class="table-responsive-custom">
                <table class="table-custom align-middle">
                    <thead>
                        <tr>
                            <th>Log #</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assigned_logs as $log)
                        <tr>
                            <td style="font-family:monospace;font-size:12px">{{ $log->log_number }}</td>
                            <td><span class="fw-medium">{{ $log->service_type }}</span></td>
                            <td><span class="badge-status badge-{{ $log->status }}" style="font-size:10px">{{ ucfirst($log->status) }}</span></td>
                            <td>
                                <a href="{{ route('admin.service-logs.show', $log->id) }}" class="btn btn-xs btn-navy py-1 px-2" style="font-size:11px">
                                    <i class="ti ti-arrow-right me-1"></i>View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                <i class="ti ti-circle-check fs-2 text-success d-block mb-1"></i>
                                No active service logs assigned to you.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Right: My Assigned Citizen Inquiries --}}
    <div class="col-12 col-lg-6">
        <div class="card-custom h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-semibold mb-0"><i class="ti ti-message-report me-1 text-warning"></i>My Assigned Citizen Complaints</h6>
                <span class="badge bg-warning text-dark" style="font-size:11px">{{ count($assigned_requests) }} requests</span>
            </div>
            <div class="table-responsive-custom">
                <table class="table-custom align-middle">
                    <thead>
                        <tr>
                            <th>Tracking #</th>
                            <th>Complaint Type</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assigned_requests as $cr)
                        <tr>
                            <td style="font-family:monospace;font-size:12px">{{ $cr->tracking_number }}</td>
                            <td><span class="fw-medium">{{ ucwords(str_replace('_',' ',$cr->request_type)) }}</span></td>
                            <td><span class="badge-status badge-{{ $cr->status }}" style="font-size:10px">{{ ucfirst($cr->status) }}</span></td>
                            <td>
                                <a href="{{ route('admin.citizen-requests.show', $cr->id) }}" class="btn btn-xs btn-navy py-1 px-2" style="font-size:11px">
                                    <i class="ti ti-arrow-right me-1"></i>View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                <i class="ti ti-circle-check fs-2 text-success d-block mb-1"></i>
                                No citizen requests assigned to you.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

@endsection
