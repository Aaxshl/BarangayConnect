@extends('layouts.portal')
@section('title', request('tab') === 'careers' ? 'Careers & Opportunities' : 'Barangay Updates')
@section('content')
<div class="container-fluid px-3 px-md-4 mt-4">
    <div class="mb-3">
        <a href="{{ session('resident_id') ? route('portal.dashboard') : route('portal.index') }}" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1" style="border-radius:8px">
            <i class="ti ti-arrow-left"></i> Back
        </a>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <div class="text-uppercase fw-bold small text-primary" style="letter-spacing:0.8px">Barangay Updates</div>
            <h2 class="section-title mb-0" style="font-size:22px;font-weight:800;color:#1e293b">
                {{ request('tab') === 'careers' ? 'Careers & Opportunities' : 'Announcements & Advisories' }}
            </h2>
        </div>
    </div>

    {{-- Source Filter Pills & Tabs --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('portal.announcements', request()->only('type')) }}" 
               class="btn btn-sm {{ !request('source') && request('tab') !== 'careers' ? 'btn-primary text-white' : 'btn-outline-secondary' }}" 
               style="border-radius:20px;padding:5px 16px;font-weight:600;font-size:13px">
                All Announcements <span class="badge {{ !request('source') && request('tab') !== 'careers' ? 'bg-light text-dark' : 'bg-secondary' }} ms-1">{{ $counts['all'] ?? $announcements->total() }}</span>
            </a>
            <a href="{{ route('portal.announcements', array_merge(request()->only('type'), ['source' => 'barangay'])) }}" 
               class="btn btn-sm {{ request('source') === 'barangay' && request('tab') !== 'careers' ? 'btn-primary text-white' : 'btn-outline-secondary' }}" 
               style="border-radius:20px;padding:5px 16px;font-weight:600;font-size:13px">
                <i class="ti ti-building-community me-1"></i>Barangay Office <span class="badge {{ request('source') === 'barangay' ? 'bg-light text-dark' : 'bg-secondary' }} ms-1">{{ $counts['barangay'] ?? '' }}</span>
            </a>
            <a href="{{ route('portal.announcements', array_merge(request()->only('type'), ['source' => 'sk'])) }}" 
               class="btn btn-sm {{ request('source') === 'sk' && request('tab') !== 'careers' ? 'btn-warning text-dark' : 'btn-outline-secondary' }}" 
               style="border-radius:20px;padding:5px 16px;font-weight:700;font-size:13px">
                ⚡ Sangguniang Kabataan <span class="badge {{ request('source') === 'sk' ? 'bg-dark text-white' : 'bg-secondary' }} ms-1">{{ $counts['sk'] ?? '' }}</span>
            </a>
            <a href="{{ route('portal.announcements', ['tab' => 'careers']) }}" 
               class="btn btn-sm {{ request('tab') === 'careers' ? 'btn-success text-white' : 'btn-outline-secondary' }}" 
               style="border-radius:20px;padding:5px 16px;font-weight:700;font-size:13px">
                <i class="ti ti-briefcase me-1"></i>Careers &amp; Opportunities <span class="badge {{ request('tab') === 'careers' ? 'bg-light text-success' : 'bg-secondary' }} ms-1">3</span>
            </a>
        </div>
    </div>

    @if(request('tab') === 'careers')
        {{-- ════════════════════ CAREERS & OPPORTUNITIES TAB ════════════════════ --}}
        {{-- Hero Banner --}}
        <div class="card-custom p-4 p-md-5 mb-4 text-white" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); border-radius: 20px;">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <span class="badge bg-white text-success fw-bold text-uppercase px-3 py-2 mb-3" style="letter-spacing: 1px;">Community Employment &amp; Service</span>
                    <h1 class="fw-extrabold display-6 mb-3" style="font-weight: 800;">Careers &amp; Opportunities</h1>
                    <p class="lead mb-0 text-white-50" style="font-size: 1.1rem; line-height: 1.7;">
                        Explore job openings, volunteer programs, and community livelihood opportunities serving {{ $settings['barangay_name'] ?? 'Barangay San Jose' }}.
                    </p>
                </div>
                <div class="col-lg-4 text-center text-lg-end mt-4 mt-lg-0">
                    <div style="width: 100px; height: 100px; border-radius: 50%; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); display: inline-flex; align-items: center; justify-content: center; font-size: 48px;">
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
                    <h5 class="fw-bold text-dark mb-1">Application Guidelines &amp; Submission</h5>
                    <p class="text-muted small mb-2" style="line-height: 1.6;">
                        Interested applicants must be bona fide residents of {{ $settings['barangay_name'] ?? 'Barangay San Jose' }} of legal age. Submit your application documents personally at the Barangay Administrative Desk or reach out via official contact channels.
                    </p>
                    <div class="d-flex flex-wrap gap-3 small text-muted">
                        <span><i class="ti ti-map-pin me-1 text-danger"></i>Barangay Hall, Ground Floor</span>
                        <span><i class="ti ti-clock me-1 text-primary"></i>Mon–Fri, 8:00 AM – 5:00 PM</span>
                        <span><i class="ti ti-phone me-1 text-success"></i>{{ $settings['contact_number'] ?? '(02) 8888-0000' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- How to Apply Modal --}}
        <div class="modal fade" id="careerApplyModal" tabindex="-1" aria-labelledby="careerApplyModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 12px 35px rgba(0,0,0,0.18);">
                    <div class="modal-header pb-2" style="padding: 24px 28px 12px;">
                        <div>
                            <span class="badge bg-success bg-opacity-10 text-success fw-bold small text-uppercase" style="letter-spacing: 0.5px;">Application Procedure</span>
                            <h5 class="modal-title fw-bold text-dark mt-1" id="careerApplyModalLabel">Apply for Position</h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="padding: 16px 28px 24px; font-size: 14px; line-height: 1.65; color: #334155;">
                        <p class="mb-3">
                            To apply for <strong id="modalJobTitle" class="text-primary">this position</strong>, prepare the following requirements:
                        </p>
                        <ul class="mb-3 ps-3">
                            <li class="mb-1.5">Updated Resume or Bio-data with 2x2 photo</li>
                            <li class="mb-1.5">Valid Government ID (showing residency in the Barangay)</li>
                            <li class="mb-1.5">Barangay Clearance (Issued within the last 6 months)</li>
                            <li class="mb-1.5">Police Clearance or NBI Clearance (for Tanod applicants)</li>
                        </ul>
                        <div class="alert alert-info py-2 px-3 mb-0 small" style="border-radius: 8px;">
                            <i class="ti ti-info-circle me-1"></i> Submit your physical folder to the <strong>Barangay Secretary's Office</strong> during office hours.
                        </div>
                    </div>
                    <div class="modal-footer bg-light" style="padding: 12px 28px; border-radius: 0 0 16px 16px;">
                        <button type="button" class="btn btn-secondary btn-sm px-4 fw-semibold" data-bs-dismiss="modal" style="border-radius: 8px;">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- ════════════════════ ANNOUNCEMENTS & ADVISORIES GRID ════════════════════ --}}
        <div class="row g-4">
            @forelse($announcements as $ann)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="announcement-card-large" data-bs-toggle="modal" data-bs-target="#annListModal-{{ $ann->id }}" style="cursor:pointer;border-radius:14px;overflow:hidden;border:1px solid #e2e8f0;background:#fff;box-shadow:0 2px 8px rgba(0,0,0,0.04);transition:transform .2s,box-shadow .2s;display:flex;flex-direction:column;height:100%">
                    @if($ann->image)
                        <img src="{{ asset('storage/'.$ann->image) }}" class="announcement-img" alt="{{ $ann->title }}" style="width:100%;height:180px;object-fit:cover;">
                    @else
                        <div class="announcement-img d-flex align-items-center justify-content-center" style="width:100%;height:180px;background:linear-gradient(135deg,#e0f2fe,#dbeafe);color:#0284c7">
                            <i class="ti ti-speakerphone" style="font-size:42px;opacity:0.7"></i>
                        </div>
                    @endif
                    <div class="p-3 d-flex flex-column flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                            <span class="badge" style="background:#e0f2fe;color:#0369a1;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px">
                                {{ ucwords(str_replace('_',' ',$ann->announcement_type)) }}
                            </span>
                            @if($ann->isSkAnnouncement())
                                <span class="badge bg-warning text-dark fw-bold" style="font-size:10px;padding:3px 8px;border-radius:6px">
                                    ⚡ Sangguniang Kabataan (SK)
                                </span>
                            @endif
                        </div>
                        <h5 class="fw-bold mb-2 text-dark" style="font-size:16px;line-height:1.35">{{ $ann->title }}</h5>
                        <p class="text-muted small mb-3 flex-grow-1" style="line-height:1.6;font-size:13px">{{ Str::limit($ann->body, 120) }}</p>
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top" style="font-size:12px;color:#94a3b8">
                            <span><i class="ti ti-calendar me-1"></i>{{ $ann->published_at ? $ann->published_at->format('M d, Y') : $ann->created_at->format('M d, Y') }}</span>
                            <span class="text-primary fw-semibold">Read more <i class="ti ti-arrow-right"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detail Modal --}}
            <div class="modal fade" id="annListModal-{{ $ann->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content" style="border-radius:16px;overflow:hidden;border:none;box-shadow:0 12px 35px rgba(0,0,0,0.18)">
                        @if($ann->image)
                            <div style="position:relative;background:#000">
                                <img src="{{ asset('storage/'.$ann->image) }}" alt="{{ $ann->title }}" style="width:100%;max-height:360px;object-fit:cover;">
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                                    style="position:absolute;top:15px;right:15px;background-color:rgba(0,0,0,0.5);border-radius:50%;padding:10px"></button>
                            </div>
                        @endif
                        <div class="modal-header {{ $ann->image ? 'border-0 pb-0' : '' }}" style="padding: 24px 28px 12px;">
                            <div class="w-100">
                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                    <span class="badge" style="background:#e0f2fe;color:#0369a1;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px">
                                        {{ ucwords(str_replace('_',' ',$ann->announcement_type)) }}
                                    </span>
                                    @if($ann->isSkAnnouncement())
                                        <span class="badge bg-warning text-dark fw-bold" style="font-size:11px;padding:3px 8px;border-radius:6px">
                                            ⚡ Sangguniang Kabataan (SK)
                                        </span>
                                    @endif
                                </div>
                                <h4 class="modal-title fw-bold text-dark mt-2 mb-2" style="font-size:20px;line-height:1.35">
                                    {{ $ann->title }}
                                </h4>
                                <div class="text-muted small d-flex align-items-center flex-wrap gap-2 pt-1 border-top" style="border-color:#f1f5f9 !important">
                                    <span><i class="ti ti-calendar me-1 text-primary"></i>Posted on {{ $ann->published_at ? $ann->published_at->format('F d, Y') : $ann->created_at->format('F d, Y') }}</span>
                                    @if($ann->isSkAnnouncement())
                                        <span>•</span>
                                        <span><i class="ti ti-bolt me-1 text-warning"></i>Posted by: <strong>Sangguniang Kabataan (SK)</strong></span>
                                    @endif
                                </div>
                            </div>
                            @if(!$ann->image)
                                <button type="button" class="btn-close align-self-start" data-bs-dismiss="modal" aria-label="Close"></button>
                            @endif
                        </div>
                        <div class="modal-body" style="padding: 18px 28px 28px;font-size:15px;line-height:1.75;color:#334155;">
                            <div style="white-space:pre-line;">{!! nl2br(e($ann->body)) !!}</div>
                        </div>
                        <div class="modal-footer bg-light" style="border-top:1px solid #e2e8f0;padding:12px 28px;">
                            <button type="button" class="btn btn-secondary btn-sm px-4 fw-semibold" data-bs-dismiss="modal" style="border-radius:8px">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center py-5 card-custom text-muted">
                    <i class="ti ti-speakerphone" style="font-size:42px;opacity:0.3;display:block;margin-bottom:10px"></i>
                    <p class="mb-0">No announcements at this time.</p>
                </div>
            </div>
            @endforelse
        </div>

        <div class="mt-4">{{ $announcements->links() }}</div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const applyModal = document.getElementById('careerApplyModal');
    if (applyModal) {
        applyModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const jobTitle = button?.getAttribute('data-job') || 'this position';
            const titleEl = document.getElementById('modalJobTitle');
            if (titleEl) titleEl.textContent = jobTitle;
        });
    }
});
</script>
@endpush
@endsection
