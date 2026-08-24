@extends('layouts.admin')
@section('title','Issue Mapping')
@section('page-title','Geographic Issue Map')
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>#map{height:420px;border-radius:12px;overflow:hidden;border:1px solid #e5e9f0}</style>
@endpush
@section('content')
<div class="row g-3 mt-1 mb-3">
    <div class="col-6 col-md-3"><div class="stat-card"><div style="display:flex;align-items:center;gap:6px"><div style="width:10px;height:10px;border-radius:50%;background:#E24B4A;flex-shrink:0"></div><div class="stat-label" style="margin:0">Streetlights</div></div><div class="stat-value" style="font-size:22px">{{ \App\Models\CitizenRequest::where('request_type','broken_streetlight')->whereNotIn('status',['closed'])->count() }}</div></div></div>
    <div class="col-6 col-md-3"><div class="stat-card"><div style="display:flex;align-items:center;gap:6px"><div style="width:10px;height:10px;border-radius:50%;background:#BA7517;flex-shrink:0"></div><div class="stat-label" style="margin:0">Garbage</div></div><div class="stat-value" style="font-size:22px">{{ \App\Models\CitizenRequest::where('request_type','garbage_collection')->whereNotIn('status',['closed'])->count() }}</div></div></div>
    <div class="col-6 col-md-3"><div class="stat-card"><div style="display:flex;align-items:center;gap:6px"><div style="width:10px;height:10px;border-radius:50%;background:#185FA5;flex-shrink:0"></div><div class="stat-label" style="margin:0">Flooding</div></div><div class="stat-value" style="font-size:22px">{{ \App\Models\CitizenRequest::where('request_type','flooding')->whereNotIn('status',['closed'])->count() }}</div></div></div>
    <div class="col-6 col-md-3"><div class="stat-card"><div style="display:flex;align-items:center;gap:6px"><div style="width:10px;height:10px;border-radius:50%;background:#555;flex-shrink:0"></div><div class="stat-label" style="margin:0">Road Damage</div></div><div class="stat-value" style="font-size:22px">{{ \App\Models\CitizenRequest::where('request_type','road_damage')->whereNotIn('status',['closed'])->count() }}</div></div></div>
</div>
<div class="card-custom">
    <div id="map"></div>
</div>
@endsection
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
fetch('{{ route("admin.mapping.data") }}')
    .then(r => r.json())
    .then(issues => initMap(issues));
</script>
@endpush
