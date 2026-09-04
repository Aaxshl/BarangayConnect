@extends('layouts.sk')
@section('title', 'SK Programs & Projects')

@section('content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div>
        <h4 class="fw-bold mb-1 text-primary"><i class="ti ti-target me-2"></i>SK Programs &amp; Youth Initiatives</h4>
        <div class="text-muted small">Comprehensive management of Sangguniang Kabataan projects, sports leagues, seminars, and community drives.</div>
    </div>
    <a href="{{ route('sk.programs.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
        <i class="ti ti-plus"></i> Propose New Program
    </a>
</div>

{{-- Metric Counters --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card-sk">
            <div class="stat-label-sk">Total Initiatives</div>
            <div class="stat-val-sk">{{ $stats['total'] }}</div>
            <div class="stat-sub-sk">All recorded projects</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card-sk" style="--sk-primary:#0d9488">
            <div class="stat-label-sk">Approved / Ongoing</div>
            <div class="stat-val-sk" style="color:#0d9488">{{ $stats['active'] }}</div>
            <div class="stat-sub-sk">Currently active</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card-sk" style="--sk-primary:#16a34a">
            <div class="stat-label-sk">Completed</div>
            <div class="stat-val-sk" style="color:#16a34a">{{ $stats['completed'] }}</div>
            <div class="stat-sub-sk">Finished initiatives</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card-sk" style="--sk-primary:#f59e0b">
            <div class="stat-label-sk">Total Budget</div>
            <div class="stat-val-sk" style="font-size:20px;color:#d97706;padding-top:4px">
                ₱{{ number_format($stats['budget'], 2) }}
            </div>
            <div class="stat-sub-sk">Allocated fund</div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card-custom mb-3 p-3">
    <form method="GET" action="{{ route('sk.programs.index') }}" class="row g-2 align-items-center">
        <div class="col-12 col-md-4">
            <div class="input-group input-group-sm">
                <span class="input-group-text"><i class="ti ti-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Search program title, location..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-6 col-md-3">
            <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Categories</option>
                @foreach(\App\Models\SkProgram::CATEGORIES as $k => $lbl)
                    <option value="{{ $k }}" {{ request('category') === $k ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-3">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                @foreach(\App\Models\SkProgram::STATUSES as $sk => $sl)
                    <option value="{{ $sk }}" {{ request('status') === $sk ? 'selected' : '' }}>{{ $sl }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
            @if(request()->hasAny(['search', 'category', 'status']))
                <a href="{{ route('sk.programs.index') }}" class="btn btn-outline-secondary btn-sm"><i class="ti ti-x"></i></a>
            @endif
        </div>
    </form>
</div>

{{-- Programs Table --}}
<div class="card-custom p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-custom-sk align-middle mb-0">
            <thead>
                <tr>
                    <th>Program Title</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Timeline</th>
                    <th>Location</th>
                    <th>Budget</th>
                    <th>Coordinator</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($programs as $prog)
                <tr>
                    <td>
                        <a href="{{ route('sk.programs.show', $prog) }}" class="fw-bold text-dark text-decoration-none">
                            {{ $prog->title }}
                        </a>
                        <div class="text-muted small">{{ Str::limit($prog->description, 60) }}</div>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border" style="font-size:11px">{{ $prog->category_label }}</span>
                    </td>
                    <td>
                        <span class="badge {{ $prog->status_badge_class }}" style="font-size:10.5px">{{ $prog->status_label }}</span>
                    </td>
                    <td>
                        <div style="font-size:12.5px">{{ $prog->start_date->format('M d, Y') }}</div>
                        @if($prog->end_date)
                            <div class="text-muted small" style="font-size:11px">to {{ $prog->end_date->format('M d, Y') }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="text-muted" style="font-size:13px">{{ $prog->location ?: 'Barangay Plaza' }}</span>
                    </td>
                    <td class="fw-semibold text-nowrap">
                        ₱{{ number_format($prog->budget, 2) }}
                    </td>
                    <td>
                        <span class="small">{{ optional($prog->coordinator)->name ?? 'Unassigned' }}</span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('sk.programs.show', $prog) }}" class="btn btn-outline-secondary btn-sm py-1 px-2" title="View Details">
                                <i class="ti ti-eye"></i>
                            </a>
                            <a href="{{ route('sk.programs.edit', $prog) }}" class="btn btn-outline-primary btn-sm py-1 px-2" title="Edit Program">
                                <i class="ti ti-edit"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="ti ti-target-arrow fs-1 d-block mb-2 text-secondary"></i>
                        No SK programs found. <a href="{{ route('sk.programs.create') }}">Click here to propose a new program!</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $programs->links() }}
</div>
@endsection
