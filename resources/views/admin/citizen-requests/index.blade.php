@extends('layouts.admin')
@section('title','Citizen Requests')
@section('page-title','Citizen Requests')
@section('content')
<div class="d-flex align-items-center justify-content-between mt-2 mb-3 flex-wrap gap-2">
    <form method="GET" class="d-flex gap-2 flex-wrap">
        <input type="text" name="search" class="form-control form-control-sm" placeholder="Tracking number..." value="{{ request('search') }}" style="width:180px">
        <select name="status" class="form-select form-select-sm" style="width:140px">
            <option value="">All statuses</option>
            @foreach(\App\Models\CitizenRequest::STATUSES as $s)
                <option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
            @endforeach
        </select>
        <select name="type" class="form-select form-select-sm" style="width:160px">
            <option value="">All types</option>
            @foreach(\App\Models\CitizenRequest::TYPES as $t)
                <option value="{{ $t }}" {{ request('type')==$t?'selected':'' }}>{{ ucwords(str_replace('_',' ',$t)) }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-navy btn-sm">Filter</button>
    </form>
</div>
@foreach($requests as $req)
<div class="card-custom mb-3">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
        <div>
            <span style="font-size:12px;font-weight:600;color:#185fa5;font-family:monospace">{{ $req->tracking_number }}</span>
            <h6 class="mb-0 mt-1">{{ ucwords(str_replace('_',' ',$req->request_type)) }}</h6>
            <small class="text-muted">{{ optional($req->resident)->full_name ?? 'Anonymous' }} · {{ $req->created_at->format('M d, Y') }}</small>
        </div>
        <span class="badge-status badge-{{ $req->status }}">{{ ucwords(str_replace('_',' ',$req->status)) }}</span>
    </div>
    <p class="mb-2" style="font-size:13.5px;color:#555">{{ Str::limit($req->description, 120) }}</p>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <small class="text-muted"><i class="ti ti-map-pin"></i> {{ $req->location }}</small>
        <div class="ms-auto d-flex gap-2">
            <a href="{{ route('admin.citizen-requests.show',$req) }}" class="btn btn-navy btn-sm">View details</a>
        </div>
    </div>
    <div class="step-tracker mt-3">
        @php $statuses = ['pending','under_review','assigned','in_progress','resolved']; @endphp
        @foreach($statuses as $s)
            @php
                $statusIdx = array_search($req->status, $statuses);
                $currentIdx = array_search($s, $statuses);
                $state = $currentIdx < $statusIdx ? 'done' : ($currentIdx == $statusIdx ? 'current' : '');
            @endphp
            <div class="step-item {{ $state }}">
                <div class="step-dot {{ $state }}">
                    @if($state == 'done')<i class="ti ti-check" style="font-size:10px"></i>
                    @elseif($state == 'current')<i class="ti ti-clock" style="font-size:10px"></i>
                    @endif
                </div>
                <div class="step-label">{{ ucwords(str_replace('_',' ',$s)) }}</div>
            </div>
        @endforeach
    </div>
</div>
@endforeach
<div class="mt-3">{{ $requests->links() }}</div>
@endsection
