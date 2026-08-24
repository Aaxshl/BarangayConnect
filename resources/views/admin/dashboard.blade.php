@extends('layouts.admin')
@section('title','Dashboard')
@section('page-title','Dashboard')

@section('content')
<div class="row g-3 mt-1 mb-3">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label"><i class="ti ti-users me-1"></i>Total Residents</div>
            <div class="stat-value">{{ number_format($total_residents) }}</div>
            <div class="stat-sub">Active registered</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label"><i class="ti ti-home me-1"></i>Households</div>
            <div class="stat-value">{{ number_format($total_households) }}</div>
            <div class="stat-sub">Registered</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label"><i class="ti ti-file-text me-1"></i>Docs Issued</div>
            <div class="stat-value">{{ number_format($docs_this_month) }}</div>
            <div class="stat-sub">This month</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label"><i class="ti ti-message-report me-1"></i>Pending Requests</div>
            <div class="stat-value">{{ $pending_requests }}</div>
            @if($urgent_requests > 0)
                <div class="stat-sub" style="color:#A32D2D">{{ $urgent_requests }} urgent</div>
            @else
                <div class="stat-sub">Awaiting action</div>
            @endif
        </div>
    </div>
</div>

<!-- Quick actions -->
<div class="row g-2 mb-3">
    <div class="col-12">
        <div class="card-custom d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.residents.create') }}" class="btn btn-navy btn-sm"><i class="ti ti-user-plus me-1"></i>Add Resident</a>
            <a href="{{ route('admin.documents.create') }}" class="btn btn-navy btn-sm"><i class="ti ti-file-plus me-1"></i>Issue Document</a>
            <a href="{{ route('admin.citizen-requests.index') }}" class="btn btn-outline-navy btn-sm"><i class="ti ti-message-report me-1"></i>View Requests</a>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-navy btn-sm"><i class="ti ti-chart-bar me-1"></i>Reports</a>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-5">
        <div class="card-custom h-100">
            <h6 class="fw-semibold mb-3">Recent Activity</h6>
            @forelse($recent_activity as $item)
            <div class="d-flex align-items-start gap-2 py-2 border-bottom border-light">
                <div style="width:7px;height:7px;border-radius:50%;background:
                    {{ $item['type'] === 'document' ? '#1a3a6b' : ($item['type'] === 'request' ? '#A32D2D' : '#854F0B') }};
                    flex-shrink:0;margin-top:6px"></div>
                <div>
                    <div style="font-size:13px">{{ $item['text'] }}</div>
                    <div style="font-size:11px;color:#888">{{ \Carbon\Carbon::parse($item['time'])->diffForHumans() }}</div>
                </div>
            </div>
            @empty
            <p class="text-muted small">No recent activity.</p>
            @endforelse
        </div>
    </div>
    <div class="col-12 col-lg-7">
        <div class="card-custom mb-3">
            <h6 class="fw-semibold mb-3">Document types — {{ now()->format('F Y') }}</h6>
            @foreach($doc_type_counts as $type => $count)
            <div class="bar-chart-row">
                <div class="bar-chart-label">{{ \App\Models\Document::TYPES[$type] ?? $type }}</div>
                <div class="bar-chart-track">
                    <div class="bar-chart-fill" style="width:{{ $doc_type_counts->max() > 0 ? round(($count/$doc_type_counts->max())*100) : 0 }}%">
                        <span class="bar-chart-val">{{ $count }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="card-custom">
            <h6 class="fw-semibold mb-3">Top reported issues</h6>
            @foreach($top_issues as $issue)
            <div style="font-size:12.5px;color:#888;margin-bottom:6px">
                {{ ucwords(str_replace('_',' ',$issue->request_type)) }}
                <div style="height:7px;border-radius:4px;background:#f0f2f5;overflow:hidden;margin-top:3px">
                    <div style="height:100%;background:#1a3a6b;width:{{ $top_issues->max('total') > 0 ? round(($issue->total/$top_issues->max('total'))*100) : 0 }}%;border-radius:4px"></div>
                </div>
                <span style="font-size:11px">{{ $issue->total }} cases</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
