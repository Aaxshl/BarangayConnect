@extends('layouts.portal')
@section('title','My Profile')
@section('content')
<div class="container-fluid px-3 px-md-4 mt-4" style="max-width:680px">
    <div class="profile-hero mb-3">
        <div class="profile-av">{{ substr($resident->first_name,0,1) }}{{ substr($resident->last_name,0,1) }}</div>
        <div>
            <div class="profile-name">{{ $resident->full_name }}</div>
            <div class="profile-meta">RES-{{ str_pad($resident->id,4,'0',STR_PAD_LEFT) }} · Registered since {{ $resident->created_at->format('Y') }}</div>
        </div>
    </div>

    <div class="portal-card mb-3">
        <div class="portal-card-title">Personal information</div>
        <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:13.5px"><span class="text-muted">Full name</span><span>{{ $resident->full_name }}</span></div>
        <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:13.5px"><span class="text-muted">Age</span><span>{{ $resident->birthdate ? $resident->birthdate->age : '—' }}</span></div>
        <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:13.5px"><span class="text-muted">Gender</span><span>{{ ucfirst($resident->gender) }}</span></div>
        <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:13.5px"><span class="text-muted">Civil status</span><span>{{ ucfirst($resident->civil_status) }}</span></div>
        <div class="d-flex justify-content-between py-2" style="font-size:13.5px"><span class="text-muted">Occupation</span><span>{{ $resident->occupation ?: '—' }}</span></div>
    </div>

    <div class="portal-card mb-3">
        <div class="portal-card-title">Update contact details</div>
        <form method="POST" action="{{ route('portal.profile.update') }}">
            @csrf @method('PUT')
            <div class="mb-3"><label class="form-label">Mobile number</label><input type="text" name="contact_number" class="form-control" value="{{ $resident->contact_number }}"></div>
            <div class="mb-3"><label class="form-label">Address</label><input type="text" name="address" class="form-control" value="{{ $resident->address }}"></div>
            <button type="submit" class="btn-navy-full"><i class="ti ti-check"></i> Update profile</button>
        </form>
    </div>
</div>
@endsection
