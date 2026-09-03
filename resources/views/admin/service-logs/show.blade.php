@extends('layouts.admin')
@section('title','Service Log Details')
@section('page-title','Service Log Details')
@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <a href="{{ route('admin.service-logs.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back to Service Logs
    </a>
    <div class="d-flex gap-2 align-items-center">
        <span class="badge-status badge-{{ $serviceLog->status }}">
            {{ ucwords(str_replace('_',' ',$serviceLog->status)) }}
        </span>
        @if(auth()->user()->canDo('services.edit'))
        <a href="{{ route('admin.service-logs.edit', $serviceLog) }}" class="btn btn-outline-navy btn-sm">
            <i class="ti ti-pencil me-1"></i> Edit Details
        </a>
        @endif
    </div>
</div>

{{-- Step Tracker --}}
@php
    $steps = [
        ['key' => 'pending',     'label' => 'Recorded',    'icon' => 'ti-file-pencil'],
        ['key' => 'assigned',    'label' => 'Assigned',    'icon' => 'ti-user-check'],
        ['key' => 'in_progress', 'label' => 'In Progress', 'icon' => 'ti-activity'],
        ['key' => 'resolved',    'label' => 'Resolved',    'icon' => 'ti-circle-check'],
        ['key' => 'closed',      'label' => 'Closed',      'icon' => 'ti-archive'],
    ];
    $statusOrder = array_column($steps, 'key');
    $currentIdx  = array_search($serviceLog->status, $statusOrder);
    if ($currentIdx === false) $currentIdx = -1;
    $isCancelled = $serviceLog->status === 'cancelled';
    $isClosed    = $serviceLog->status === 'closed';
@endphp

@if($isCancelled)
<div class="alert alert-danger d-flex align-items-start gap-3 py-3 mb-3">
    <i class="ti ti-alert-triangle" style="font-size:22px;margin-top:2px"></i>
    <div>
        <div class="fw-bold mb-1">Service Log Cancelled / Dismissed</div>
        @if($serviceLog->cancellation_reason)
            <div style="font-size:13.5px">Reason: {{ $serviceLog->cancellation_reason }}</div>
        @endif
    </div>
</div>
@else
{{-- 5-step progress tracker --}}
<div class="card-custom mb-3 py-4 px-3">
    <div class="d-flex align-items-start" style="position:relative">
        {{-- Progress line behind dots --}}
        <div style="position:absolute;top:17px;left:9%;right:9%;height:3px;z-index:0;
            background:linear-gradient(to right,
                #059669 {{ $isClosed ? '100%' : (max(0, $currentIdx) / 4 * 100).'%' }},
                #e2e8f0 {{ $isClosed ? '0%' : (max(0, $currentIdx) / 4 * 100).'%' }});">
        </div>
        @foreach($steps as $i => $step)
            @php
                if ($isClosed) {
                    $state = 'done';
                } elseif ($i < $currentIdx) {
                    $state = 'done';
                } elseif ($i === $currentIdx) {
                    $state = 'current';
                } else {
                    $state = 'upcoming';
                }
            @endphp
            <div class="text-center flex-fill" style="position:relative;z-index:1">
                <div style="width:36px;height:36px;border-radius:50%;margin:0 auto 8px;
                    display:flex;align-items:center;justify-content:center;font-size:16px;
                    @if($state === 'done') background:#059669;color:#fff;
                    @elseif($state === 'current') background:#185fa5;color:#fff;box-shadow:0 0 0 4px rgba(24,95,165,0.18);
                    @else background:#e2e8f0;color:#94a3b8;
                    @endif">
                    <i class="ti {{ $state === 'done' ? 'ti-check' : $step['icon'] }}"></i>
                </div>
                <div style="font-size:11px;line-height:1.3;
                    font-weight:{{ $state === 'current' ? '700' : '500' }};
                    color:{{ $state === 'upcoming' ? '#94a3b8' : '#1e293b' }}">
                    {{ $step['label'] }}
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

<div class="row justify-content-center">
    <div class="col-12 col-lg-9">
        <div class="card-custom">
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
                <div>
                    <div style="font-family:monospace;font-size:14px;font-weight:700;color:#185fa5">{{ $serviceLog->log_number }}</div>
                    <h5 class="mb-0 mt-1">{{ ucwords(str_replace('_',' ',$serviceLog->service_type)) }}</h5>
                </div>
                <span class="badge-status badge-{{ $serviceLog->status }}">
                    {{ ucwords(str_replace('_',' ',$serviceLog->status)) }}
                </span>
            </div>

            {{-- Service Details --}}
            <div class="row g-3 pb-3 border-bottom" style="font-size:13.5px">
                <div class="col-md-6">
                    <span class="text-muted" style="font-size:12px">Resident / Party Involved</span>
                    <div class="fw-semibold">
                        @if($serviceLog->resident)
                            <a href="{{ route('admin.residents.show', $serviceLog->resident_id) }}" style="color:#1a3a6b">
                                {{ $serviceLog->resident->full_name }}
                            </a>
                        @else
                            <span class="text-muted">General Service / Unassigned Resident</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <span class="text-muted" style="font-size:12px">Assigned Staff / Officer</span>
                    <div class="fw-semibold">
                        @if($serviceLog->assignedTo)
                            <i class="ti ti-user-check text-primary me-1"></i>{{ $serviceLog->assignedTo->name }} ({{ ucfirst($serviceLog->assignedTo->role) }})
                        @else
                            <span class="text-warning"><i class="ti ti-alert-circle me-1"></i>Unassigned</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <span class="text-muted" style="font-size:12px">Date of Service</span>
                    <div>{{ optional($serviceLog->date_of_service)->format('M d, Y') }}</div>
                </div>
                <div class="col-md-6">
                    <span class="text-muted" style="font-size:12px">Logged By</span>
                    <div>{{ optional($serviceLog->createdBy)->name ?: 'System' }} on {{ $serviceLog->created_at->format('M d, Y g:i A') }}</div>
                </div>
                <div class="col-12">
                    <span class="text-muted" style="font-size:12px">Description / Narrative</span>
                    <div class="p-3 bg-light rounded mt-1" style="line-height:1.6;color:#333">{{ $serviceLog->description }}</div>
                </div>
                @if($serviceLog->remarks)
                <div class="col-12">
                    <span class="text-muted" style="font-size:12px">Staff Remarks / Notes</span>
                    <div class="p-2 bg-light rounded mt-1">{{ $serviceLog->remarks }}</div>
                </div>
                @endif

                @if($serviceLog->resolution_notes)
                <div class="col-12">
                    <span class="text-muted" style="font-size:12px">Resolution Summary / Outcome</span>
                    <div class="p-3 rounded mt-1" style="background:#ecfdf5;border-left:4px solid #059669;color:#065f46">
                        <div class="fw-bold mb-1"><i class="ti ti-check me-1"></i>Resolved</div>
                        <div>{{ $serviceLog->resolution_notes }}</div>
                        @if($serviceLog->resolved_at)
                            <div class="small mt-2 text-muted">Completed on {{ $serviceLog->resolved_at->format('M d, Y g:i A') }}</div>
                        @endif
                    </div>
                </div>
                @endif

                @if($serviceLog->closed_at)
                <div class="col-md-6">
                    <span class="text-muted" style="font-size:12px">Closed / Archived Date</span>
                    <div>{{ $serviceLog->closed_at->format('M d, Y g:i A') }}</div>
                </div>
                @endif
            </div>

            {{-- ═══ Progressive Action Buttons ═══ --}}
            <div class="mt-4">
                <div class="fw-semibold mb-3" style="font-size:12.5px;color:#64748b;text-transform:uppercase;letter-spacing:0.5px">
                    <i class="ti ti-player-play me-1 text-primary"></i> Actions
                </div>
                <div class="d-flex gap-2 flex-wrap align-items-center">

                    {{-- Pending: Assign button --}}
                    @if($serviceLog->status === 'pending' && auth()->user()->canDo('services.assign'))
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignModal">
                            <i class="ti ti-user-plus me-1"></i> Assign Officer &amp; Schedule
                        </button>
                    @endif

                    {{-- Assigned: Start button or Reassign --}}
                    @if($serviceLog->status === 'assigned')
                        @if(auth()->user()->canDo('services.status'))
                        <form method="POST" action="{{ route('admin.service-logs.status', $serviceLog) }}">
                            @csrf
                            <input type="hidden" name="action" value="start">
                            <button type="submit" class="btn btn-success">
                                <i class="ti ti-activity me-1"></i> Start Service / Ongoing
                            </button>
                        </form>
                        @endif
                        @if(auth()->user()->canDo('services.assign'))
                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#assignModal">
                            <i class="ti ti-refresh me-1"></i> Reassign / Reschedule
                        </button>
                        @endif
                    @endif

                    {{-- In Progress: Resolve button --}}
                    @if($serviceLog->status === 'in_progress' && auth()->user()->canDo('services.status'))
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#resolveModal">
                            <i class="ti ti-circle-check me-1"></i> Mark as Resolved / Completed
                        </button>
                    @endif

                    {{-- Resolved: Close button --}}
                    @if($serviceLog->status === 'resolved' && auth()->user()->canDo('services.status'))
                        <form method="POST" action="{{ route('admin.service-logs.status', $serviceLog) }}">
                            @csrf
                            <input type="hidden" name="action" value="close">
                            <button type="submit" class="btn btn-navy" onclick="return confirm('Close and archive this service log?')">
                                <i class="ti ti-archive me-1"></i> Close &amp; Archive Record
                            </button>
                        </form>
                    @endif

                    {{-- Reopen option for Resolved, Closed, or Cancelled --}}
                    @if(in_array($serviceLog->status, ['resolved', 'closed', 'cancelled']) && auth()->user()->canDo('services.status'))
                        <form method="POST" action="{{ route('admin.service-logs.status', $serviceLog) }}">
                            @csrf
                            <input type="hidden" name="action" value="reopen">
                            <button type="submit" class="btn btn-outline-secondary btn-sm" onclick="return confirm('Reopen this record for further action?')">
                                <i class="ti ti-rotate-clockwise me-1"></i> Reopen Record
                            </button>
                        </form>
                    @endif

                    {{-- Cancel button (available for active statuses) --}}
                    @if(!in_array($serviceLog->status, ['closed', 'cancelled']) && auth()->user()->canDo('services.status'))
                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal">
                            <i class="ti ti-x me-1"></i> Cancel / Dismiss
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Assign Modal --}}
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.service-logs.assign', $serviceLog) }}">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title fw-bold"><i class="ti ti-user-check text-primary me-2"></i>Assign Staff &amp; Schedule Service</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px">Assign Staff / Officer <span class="text-danger">*</span></label>
                        <select name="assigned_to" class="form-select" required>
                            <option value="">Select staff...</option>
                            @foreach($staff as $s)
                                <option value="{{ $s->id }}" {{ $serviceLog->assigned_to == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }} ({{ ucfirst($s->role) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px">Date of Service / Hearing <span class="text-danger">*</span></label>
                        <input type="date" name="date_of_service" class="form-control"
                            value="{{ old('date_of_service', optional($serviceLog->date_of_service)->format('Y-m-d') ?: date('Y-m-d')) }}" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold" style="font-size:13px">Instructions / Remarks</label>
                        <textarea name="remarks" class="form-control" rows="2" placeholder="Specific instructions for assigned staff...">{{ $serviceLog->remarks }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-check me-1"></i>Confirm Assignment</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Resolve Modal --}}
<div class="modal fade" id="resolveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.service-logs.status', $serviceLog) }}">
                @csrf
                <input type="hidden" name="action" value="resolve">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold"><i class="ti ti-circle-check text-success me-2"></i>Resolve &amp; Complete Service Log</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label fw-semibold" style="font-size:13px">Resolution Summary / Action Taken <span class="text-danger">*</span></label>
                    <textarea name="resolution_notes" class="form-control" rows="3" required
                        placeholder="e.g. Mediation completed with signed settlement agreement; medical assistance distributed; inspection completed and cleared..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm"><i class="ti ti-check me-1"></i>Confirm Resolution</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Cancel Modal --}}
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.service-logs.status', $serviceLog) }}">
                @csrf
                <input type="hidden" name="action" value="cancel">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold"><i class="ti ti-alert-triangle text-danger me-2"></i>Cancel / Dismiss Service Log</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label fw-semibold" style="font-size:13px">Reason for Cancellation / Dismissal <span class="text-danger">*</span></label>
                    <textarea name="cancellation_reason" class="form-control" rows="3" required
                        placeholder="e.g. Resident withdrew complaint; duplicate log entry; party not amenable to mediation..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm"><i class="ti ti-x me-1"></i>Confirm Cancellation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
