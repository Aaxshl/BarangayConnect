@extends('layouts.portal')
@section('title','Dashboard')
@section('content')
<div class="portal-hero">
    <div class="container-fluid px-3 px-md-4">
        <h1 class="hero-greeting">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }}, {{ $resident->first_name }}</h1>
        <p class="hero-sub">{{ optional($resident->household)->address ?? $resident->address }} · Resident since {{ $resident->created_at->format('Y') }}</p>
        <div class="hero-actions">
            <a href="{{ route('portal.request') }}" class="hero-action-btn">
                <i class="ti ti-file-plus"></i>
                <span class="hero-action-title">Request doc</span>
                <span class="hero-action-sub">Clearance, cert., more</span>
            </a>
            <a href="{{ route('portal.report') }}" class="hero-action-btn">
                <i class="ti ti-message-report"></i>
                <span class="hero-action-title">Report issue</span>
                <span class="hero-action-sub">Streetlight, road...</span>
            </a>
            <a href="{{ route('portal.track') }}" class="hero-action-btn">
                <i class="ti ti-list-search"></i>
                <span class="hero-action-title">Track request</span>
                <span class="hero-action-sub">Check your status</span>
            </a>
            <a href="{{ route('portal.announcements') }}" class="hero-action-btn">
                <i class="ti ti-speakerphone"></i>
                <span class="hero-action-title">Notices</span>
                <span class="hero-action-sub">Latest updates</span>
            </a>
        </div>
    </div>
</div>

<div class="container-fluid px-3 px-md-4 mt-4">
    <div class="row g-3">
        <div class="col-12 col-md-6">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="section-title">Your recent requests</span>
                <a href="{{ route('portal.track') }}" style="font-size:13px;color:#1a3a6b">View all</a>
            </div>
            <div class="portal-card">
                @forelse($myRequests as $req)
                <div class="request-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="request-id">{{ $req->tracking_number }}</div>
                            <div class="request-title">{{ ucwords(str_replace('_',' ',$req->request_type)) }}</div>
                            <div class="request-meta">{{ $req->created_at->format('M d, Y') }}</div>
                        </div>
                        <span class="badge-status badge-{{ $req->status }}">{{ ucwords(str_replace('_',' ',$req->status)) }}</span>
                    </div>
                </div>
                @empty
                @forelse($myDocuments as $doc)
                <div class="request-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="request-id">{{ $doc->document_number }}</div>
                            <div class="request-title">{{ \App\Models\Document::TYPES[$doc->document_type] ?? $doc->document_type }}</div>
                            <div class="request-meta">{{ $doc->issue_date->format('M d, Y') }}</div>
                        </div>
                        <span class="badge-status badge-{{ $doc->status }}">{{ ucwords(str_replace('_',' ',$doc->status)) }}</span>
                    </div>
                </div>
                @empty
                <p class="text-muted small text-center py-3">No requests yet.</p>
                @endforelse
                @endforelse
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="section-title">Announcements</span>
                <a href="{{ route('portal.announcements') }}" style="font-size:13px;color:#1a3a6b">See all</a>
            </div>
            @forelse($announcements as $ann)
            <div class="announce-card">
                <div class="announce-type">{{ ucwords(str_replace('_',' ',$ann->announcement_type)) }}</div>
                <div class="announce-title">{{ $ann->title }}</div>
                <div class="announce-date">Posted {{ $ann->published_at->format('M d, Y') }}</div>
            </div>
            @empty
            <p class="text-muted small text-center py-3">No announcements.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
