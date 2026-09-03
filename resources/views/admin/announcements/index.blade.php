@extends('layouts.admin')
@section('title','Announcements')
@section('page-title','Announcements')
@section('content')

{{-- Filter Tabs & Controls --}}
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 mt-1">
    {{-- Status Filter Pills --}}
    <div class="d-flex gap-1 flex-wrap">
        <a href="{{ route('admin.announcements.index') }}" 
           class="btn btn-sm {{ !request('status') ? 'btn-navy' : 'btn-outline-secondary' }}" style="font-size:12.5px;border-radius:20px;padding:4px 14px">
            All <span class="badge {{ !request('status') ? 'bg-light text-dark' : 'bg-secondary' }} ms-1">{{ $counts['all'] }}</span>
        </a>
        <a href="{{ route('admin.announcements.index', array_merge(request()->query(), ['status' => 'published'])) }}" 
           class="btn btn-sm {{ request('status') === 'published' ? 'btn-success' : 'btn-outline-secondary' }}" style="font-size:12.5px;border-radius:20px;padding:4px 14px">
            <i class="ti ti-broadcast me-1"></i>Published <span class="badge {{ request('status') === 'published' ? 'bg-light text-dark' : 'bg-secondary' }} ms-1">{{ $counts['published'] }}</span>
        </a>
        <a href="{{ route('admin.announcements.index', array_merge(request()->query(), ['status' => 'scheduled'])) }}" 
           class="btn btn-sm {{ request('status') === 'scheduled' ? 'btn-primary' : 'btn-outline-secondary' }}" style="font-size:12.5px;border-radius:20px;padding:4px 14px">
            <i class="ti ti-calendar-time me-1"></i>Scheduled <span class="badge {{ request('status') === 'scheduled' ? 'bg-light text-dark' : 'bg-secondary' }} ms-1">{{ $counts['scheduled'] }}</span>
        </a>
        <a href="{{ route('admin.announcements.index', array_merge(request()->query(), ['status' => 'draft'])) }}" 
           class="btn btn-sm {{ request('status') === 'draft' ? 'btn-warning' : 'btn-outline-secondary' }}" style="font-size:12.5px;border-radius:20px;padding:4px 14px">
            <i class="ti ti-file-pencil me-1"></i>Drafts <span class="badge {{ request('status') === 'draft' ? 'bg-light text-dark' : 'bg-secondary' }} ms-1">{{ $counts['draft'] }}</span>
        </a>
        <a href="{{ route('admin.announcements.index', array_merge(request()->query(), ['status' => 'archived'])) }}" 
           class="btn btn-sm {{ request('status') === 'archived' ? 'btn-secondary' : 'btn-outline-secondary' }}" style="font-size:12.5px;border-radius:20px;padding:4px 14px">
            <i class="ti ti-archive me-1"></i>Archived <span class="badge {{ request('status') === 'archived' ? 'bg-light text-dark' : 'bg-secondary' }} ms-1">{{ $counts['archived'] }}</span>
        </a>
    </div>

    @if(auth()->user()->canDo('announcements.create'))
    <a href="{{ route('admin.announcements.create') }}" class="btn btn-navy btn-sm">
        <i class="ti ti-plus me-1"></i>New Announcement
    </a>
    @endif
</div>

{{-- Search & Type Filter --}}
<div class="card-custom py-2 px-3 mb-4">
    <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        <div class="input-group input-group-sm" style="max-width: 260px;">
            <span class="input-group-text bg-light"><i class="ti ti-search text-muted"></i></span>
            <input type="text" name="search" class="form-control" placeholder="Search announcements..." value="{{ request('search') }}" onchange="this.form.submit()">
        </div>
        <select name="type" class="form-select form-select-sm" style="width: 200px;" onchange="this.form.submit()">
            <option value="">All Categories</option>
            @foreach(\App\Models\Announcement::TYPES as $t)
                <option value="{{ $t }}" {{ request('type') == $t ? 'selected' : '' }}>
                    {{ ucwords(str_replace('_',' ',$t)) }}
                </option>
            @endforeach
        </select>
        @if(request('search') || request('type'))
            <a href="{{ route('admin.announcements.index', request()->only('status')) }}" class="btn btn-sm btn-link text-muted p-0 text-decoration-none" style="font-size:12px">Clear Filters</a>
        @endif
    </form>
</div>

{{-- Announcement Grid (Portal-Style Cards) --}}
<div class="row g-4">
    @forelse($announcements as $ann)
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card-custom h-100 p-0 d-flex flex-column" style="overflow:hidden;border:1px solid #e2e8f0;transition:transform .2s,box-shadow .2s;">
            {{-- Image Banner with Status Overlay --}}
            <div style="position:relative;height:180px;background:#f1f5f9;cursor:pointer;" data-bs-toggle="modal" data-bs-target="#annDetailModal-{{ $ann->id }}">
                @if($ann->image)
                    <img src="{{ asset('storage/'.$ann->image) }}" alt="{{ $ann->title }}" style="width:100%;height:100%;object-fit:cover;">
                @else
                    <div class="d-flex align-items-center justify-content-center h-100" style="background:linear-gradient(135deg,#e0f2fe,#dbeafe);color:#0284c7">
                        <i class="ti ti-speakerphone" style="font-size:42px;opacity:0.6"></i>
                    </div>
                @endif
                
                {{-- Status Badge (Top Right) --}}
                <div style="position:absolute;top:10px;right:10px;">
                    <span class="badge-status badge-{{ $ann->status }}" style="box-shadow:0 2px 6px rgba(0,0,0,0.15)">
                        @if($ann->status === 'published')
                            <i class="ti ti-broadcast me-1"></i>Published
                        @elseif($ann->status === 'scheduled')
                            <i class="ti ti-calendar-time me-1"></i>Scheduled
                        @elseif($ann->status === 'draft')
                            <i class="ti ti-file-pencil me-1"></i>Draft
                        @else
                            <i class="ti ti-archive me-1"></i>Archived
                        @endif
                    </span>
                </div>

                {{-- Type Badge (Top Left) --}}
                <div style="position:absolute;top:10px;left:10px;">
                    <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;padding:3px 8px;border-radius:6px;background:rgba(255,255,255,0.9);color:#1e293b;box-shadow:0 2px 6px rgba(0,0,0,0.15)">
                        {{ ucwords(str_replace('_',' ',$ann->announcement_type)) }}
                    </span>
                </div>
            </div>

            {{-- Card Body --}}
            <div class="p-3 d-flex flex-column flex-grow-1">
                <h5 class="fw-bold mb-2" style="font-size:16px;line-height:1.4;cursor:pointer;color:#1e293b" data-bs-toggle="modal" data-bs-target="#annDetailModal-{{ $ann->id }}">
                    {{ $ann->title }}
                </h5>
                
                <p class="text-muted small mb-3 flex-grow-1" style="line-height:1.6;font-size:13px">
                    {{ Str::limit($ann->body, 110) }}
                </p>

                {{-- Meta Info --}}
                <div class="pt-2 border-top mb-3" style="font-size:12px;color:#64748b">
                    @if($ann->status === 'scheduled' && $ann->published_at)
                        <div class="text-primary fw-semibold mb-1">
                            <i class="ti ti-clock me-1"></i>Schedules: {{ $ann->published_at->format('M d, Y · g:i A') }}
                        </div>
                    @elseif($ann->published_at)
                        <div class="mb-1">
                            <i class="ti ti-calendar me-1"></i>Published: {{ $ann->published_at->format('M d, Y') }}
                        </div>
                    @else
                        <div class="mb-1">
                            <i class="ti ti-calendar me-1"></i>Created: {{ $ann->created_at->format('M d, Y') }}
                        </div>
                    @endif
                    <div>
                        <i class="ti ti-user me-1"></i>By {{ optional($ann->createdBy)->name ?: 'Admin' }}
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex justify-content-between align-items-center pt-2 border-top gap-1">
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" title="View Details" data-bs-toggle="modal" data-bs-target="#annDetailModal-{{ $ann->id }}">
                            <i class="ti ti-eye"></i>
                        </button>
                        @if(auth()->user()->canDo('announcements.create'))
                        <a href="{{ route('admin.announcements.edit', $ann) }}" class="btn btn-sm btn-outline-navy py-1 px-2" title="Edit Announcement">
                            <i class="ti ti-pencil"></i>
                        </a>
                        @endif
                    </div>

                    <div class="d-flex gap-1 align-items-center">
                        @if(auth()->user()->canDo('announcements.publish'))
                            @if($ann->status !== 'published')
                                <form method="POST" action="{{ route('admin.announcements.publish', $ann) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success py-1 px-2" title="Publish Live Now" style="font-size:11.5px">
                                        <i class="ti ti-broadcast me-1"></i>Publish
                                    </button>
                                </form>
                            @endif

                            @if($ann->status !== 'scheduled')
                                <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2" title="Schedule Date & Time" data-bs-toggle="modal" data-bs-target="#scheduleModal-{{ $ann->id }}" style="font-size:11.5px">
                                    <i class="ti ti-calendar-time"></i>
                                </button>
                            @endif

                            @if($ann->status === 'published')
                                <form method="POST" action="{{ route('admin.announcements.archive', $ann) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary py-1 px-2" title="Archive" onclick="return confirm('Archive this announcement?')" style="font-size:11.5px">
                                        <i class="ti ti-archive"></i>
                                    </button>
                                </form>
                            @endif

                            @if(in_array($ann->status, ['scheduled', 'archived']))
                                <form method="POST" action="{{ route('admin.announcements.draft', $ann) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-warning py-1 px-2" title="Revert to Draft" style="font-size:11.5px">
                                        <i class="ti ti-file-pencil"></i>
                                    </button>
                                </form>
                            @endif
                        @endif

                        @if(auth()->user()->canDo('announcements.delete'))
                        <form method="POST" action="{{ route('admin.announcements.destroy', $ann) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" title="Delete" onclick="return confirm('Delete this announcement permanently?')" style="font-size:11.5px">
                                <i class="ti ti-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Modal for this Announcement --}}
    <div class="modal fade" id="annDetailModal-{{ $ann->id }}" tabindex="-1" aria-labelledby="annLabel-{{ $ann->id }}" aria-hidden="true">
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
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge-status badge-{{ $ann->status }}">
                                {{ ucfirst($ann->status) }}
                            </span>
                            <span style="font-size:12px;font-weight:700;text-transform:uppercase;color:#185fa5">
                                {{ ucwords(str_replace('_',' ',$ann->announcement_type)) }}
                            </span>
                        </div>
                        <h4 class="modal-title fw-bold text-dark mt-1 mb-2" id="annLabel-{{ $ann->id }}" style="font-size:22px;line-height:1.35">
                            {{ $ann->title }}
                        </h4>
                        <div class="text-muted small d-flex align-items-center flex-wrap gap-3 pt-1 border-top" style="border-color:#f1f5f9 !important">
                            <span><i class="ti ti-user me-1 text-primary"></i>Created by {{ optional($ann->createdBy)->name ?: 'System' }}</span>
                            @if($ann->published_at)
                                <span><i class="ti ti-calendar me-1 text-primary"></i>{{ $ann->status === 'scheduled' ? 'Scheduled for' : 'Published on' }} {{ $ann->published_at->format('F d, Y · h:i A') }}</span>
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
                <div class="modal-footer bg-light d-flex justify-content-between" style="border-top:1px solid #e2e8f0;padding:12px 28px;">
                    <a href="{{ route('admin.announcements.edit', $ann) }}" class="btn btn-outline-navy btn-sm px-3">
                        <i class="ti ti-pencil me-1"></i>Edit
                    </a>
                    <button type="button" class="btn btn-secondary btn-sm px-4 fw-semibold" data-bs-dismiss="modal" style="border-radius:8px">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Schedule Modal --}}
    <div class="modal fade" id="scheduleModal-{{ $ann->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content" style="border-radius:14px;">
                <form method="POST" action="{{ route('admin.announcements.schedule', $ann) }}">
                    @csrf
                    <div class="modal-header">
                        <h6 class="modal-title fw-bold"><i class="ti ti-calendar-time me-1 text-primary"></i>Schedule Post</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label small fw-semibold">Publish Date &amp; Time *</label>
                        <input type="datetime-local" name="scheduled_at" class="form-control" 
                            value="{{ $ann->published_at ? $ann->published_at->format('Y-m-d\TH:i') : now()->addDay()->format('Y-m-d\T08:00') }}" required>
                        <div class="text-muted small mt-2">The announcement will automatically become visible on the resident portal at this specified date &amp; time.</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-check me-1"></i>Save Schedule</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center py-5 card-custom text-muted">
            <i class="ti ti-speakerphone" style="font-size:48px;opacity:0.3;display:block;margin-bottom:12px"></i>
            <h5 class="fw-bold text-dark">No announcements found</h5>
            <p class="small text-muted mb-3">Create announcements, advisories, or notices to keep your barangay residents informed.</p>
            <a href="{{ route('admin.announcements.create') }}" class="btn btn-navy btn-sm">
                <i class="ti ti-plus me-1"></i>Create First Announcement
            </a>
        </div>
    </div>
    @endforelse
</div>

<div class="mt-4">{{ $announcements->links() }}</div>
@endsection
