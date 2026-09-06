@extends('layouts.portal')
@section('title', 'Who We Are — Barangay San Jose')
@section('content')

<div class="container-fluid px-3 px-md-5 my-4">
    {{-- Back to Portal Button --}}
    <div class="mb-3">
        <a href="{{ session('resident_id') ? route('portal.dashboard') : route('portal.index') }}" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1" style="border-radius:8px">
            <i class="ti ti-arrow-left"></i> Back to Portal
        </a>
    </div>

    {{-- Hero Header --}}
    <div class="card-custom p-4 p-md-5 mb-4 text-white" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); border-radius: 20px;">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-white text-primary fw-bold text-uppercase px-3 py-2 mb-3" style="letter-spacing: 1px;">About Our Barangay</span>
                <h1 class="fw-extrabold display-5 mb-3" style="font-weight: 800;">Who We Are</h1>
                <p class="lead mb-0 text-white-50" style="font-size: 1.15rem; line-height: 1.7;">
                    Dedicated to transparent governance, rapid public service delivery, and empowering every resident in {{ $settings['barangay_name'] ?? 'Barangay San Jose' }}.
                </p>
            </div>
            <div class="col-lg-4 text-center text-lg-end mt-4 mt-lg-0">
                <div style="width: 110px; height: 110px; border-radius: 50%; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); display: inline-flex; align-items: center; justify-content: center; font-size: 54px;">
                    🏛️
                </div>
            </div>
        </div>
    </div>

    {{-- Mission & Vision Cards --}}
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="card-custom p-4 h-100 border-0 shadow-sm" style="border-radius: 16px; background: #ffffff;">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                        <i class="ti ti-target-arrow"></i>
                    </div>
                    <h4 class="fw-bold mb-0 text-dark">Our Mission</h4>
                </div>
                <p class="text-muted mb-0" style="line-height: 1.7; font-size: 14.5px;">
                    To deliver compassionate, progressive, and technologically powered public services to every constituent, maintaining peace and order, promoting health and welfare, and fostering community participation.
                </p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card-custom p-4 h-100 border-0 shadow-sm" style="border-radius: 16px; background: #ffffff;">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #f0fdf4; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                        <i class="ti ti-eye"></i>
                    </div>
                    <h4 class="fw-bold mb-0 text-dark">Our Vision</h4>
                </div>
                <p class="text-muted mb-0" style="line-height: 1.7; font-size: 14.5px;">
                    A modern, resilient, and inclusive Smart Barangay where citizens thrive in a safe, healthy, and digitally connected environment driven by accountable leadership.
                </p>
            </div>
        </div>
    </div>

    {{-- Barangay Council Directory --}}
    <div class="mb-5">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1"><i class="ti ti-users me-2 text-primary"></i>Barangay Council &amp; Officials</h3>
                <div class="text-muted small">Leaders working together for our community's development</div>
            </div>
        </div>

        <div class="row g-4">
            @forelse($councilors as $official)
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card-custom p-3 text-center border-0 shadow-sm h-100" style="border-radius: 14px; background: #fff; transition: transform 0.2s;">
                    <div style="width: 72px; height: 72px; border-radius: 50%; background: linear-gradient(135deg, #2563eb, #1d4ed8); color: #fff; font-size: 28px; font-weight: 700; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                        {{ substr($official->name, 0, 1) }}
                    </div>
                    <h6 class="fw-bold mb-1 text-dark">{{ $official->name }}</h6>
                    <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold px-2 py-1" style="font-size: 11px;">
                        {{ $official->role_label }}
                    </span>
                    <div class="mt-2 text-muted small"><i class="ti ti-mail me-1"></i>{{ $official->email }}</div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-4 text-muted">
                <i class="ti ti-user-x fs-1 d-block mb-2 text-secondary"></i>
                Barangay Council directory information is currently being updated.
            </div>
            @endforelse
        </div>
    </div>

    {{-- Contact Info Card --}}
    <div class="card-custom p-4 border-0 shadow-sm" style="border-radius: 16px; background: #f8fafc;">
        <div class="row align-items-center">
            <div class="col-12">
                <h5 class="fw-bold text-dark mb-2"><i class="ti ti-map-pin text-danger me-2"></i>Visit Our Barangay Hall</h5>
                <p class="text-muted mb-1 small"><i class="ti ti-location me-1"></i>{{ $settings['barangay_address'] ?? 'San Pedro City, Laguna, Philippines' }}</p>
                <p class="text-muted mb-0 small"><i class="ti ti-phone me-1"></i>{{ $settings['contact_number'] ?? '(02) 8888-0000' }} &bull; <i class="ti ti-mail me-1"></i>{{ $settings['email'] ?? 'contact@barangaysanjose.gov.ph' }}</p>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .portal-desktop-topbar, .portal-nav, .mobile-bottom-nav { display: none !important; }
    main { padding-top: 16px !important; padding-bottom: 40px !important; }
</style>
@endpush

@endsection
