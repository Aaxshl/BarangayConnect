@extends('layouts.sk')
@section('title', 'SK Dashboard')

@section('content')

{{-- Welcome Hero Banner --}}
<div class="card-custom mb-4 p-4" style="background:linear-gradient(135deg, #0d9488 0%, #0f766e 100%);color:#fff;border:none">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div style="width:52px;height:52px;border-radius:14px;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;font-size:26px">
                ⚡
            </div>
            <div>
                <h4 class="fw-bold mb-1" style="color:#fff">Mabuhay, {{ $user->name }}!</h4>
                <div style="font-size:13.5px;opacity:0.9">
                    <span class="badge bg-warning text-dark fw-bold me-2">{{ $user->role_label }}</span>
                    <span>Katipunan ng Kabataan (KK) Official Portal</span>
                </div>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('sk.programs.create') }}" class="btn btn-warning btn-sm fw-bold text-dark d-flex align-items-center gap-1">
                <i class="ti ti-plus"></i> Propose Program
            </a>
            <a href="{{ route('sk.youth-residents.index') }}" class="btn btn-outline-light btn-sm d-flex align-items-center gap-1">
                <i class="ti ti-users"></i> Youth Directory
            </a>
            <a href="{{ route('sk.announcements.create') }}" class="btn btn-outline-light btn-sm d-flex align-items-center gap-1">
                <i class="ti ti-speakerphone"></i> Post Announcement
            </a>
        </div>
    </div>
</div>

{{-- 4 Primary Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card-sk">
            <div class="stat-label-sk"><i class="ti ti-users me-1 text-primary"></i>Youth Population</div>
            <div class="stat-val-sk">{{ number_format($totalYouth) }}</div>
            <div class="stat-sub-sk">Residents aged 15–24</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card-sk" style="--sk-primary:#0d9488">
            <div class="stat-label-sk"><i class="ti ti-target me-1 text-teal"></i>Active Programs</div>
            <div class="stat-val-sk" style="color:#0d9488">{{ $activeProgramsCount }}</div>
            <div class="stat-sub-sk">{{ $proposedProgramsCount }} proposed / pending</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card-sk" style="--sk-primary:#16a34a">
            <div class="stat-label-sk"><i class="ti ti-circle-check me-1 text-success"></i>Completed Projects</div>
            <div class="stat-val-sk" style="color:#16a34a">{{ $completedProgramsCount }}</div>
            <div class="stat-sub-sk">Successfully delivered</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card-sk" style="--sk-primary:#f59e0b">
            <div class="stat-label-sk"><i class="ti ti-coins me-1 text-warning"></i>Allocated Budget</div>
            <div class="stat-val-sk" style="font-size:22px;color:#d97706;padding-top:4px">
                ₱{{ number_format($totalBudgetAllocated, 2) }}
            </div>
            <div class="stat-sub-sk">Approved youth initiatives</div>
        </div>
    </div>
</div>

{{-- Youth Demographic Sub-Brackets --}}
<div class="card-custom mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
        <div>
            <h6 class="fw-bold mb-0 text-primary"><i class="ti ti-chart-pie me-1"></i>Youth Demographics by Age Cohort (Ages 15–24)</h6>
            <div class="text-muted small">Distribution of youth residents in the barangay</div>
        </div>
        <a href="{{ route('sk.youth-residents.index') }}" class="btn btn-sm btn-link p-0 text-decoration-none" style="font-size:12.5px">
            View Full Youth Masterlist <i class="ti ti-arrow-right"></i>
        </a>
    </div>

    @php
        $teenPct = $totalYouth > 0 ? round(($teensCount / $totalYouth) * 100, 1) : 0;
        $youngAdultPct = $totalYouth > 0 ? round(($youngAdultsCount / $totalYouth) * 100, 1) : 0;
    @endphp

    <div class="row g-3">
        <div class="col-md-6">
            <div class="p-3 border rounded bg-white h-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="badge bg-info text-dark">15–17 years old</span>
                    <span class="fw-bold fs-5 text-dark">{{ number_format($teensCount) }}</span>
                </div>
                <div class="fw-semibold small text-muted">Adolescents &amp; Teens</div>
                <div class="progress mt-2" style="height:7px;border-radius:4px">
                    <div class="progress-bar bg-info" style="width: {{ $teenPct }}%"></div>
                </div>
                <div class="text-end small text-muted mt-1">{{ $teenPct }}% of youth population</div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="p-3 border rounded bg-white h-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="badge bg-primary">18–24 years old</span>
                    <span class="fw-bold fs-5 text-dark">{{ number_format($youngAdultsCount) }}</span>
                </div>
                <div class="fw-semibold small text-muted">Young Adults (Higher Ed &amp; Early Workforce)</div>
                <div class="progress mt-2" style="height:7px;border-radius:4px">
                    <div class="progress-bar bg-primary" style="width: {{ $youngAdultPct }}%"></div>
                </div>
                <div class="text-end small text-muted mt-1">{{ $youngAdultPct }}% of youth population</div>
            </div>
        </div>
    </div>
</div>

{{-- Two-Column Grid: Recent Programs & SK Announcements --}}
<div class="row g-3">
    {{-- Left: SK Programs & Projects --}}
    <div class="col-12 col-lg-8">
        <div class="card-custom h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h6 class="fw-bold mb-0 text-primary"><i class="ti ti-target me-1"></i>Sangguniang Kabataan Programs &amp; Projects</h6>
                    <div class="text-muted small">Current and recent youth governance initiatives</div>
                </div>
                <a href="{{ route('sk.programs.index') }}" class="btn btn-outline-primary btn-sm" style="font-size:12.5px">
                    View All Programs
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-custom-sk align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Program Title</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Budget</th>
                            <th>Coordinator</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPrograms as $prog)
                        <tr>
                            <td>
                                <a href="{{ route('sk.programs.show', $prog) }}" class="fw-bold text-dark text-decoration-none">
                                    {{ $prog->title }}
                                </a>
                                <div class="text-muted small"><i class="ti ti-calendar me-1"></i>{{ $prog->start_date->format('M d, Y') }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border" style="font-size:11px">{{ $prog->category_label }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $prog->status_badge_class }}" style="font-size:10.5px">{{ $prog->status_label }}</span>
                            </td>
                            <td class="fw-semibold text-nowrap">
                                ₱{{ number_format($prog->budget, 2) }}
                            </td>
                            <td>
                                <span class="small">{{ optional($prog->coordinator)->name ?? 'Unassigned' }}</span>
                            </td>
                            <td>
                                <a href="{{ route('sk.programs.show', $prog) }}" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size:11.5px">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="ti ti-target-arrow fs-2 d-block mb-1 text-secondary"></i>
                                No SK programs created yet. <a href="{{ route('sk.programs.create') }}">Propose the first program!</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Right: SK Announcements & Events --}}
    <div class="col-12 col-lg-4">
        <div class="card-custom h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold mb-0 text-primary"><i class="ti ti-speakerphone me-1"></i>Youth Announcements</h6>
                <a href="{{ route('sk.announcements.index') }}" class="btn btn-link btn-sm p-0 text-decoration-none" style="font-size:12.5px">View All</a>
            </div>

            @forelse($skAnnouncements as $ann)
            <div class="p-2 border rounded mb-2 bg-light">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="fw-bold text-dark" style="font-size:13px">{{ $ann->title }}</span>
                    <span class="badge bg-secondary" style="font-size:10px">{{ ucwords(str_replace('_',' ',$ann->announcement_type)) }}</span>
                </div>
                <div class="text-muted mt-1" style="font-size:12px">
                    {{ Str::limit(strip_tags($ann->body), 85) }}
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2 small text-muted" style="font-size:11px">
                    <span><i class="ti ti-clock me-1"></i>{{ $ann->created_at->diffForHumans() }}</span>
                    <a href="{{ route('sk.announcements.show', $ann) }}" class="text-primary text-decoration-none">Read &rarr;</a>
                </div>
            </div>
            @empty
            <div class="text-center py-4 text-muted">
                <i class="ti ti-bell-off fs-2 d-block mb-1 text-secondary"></i>
                No active announcements for youth.
            </div>
            @endforelse

            <div class="mt-3 text-center">
                <a href="{{ route('sk.announcements.create') }}" class="btn btn-outline-primary btn-sm w-100">
                    <i class="ti ti-plus me-1"></i> Post New Announcement
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
