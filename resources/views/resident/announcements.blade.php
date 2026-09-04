@extends('layouts.portal')
@section('title','Announcements')
@section('content')
<div class="container-fluid px-3 px-md-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <div class="text-uppercase fw-bold small text-primary" style="letter-spacing:0.8px">Barangay Updates</div>
            <h2 class="section-title mb-0" style="font-size:22px;font-weight:800;color:#1e293b">Announcements &amp; Advisories</h2>
        </div>
    </div>

    {{-- Source Filter Pills --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('portal.announcements', request()->only('type')) }}" 
               class="btn btn-sm {{ !request('source') ? 'btn-primary text-white' : 'btn-outline-secondary' }}" 
               style="border-radius:20px;padding:5px 16px;font-weight:600;font-size:13px">
                All Announcements <span class="badge {{ !request('source') ? 'bg-light text-dark' : 'bg-secondary' }} ms-1">{{ $counts['all'] ?? $announcements->total() }}</span>
            </a>
            <a href="{{ route('portal.announcements', array_merge(request()->only('type'), ['source' => 'barangay'])) }}" 
               class="btn btn-sm {{ request('source') === 'barangay' ? 'btn-primary text-white' : 'btn-outline-secondary' }}" 
               style="border-radius:20px;padding:5px 16px;font-weight:600;font-size:13px">
                <i class="ti ti-building-community me-1"></i>Barangay Office <span class="badge {{ request('source') === 'barangay' ? 'bg-light text-dark' : 'bg-secondary' }} ms-1">{{ $counts['barangay'] ?? '' }}</span>
            </a>
            <a href="{{ route('portal.announcements', array_merge(request()->only('type'), ['source' => 'sk'])) }}" 
               class="btn btn-sm {{ request('source') === 'sk' ? 'btn-warning text-dark' : 'btn-outline-secondary' }}" 
               style="border-radius:20px;padding:5px 16px;font-weight:700;font-size:13px">
                ⚡ Sangguniang Kabataan <span class="badge {{ request('source') === 'sk' ? 'bg-dark text-white' : 'bg-secondary' }} ms-1">{{ $counts['sk'] ?? '' }}</span>
            </a>
        </div>
    </div>

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
</div>
@endsection
