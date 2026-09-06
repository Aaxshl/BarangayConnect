@extends('layouts.portal')
@section('title', 'Careers & Opportunities — Barangay San Jose')
@section('content')

<div class="container-fluid px-3 px-md-5 my-4">
    {{-- Back Button --}}
    <div class="mb-3">
        <a href="{{ session('resident_id') ? route('portal.dashboard') : route('portal.index') }}" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1" style="border-radius:8px">
            <i class="ti ti-arrow-left"></i> Back
        </a>
    </div>

    {{-- Hero Banner --}}
    <div class="card-custom p-4 p-md-5 mb-4 text-white" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); border-radius: 20px;">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-white text-success fw-bold text-uppercase px-3 py-2 mb-3" style="letter-spacing: 1px;">Community Employment &amp; Service</span>
                <h1 class="fw-extrabold display-5 mb-3" style="font-weight: 800;">Careers &amp; Opportunities</h1>
                <p class="lead mb-0 text-white-50" style="font-size: 1.15rem; line-height: 1.7;">
                    Explore job openings, volunteer programs, and community livelihood opportunities serving {{ $settings['barangay_name'] ?? 'Barangay San Jose' }}.
                </p>
            </div>
            <div class="col-lg-4 text-center text-lg-end mt-4 mt-lg-0">
                <div style="width: 110px; height: 110px; border-radius: 50%; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); display: inline-flex; align-items: center; justify-content: center; font-size: 54px;">
                    💼
                </div>
            </div>
        </div>
    </div>

    {{-- Job Openings / Opportunities List --}}
    <div class="row g-4 mb-5">
        {{-- 1. Tanod Officer --}}
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card-custom p-4 h-100 border-0 shadow-sm d-flex flex-column" style="border-radius: 16px; background: #fff;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="badge bg-success bg-opacity-10 text-success fw-bold px-2.5 py-1" style="font-size: 11px;">Full-Time / Shift</span>
                    <span class="text-muted small"><i class="ti ti-clock me-1"></i>Active</span>
                </div>
                <h5 class="fw-bold text-dark mb-2">Barangay Tanod Officer</h5>
                <p class="text-muted small mb-4 flex-grow-1" style="line-height: 1.6;">
                    Assist the Barangay Council in patrolling streets, ensuring public safety, and maintaining peace and order across all puroks.
                </p>
                <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                    <span class="small fw-semibold text-dark"><i class="ti ti-cash me-1 text-success"></i>Monthly Honorarium</span>
                    <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#careerApplyModal" data-job="Barangay Tanod Officer">
                        How to Apply
                    </button>
                </div>
            </div>
        </div>

        {{-- 2. Health Worker --}}
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card-custom p-4 h-100 border-0 shadow-sm d-flex flex-column" style="border-radius: 16px; background: #fff;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-2.5 py-1" style="font-size: 11px;">Community Service</span>
                    <span class="text-muted small"><i class="ti ti-clock me-1"></i>Active</span>
                </div>
                <h5 class="fw-bold text-dark mb-2">Barangay Health Worker (BHW)</h5>
                <p class="text-muted small mb-4 flex-grow-1" style="line-height: 1.6;">
                    Assist local health doctors and nurses during community vaccinations, maternal care checkups, and senior citizen wellness days.
                </p>
                <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                    <span class="small fw-semibold text-dark"><i class="ti ti-heart me-1 text-danger"></i>Allowance &amp; Training</span>
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#careerApplyModal" data-job="Barangay Health Worker (BHW)">
                        How to Apply
                    </button>
                </div>
            </div>
        </div>

        {{-- 3. Administrative Desk Assistant --}}
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card-custom p-4 h-100 border-0 shadow-sm d-flex flex-column" style="border-radius: 16px; background: #fff;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="badge bg-info bg-opacity-10 text-info fw-bold px-2.5 py-1" style="font-size: 11px;">Office Staff</span>
                    <span class="text-muted small"><i class="ti ti-clock me-1"></i>Open</span>
                </div>
                <h5 class="fw-bold text-dark mb-2">Administrative Desk Assistant</h5>
                <p class="text-muted small mb-4 flex-grow-1" style="line-height: 1.6;">
                    Provide frontline assistance to residents processing clearances, managing queue numbers, and organizing official records.
                </p>
                <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                    <span class="small fw-semibold text-dark"><i class="ti ti-building me-1 text-info"></i>Office Environment</span>
                    <button type="button" class="btn btn-sm btn-outline-info text-dark" data-bs-toggle="modal" data-bs-target="#careerApplyModal" data-job="Administrative Desk Assistant">
                        How to Apply
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Application Details Card --}}
    <div class="card-custom p-4 border-0 shadow-sm" style="border-radius: 16px; background: #f8fafc;">
        <div class="d-flex align-items-start gap-3">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;">
                <i class="ti ti-info-circle"></i>
            </div>
            <div>
                <h6 class="fw-bold text-dark mb-1">Application Submission Guidelines</h6>
                <p class="text-muted small mb-0" style="line-height: 1.6;">
                    All job applications, volunteer sign-ups, and resume submissions are handled directly at the <strong>Barangay Office Front Desk</strong> during official office hours (Monday to Friday, 8:00 AM – 5:00 PM). Please bring a hard copy of your Resume / Bio-data and a valid Government ID.
                </p>
            </div>
        </div>
    </div>
</div>

{{-- On-Page Application Information Modal (No Redirection) --}}
<div class="modal fade" id="careerApplyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.12);">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold" id="applyModalTitle">Application Guidelines</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3">
                <div class="p-3 bg-light rounded mb-3">
                    <div class="small text-muted">Selected Role:</div>
                    <div class="fw-bold fs-6 text-dark" id="applyModalJobName">Barangay Personnel</div>
                </div>
                <p class="small text-muted mb-3" style="line-height: 1.6;">
                    To apply for this opportunity, please prepare the following required documents and proceed to the <strong>Barangay Secretary's Office</strong>:
                </p>
                <ul class="small text-muted mb-3 ps-3" style="line-height: 1.8;">
                    <li>Updated Resume or Bio-data with 2x2 ID Photo</li>
                    <li>Barangay Clearance (Issued within the last 6 months)</li>
                    <li>Photocopy of Valid Government ID or Resident ID</li>
                    <li>Certificate of Good Moral Character (if applicable)</li>
                </ul>
                <div class="small p-2.5 rounded text-dark" style="background: #eff6ff; border: 1px solid #bfdbfe;">
                    <i class="ti ti-clock me-1 text-primary"></i> <strong>Office Hours:</strong> Mon–Fri, 8:00 AM – 5:00 PM<br>
                    <i class="ti ti-map-pin me-1 text-danger"></i> <strong>Location:</strong> Barangay Hall Front Desk
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-primary w-100" data-bs-dismiss="modal" style="border-radius: 10px;">Got It</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const applyModal = document.getElementById('careerApplyModal');
        if (applyModal) {
            applyModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const jobTitle = button ? button.getAttribute('data-job') : 'Barangay Role';
                document.getElementById('applyModalJobName').textContent = jobTitle;
            });
        }
    });
</script>
@push('styles')
<style>
    .portal-desktop-topbar, .portal-nav, .mobile-bottom-nav { display: none !important; }
    main { padding-top: 16px !important; padding-bottom: 40px !important; }
</style>
@endpush

@endsection
