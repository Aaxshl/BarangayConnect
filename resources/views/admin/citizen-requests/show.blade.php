@extends('layouts.admin')
@section('title','Request Details')
@section('page-title','Request Details')
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
#request-map { height: 220px; border-radius: 10px; width: 100%; }
.photo-lightbox { cursor: zoom-in; transition: transform .2s; }
.photo-lightbox:hover { transform: scale(1.02); }
</style>
@endpush
@section('content')
<div class="mb-3">
    <a href="{{ route('admin.citizen-requests.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back to Requests
    </a>
</div>
<div class="row">
    <div class="col-12 col-lg-8 mb-3">
        <div class="card-custom mb-3">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                <div>
                    <div style="font-family:monospace;font-size:13px;font-weight:700;color:#185fa5">{{ $citizenRequest->tracking_number }}</div>
                    <h5 class="mb-1 mt-1">{{ ucwords(str_replace('_',' ',$citizenRequest->request_type)) }}</h5>
                    <small class="text-muted">Submitted {{ $citizenRequest->created_at->format('M d, Y \a\t g:i A') }}</small>
                </div>
                <span class="badge-status badge-{{ $citizenRequest->status }}">{{ ucwords(str_replace('_',' ',$citizenRequest->status)) }}</span>
            </div>
            <div class="step-tracker mb-4">
                @php $statuses = ['pending','under_review','assigned','in_progress','resolved']; @endphp
                @foreach($statuses as $s)
                    @php
                        $idx = array_search($citizenRequest->status, $statuses);
                        $cur = array_search($s, $statuses);
                        $state = $cur < $idx ? 'done' : ($cur == $idx ? 'current' : '');
                    @endphp
                    <div class="step-item {{ $state }}">
                        <div class="step-dot {{ $state }}">
                            @if($state=='done')<i class="ti ti-check" style="font-size:10px"></i>
                            @elseif($state=='current')<i class="ti ti-clock" style="font-size:10px"></i>@endif
                        </div>
                        <div class="step-label">{{ ucwords(str_replace('_',' ',$s)) }}</div>
                    </div>
                @endforeach
            </div>
            <h6 class="fw-semibold">Description</h6>
            <p style="font-size:13.5px;color:#444;line-height:1.6">{{ $citizenRequest->description }}</p>

            {{-- Location Section --}}
            <div class="mb-3">
                <h6 class="fw-semibold mb-2"><i class="ti ti-map-pin me-1 text-danger"></i>Location</h6>
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    <span style="font-size:13.5px">{{ $citizenRequest->location }}</span>
                    @if($citizenRequest->latitude && $citizenRequest->longitude)
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $citizenRequest->latitude }},{{ $citizenRequest->longitude }}"
                           target="_blank" class="btn btn-sm btn-outline-danger py-0 px-2">
                            <i class="ti ti-brand-google-maps me-1"></i>Get Directions
                        </a>
                    @endif
                </div>
                @if($citizenRequest->latitude && $citizenRequest->longitude)
                    <div id="request-map"></div>
                    <div class="text-muted small mt-1">
                        GPS: {{ number_format($citizenRequest->latitude, 6) }}, {{ number_format($citizenRequest->longitude, 6) }}
                    </div>
                @else
                    <div class="alert alert-secondary py-2 mb-0 small">No GPS coordinates available for this report.</div>
                @endif
            </div>

            {{-- Submitted Photo --}}
            @if($citizenRequest->photo)
            <div class="mb-2">
                <h6 class="fw-semibold mb-2"><i class="ti ti-photo me-1"></i>Uploaded Photo</h6>
                <a href="{{ asset('storage/'.$citizenRequest->photo) }}" target="_blank">
                    <img src="{{ asset('storage/'.$citizenRequest->photo) }}"
                         class="photo-lightbox rounded"
                         style="max-width:100%;max-height:320px;object-fit:cover;border:2px solid #e0e8f4"
                         alt="Report photo">
                </a>
                <div class="text-muted small mt-1">Click to view full size</div>
            </div>
            @endif

            <div class="row g-2 mt-2" style="font-size:13.5px">
                <div class="col-md-6"><span class="text-muted">Submitted by</span><div>{{ optional($citizenRequest->resident)->full_name ?? 'Anonymous' }}</div></div>
                <div class="col-md-6"><span class="text-muted">Assigned to</span><div>{{ optional($citizenRequest->assignedTo)->name ?? 'Unassigned' }}</div></div>
                @if($citizenRequest->resolved_at)<div class="col-md-6"><span class="text-muted">Resolved</span><div>{{ $citizenRequest->resolved_at->format('M d, Y') }}</div></div>@endif
                @if($citizenRequest->resolution_note)<div class="col-12"><span class="text-muted">Resolution note</span><div class="p-2 bg-light rounded mt-1">{{ $citizenRequest->resolution_note }}</div></div>@endif
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4 mb-3">
        <div class="card-custom mb-3">
            <h6 class="fw-semibold mb-3">Assign to staff</h6>
            <form method="POST" action="{{ route('admin.citizen-requests.assign',$citizenRequest) }}">
                @csrf
                <div class="mb-2"><select name="assigned_to" class="form-select form-select-sm"><option value="">Select staff...</option>@foreach($staff as $u)<option value="{{ $u->id }}" {{ $citizenRequest->assigned_to==$u->id?'selected':'' }}>{{ $u->name }}</option>@endforeach</select></div>
                <button type="submit" class="btn btn-navy btn-sm w-100">Assign</button>
            </form>
        </div>
        <div class="card-custom mb-3">
            <h6 class="fw-semibold mb-3">Update status</h6>
            <form method="POST" action="{{ route('admin.citizen-requests.status',$citizenRequest) }}">
                @csrf
                <div class="mb-2">
                    <label class="form-label small text-muted mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        @foreach(\App\Models\CitizenRequest::STATUSES as $s)
                        <option value="{{ $s }}" {{ $citizenRequest->status==$s?'selected':'' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label small text-muted mb-1">Resolution Note</label>
                    <textarea name="resolution_note" class="form-control form-control-sm" rows="3" placeholder="Describe how the issue was resolved...">{{ $citizenRequest->resolution_note }}</textarea>
                </div>
                <button type="submit" class="btn btn-navy btn-sm w-100">Save Status</button>
            </form>
        </div>
        <div class="card-custom">
            <h6 class="fw-semibold mb-2">Actions</h6>
            <form method="POST" action="{{ route('admin.citizen-requests.convert',$citizenRequest) }}">
                @csrf
                <button type="submit" class="btn btn-outline-navy btn-sm w-100 mb-2"><i class="ti ti-list-check me-1"></i>Convert to service log</button>
            </form>
            <form method="POST" action="{{ route('admin.citizen-requests.destroy',$citizenRequest) }}">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-outline-secondary btn-sm w-100"><i class="ti ti-x me-1"></i>Close request</button>
            </form>
        </div>
    </div>
</div>
@push('scripts')
@if($citizenRequest->latitude && $citizenRequest->longitude)
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const lat = {{ $citizenRequest->latitude }};
    const lng = {{ $citizenRequest->longitude }};
    const map = L.map('request-map').setView([lat, lng], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    const marker = L.marker([lat, lng]).addTo(map);
    marker.bindPopup('<b>{{ addslashes($citizenRequest->location) }}</b><br>{{ addslashes(ucwords(str_replace("_"," ",$citizenRequest->request_type))) }}').openPopup();
});
</script>
@endif
@endpush
@endsection
