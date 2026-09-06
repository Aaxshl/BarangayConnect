@extends('layouts.portal')
@section('title','Dashboard')
@section('content')
<div class="portal-hero">
    <div class="container-fluid px-3 px-md-4">
        <h1 class="hero-greeting">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }}, {{ $resident->first_name }}</h1>
        <p class="hero-sub mb-0">{{ optional($resident->household)->address ?? $resident->address }} · Resident since {{ $resident->created_at->format('Y') }}</p>
    </div>
</div>

<div class="container-fluid px-3 px-md-4 mt-4">
    {{-- Community Portal Quick Info Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <a href="{{ route('portal.about') }}" class="text-decoration-none">
                <div class="p-3 bg-white border rounded-3 d-flex align-items-center justify-content-between shadow-sm" style="transition:transform 0.15s,box-shadow 0.15s;cursor:pointer">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:46px;height:46px;border-radius:12px;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;font-size:22px">
                            <i class="ti ti-info-circle"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark" style="font-size:15px">Who We Are</div>
                            <div class="text-muted small">Barangay Council, leadership &amp; mission</div>
                        </div>
                    </div>
                    <div class="text-primary fw-bold" style="font-size:13px">
                        View Details &rarr;
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-md-6">
            <a href="{{ route('portal.careers') }}" class="text-decoration-none">
                <div class="p-3 bg-white border rounded-3 d-flex align-items-center justify-content-between shadow-sm" style="transition:transform 0.15s,box-shadow 0.15s;cursor:pointer">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:46px;height:46px;border-radius:12px;background:#f0fdf4;color:#16a34a;display:flex;align-items:center;justify-content:center;font-size:22px">
                            <i class="ti ti-briefcase"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark" style="font-size:15px">Careers &amp; Opportunities</div>
                            <div class="text-muted small">Job openings &amp; community volunteer programs</div>
                        </div>
                    </div>
                    <div class="text-success fw-bold" style="font-size:13px">
                        View Openings &rarr;
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Recent Requests Column --}}
        <div class="col-12 col-md-6">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="section-title">Your recent requests</span>
                <a href="{{ route('portal.track') }}" style="font-size:13px;color:#1a3a6b">View all →</a>
            </div>
            <div class="portal-card">
                @forelse($myRequests as $req)
                <a href="{{ route('portal.track.detail', $req->tracking_number) }}" class="text-decoration-none d-block mb-2 text-dark">
                    <div class="request-item p-3 rounded border hover-shadow" style="background:#fff;transition:transform 0.15s,box-shadow 0.15s">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <div>
                                <div class="request-id fw-bold text-primary" style="font-family:monospace">{{ $req->tracking_number }}</div>
                                <div class="request-title fw-bold mt-1 mb-0">{{ ucwords(str_replace('_',' ',$req->request_type)) }}</div>
                                <div class="request-meta text-muted small mt-1">
                                    <i class="ti ti-calendar me-1"></i>{{ $req->created_at->format('M d, Y') }}
                                    @if($req->assignedTo)
                                        <span class="mx-1">•</span>
                                        <i class="ti ti-user-check text-primary me-1"></i>{{ $req->assignedTo->name }}
                                    @endif
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge-status badge-{{ $req->status }}">{{ ucwords(str_replace('_',' ',$req->status)) }}</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 small">
                                <i class="ti ti-messages me-1"></i>Case Chat ({{ $req->comments_count ?? 0 }})
                            </span>
                            <span class="text-primary small fw-semibold">View Case Chat &rarr;</span>
                        </div>
                    </div>
                </a>
                @empty
                @forelse($myDocuments as $doc)
                <a href="{{ route('portal.track.detail', $doc->document_number) }}" class="text-decoration-none d-block mb-2 text-dark">
                    <div class="request-item p-3 rounded border hover-shadow" style="background:#fff;transition:transform 0.15s,box-shadow 0.15s">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="request-id fw-bold text-primary" style="font-family:monospace">{{ $doc->document_number }}</div>
                                <div class="request-title fw-bold mt-1">{{ \App\Models\Document::TYPES[$doc->document_type] ?? $doc->document_type }}</div>
                                <div class="request-meta text-muted small mt-1"><i class="ti ti-calendar me-1"></i>{{ $doc->issue_date->format('M d, Y') }}</div>
                            </div>
                            <span class="badge-status badge-{{ $doc->status }}">{{ ucwords(str_replace('_',' ',$doc->status)) }}</span>
                        </div>
                        <div class="d-flex justify-content-end align-items-center mt-2 pt-2 border-top">
                            <span class="text-primary small fw-semibold">View Status &rarr;</span>
                        </div>
                    </div>
                </a>
                @empty
                <p class="text-muted small text-center py-4 mb-0">No requests submitted yet.</p>
                @endforelse
                @endforelse
            </div>
        </div>

        {{-- Announcements Column (Clickable with Modals) --}}
        <div class="col-12 col-md-6">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="section-title">Latest Announcements</span>
                <a href="{{ route('portal.announcements') }}" style="font-size:13px;color:#1a3a6b">See all →</a>
            </div>
            @forelse($announcements as $ann)
            <div class="announce-card mb-3" style="cursor:pointer;transition:transform .2s,box-shadow .2s;border-radius:12px;overflow:hidden;border:1px solid #e2e8f0;background:#fff;padding:14px;" data-bs-toggle="modal" data-bs-target="#dashAnnModal-{{ $ann->id }}">
                <div class="d-flex gap-3 align-items-start">
                    @if($ann->image)
                        <img src="{{ asset('storage/'.$ann->image) }}" alt="{{ $ann->title }}" style="width:70px;height:70px;border-radius:8px;object-fit:cover;flex-shrink:0;">
                    @else
                        <div style="width:70px;height:70px;border-radius:8px;background:linear-gradient(135deg,#e0f2fe,#dbeafe);color:#0284c7;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:24px">
                            <i class="ti ti-speakerphone"></i>
                        </div>
                    @endif
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-1 mb-1 flex-wrap">
                            <span class="announce-type" style="font-size:10px;font-weight:700;color:#185fa5;text-transform:uppercase">
                                {{ ucwords(str_replace('_',' ',$ann->announcement_type)) }}
                            </span>
                            @if($ann->isSkAnnouncement())
                                <span class="badge bg-warning text-dark fw-bold" style="font-size:9.5px;padding:2px 6px;border-radius:4px">
                                    ⚡ SK
                                </span>
                            @endif
                        </div>
                        <div class="announce-title fw-bold" style="font-size:14px;color:#1e293b;line-height:1.3;margin-bottom:4px">
                            {{ $ann->title }}
                        </div>
                        <div class="announce-date small text-muted" style="font-size:11.5px">
                            <i class="ti ti-calendar me-1"></i>{{ $ann->published_at ? $ann->published_at->format('M d, Y') : $ann->created_at->format('M d, Y') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Announcement Modal --}}
            <div class="modal fade" id="dashAnnModal-{{ $ann->id }}" tabindex="-1" aria-labelledby="dashAnnLabel-{{ $ann->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content" style="border-radius:16px;overflow:hidden;border:none;box-shadow:0 12px 35px rgba(0,0,0,0.18)">
                        @if($ann->image)
                            <div style="position:relative;background:#000">
                                <img src="{{ asset('storage/'.$ann->image) }}" alt="{{ $ann->title }}" style="width:100%;max-height:340px;object-fit:cover;">
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                                    style="position:absolute;top:15px;right:15px;background-color:rgba(0,0,0,0.5);border-radius:50%;padding:10px"></button>
                            </div>
                        @endif
                        <div class="modal-header {{ $ann->image ? 'border-0 pb-0' : '' }}" style="padding: 24px 28px 12px;">
                            <div class="w-100">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="badge" style="background:#e0f2fe;color:#0369a1;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px">
                                        {{ ucwords(str_replace('_',' ',$ann->announcement_type)) }}
                                    </span>
                                    @if($ann->isSkAnnouncement())
                                        <span class="badge bg-warning text-dark fw-bold" style="font-size:10.5px;padding:3px 8px;border-radius:6px">
                                            ⚡ Sangguniang Kabataan (SK)
                                        </span>
                                    @endif
                                </div>
                                <h4 class="modal-title fw-bold text-dark mt-2 mb-2" id="dashAnnLabel-{{ $ann->id }}" style="font-size:20px;line-height:1.35">
                                    {{ $ann->title }}
                                </h4>
                                <div class="text-muted small d-flex align-items-center flex-wrap gap-2 pt-1 border-top" style="border-color:#f1f5f9 !important">
                                    <span><i class="ti ti-calendar me-1 text-primary"></i>Posted {{ $ann->published_at ? $ann->published_at->format('F d, Y') : $ann->created_at->format('F d, Y') }}</span>
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
            <p class="text-muted small text-center py-4">No announcements posted at this time.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
