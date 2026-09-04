@extends('layouts.sk')
@section('title', $program->title)

@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <a href="{{ route('sk.programs.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back to Programs
    </a>
    <div class="d-flex gap-2">
        <a href="{{ route('sk.programs.edit', $program) }}" class="btn btn-outline-primary btn-sm">
            <i class="ti ti-edit me-1"></i> Edit Details
        </a>
        <form method="POST" action="{{ route('sk.programs.destroy', $program) }}" onsubmit="return confirm('Are you sure you want to permanently delete this program record?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm">
                <i class="ti ti-trash me-1"></i> Delete
            </button>
        </form>
    </div>
</div>

<div class="row g-3">
    {{-- Left: Program Details & Status Flow --}}
    <div class="col-12 col-lg-8">
        <div class="card-custom mb-3 p-4">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                <div>
                    <span class="badge bg-light text-dark border mb-2">{{ $program->category_label }}</span>
                    <h4 class="fw-bold mb-1">{{ $program->title }}</h4>
                    <div class="text-muted small">
                        <i class="ti ti-map-pin me-1"></i>{{ $program->location ?: 'Barangay Plaza' }} &bull;
                        Proposed by {{ optional($program->createdBy)->name }} on {{ $program->created_at->format('M d, Y') }}
                    </div>
                </div>
                <div>
                    <span class="badge {{ $program->status_badge_class }} fs-6 px-3 py-2">
                        {{ $program->status_label }}
                    </span>
                </div>
            </div>

            {{-- Progress Status Flow Tracker --}}
            @php
                $statusOrder = ['proposed', 'approved', 'ongoing', 'completed'];
                $currentIdx = array_search($program->status, $statusOrder);
                if ($currentIdx === false) $currentIdx = -1;
            @endphp
            @if($program->status !== 'cancelled')
            <div class="p-3 bg-light rounded mb-4">
                <div class="d-flex align-items-center justify-content-between">
                    @foreach($statusOrder as $sIdx => $sName)
                    <div class="text-center flex-fill">
                        <div style="width:32px;height:32px;border-radius:50%;margin:0 auto 4px;display:flex;align-items:center;justify-content:center;font-size:14px;
                            {{ $sIdx <= $currentIdx ? 'background:#0d9488;color:#fff;font-weight:bold' : 'background:#e2e8f0;color:#64748b' }}">
                            {{ $sIdx + 1 }}
                        </div>
                        <div style="font-size:11.5px;font-weight:{{ $sIdx === $currentIdx ? '700' : '500' }};color:{{ $sIdx <= $currentIdx ? '#0f172a' : '#94a3b8' }}">
                            {{ ucfirst($sName) }}
                        </div>
                    </div>
                    @if(!$loop->last)
                        <div style="flex:1;height:2px;background:{{ $sIdx < $currentIdx ? '#0d9488' : '#cbd5e1' }};margin-top:-14px"></div>
                    @endif
                    @endforeach
                </div>
            </div>
            @else
            <div class="alert alert-danger py-2 mb-4 d-flex align-items-center gap-2">
                <i class="ti ti-alert-circle fs-5"></i>
                <div>This program initiative has been <strong>Cancelled</strong>.</div>
            </div>
            @endif

            {{-- Description --}}
            <h6 class="fw-bold text-primary mb-2">Scope &amp; Description</h6>
            <div class="p-3 bg-white border rounded mb-4" style="line-height:1.7;font-size:14px;white-space:pre-line">
                {{ $program->description }}
            </div>

            {{-- Status Action Trigger Form --}}
            <div class="p-3 border rounded bg-light">
                <h6 class="fw-bold mb-2 small text-uppercase text-muted" style="letter-spacing:0.5px">Program Status Workflow Actions</h6>
                <div class="d-flex gap-2 flex-wrap">
                    @if($program->status === 'proposed')
                        @if(in_array(auth()->user()->role, ['sk_chairman', 'captain', 'administrator']))
                            <form method="POST" action="{{ route('sk.programs.status', $program) }}">
                                @csrf
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="btn btn-info btn-sm fw-bold">
                                    <i class="ti ti-check me-1"></i> Approve Program Proposal
                                </button>
                            </form>
                        @else
                            <div class="small text-muted fst-italic">
                                <i class="ti ti-info-circle me-1"></i> Awaiting official approval from SK Chairman or Punong Barangay.
                            </div>
                        @endif
                    @elseif($program->status === 'approved')
                        <form method="POST" action="{{ route('sk.programs.status', $program) }}">
                            @csrf
                            <input type="hidden" name="action" value="start">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="ti ti-player-play me-1"></i> Mark as Ongoing / Started
                            </button>
                        </form>
                    @elseif($program->status === 'ongoing')
                        <form method="POST" action="{{ route('sk.programs.status', $program) }}">
                            @csrf
                            <input type="hidden" name="action" value="complete">
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="ti ti-circle-check me-1"></i> Mark as Completed
                            </button>
                        </form>
                    @elseif(in_array($program->status, ['completed', 'cancelled']))
                        <form method="POST" action="{{ route('sk.programs.status', $program) }}">
                            @csrf
                            <input type="hidden" name="action" value="reopen">
                            <button type="submit" class="btn btn-outline-secondary btn-sm">
                                <i class="ti ti-refresh me-1"></i> Reopen Program
                            </button>
                        </form>
                    @endif

                    @if($program->status !== 'cancelled' && $program->status !== 'completed')
                        <form method="POST" action="{{ route('sk.programs.status', $program) }}" onsubmit="return confirm('Are you sure you want to cancel this program?')">
                            @csrf
                            <input type="hidden" name="action" value="cancel">
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="ti ti-x me-1"></i> Cancel Program
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Right: Project Factsheet Card --}}
    <div class="col-12 col-lg-4">
        <div class="card-custom mb-3 p-4">
            <h6 class="fw-bold text-primary mb-3"><i class="ti ti-clipboard-list me-2"></i>Project Factsheet</h6>
            <div class="d-flex flex-column gap-3" style="font-size:13.5px">
                <div>
                    <span class="text-muted small d-block">Allocated Budget</span>
                    <strong class="fs-5 text-dark">₱{{ number_format($program->budget, 2) }}</strong>
                </div>
                <div>
                    <span class="text-muted small d-block">Target Beneficiaries / Participants</span>
                    <strong>{{ $program->target_participants ? number_format($program->target_participants) . ' youth' : 'General Public / Youth' }}</strong>
                </div>
                <div>
                    <span class="text-muted small d-block">Implementation Timeline</span>
                    <strong>{{ $program->start_date->format('F d, Y') }}</strong>
                    @if($program->end_date)
                        <div class="text-muted small">to {{ $program->end_date->format('F d, Y') }}</div>
                    @endif
                </div>
                <div>
                    <span class="text-muted small d-block">Designated Project Coordinator</span>
                    <strong>{{ optional($program->coordinator)->name ?? 'Unassigned' }}</strong>
                    @if($program->coordinator)
                        <div class="text-muted small">{{ $program->coordinator->role_label }}</div>
                    @endif
                </div>
                <div>
                    <span class="text-muted small d-block">Event Location</span>
                    <strong>{{ $program->location ?: 'Barangay Plaza' }}</strong>
                </div>
                @if($program->remarks)
                <div>
                    <span class="text-muted small d-block">Administrative Remarks</span>
                    <div class="p-2 border rounded bg-light small">{{ $program->remarks }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
