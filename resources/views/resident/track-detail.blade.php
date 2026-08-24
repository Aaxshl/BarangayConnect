@extends('layouts.portal')
@section('title','Track — {{ $tracking }}')
@section('content')
<div class="container-fluid px-3 px-md-4 mt-4" style="max-width:680px">
    <a href="{{ route('portal.track') }}" style="font-size:13px;color:#1a3a6b;text-decoration:none;display:flex;align-items:center;gap:5px;margin-bottom:16px"><i class="ti ti-arrow-left"></i> Back to all requests</a>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="portal-card">
        <div class="request-id mb-2">{{ $tracking }}</div>
        @if($item instanceof \App\Models\CitizenRequest)
            <h5>{{ ucwords(str_replace('_',' ',$item->request_type)) }}</h5>
            <p style="font-size:13.5px;color:#555;line-height:1.6">{{ $item->description }}</p>
            <div style="font-size:13px;color:#888"><i class="ti ti-map-pin"></i> {{ $item->location }}</div>
            <div class="step-tracker mt-4">
                @php $statuses = ['pending','under_review','assigned','in_progress','resolved']; @endphp
                @foreach($statuses as $s)
                    @php
                        $idx = array_search($item->status, $statuses);
                        $cur = array_search($s, $statuses);
                        $state = $cur < $idx ? 'done' : ($cur == $idx ? 'current' : '');
                    @endphp
                    <div class="step-item {{ $state }}">
                        <div class="step-dot {{ $state }}">@if($state=='done')<i class="ti ti-check" style="font-size:9px"></i>@elseif($state=='current')<i class="ti ti-clock" style="font-size:9px"></i>@endif</div>
                        <div class="step-label">{{ ucwords(str_replace('_',' ',$s)) }}</div>
                    </div>
                @endforeach
            </div>
            @if($item->resolution_note)<div class="mt-3 pt-3 border-top" style="font-size:13px;color:#555"><i class="ti ti-message me-1"></i>{{ $item->resolution_note }}</div>@endif
        @else
            <h5>{{ \App\Models\Document::TYPES[$item->document_type] ?? $item->document_type }}</h5>
            <div style="font-size:13.5px">Purpose: {{ $item->purpose }}</div>
            <div class="mt-3"><span class="badge-status badge-{{ $item->status }}">{{ ucwords(str_replace('_',' ',$item->status)) }}</span></div>
            @if($item->status === 'pending_pickup')
            <div class="info-banner mt-3"><i class="ti ti-building"></i>Ready for pickup at the Barangay Hall. Mon–Fri, 8 AM – 5 PM. Bring a valid ID.</div>
            @endif
        @endif
    </div>
</div>
@endsection
