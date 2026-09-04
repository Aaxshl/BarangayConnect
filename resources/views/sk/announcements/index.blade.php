@extends('layouts.sk')
@section('title', 'Youth Announcements & Events')

@section('content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div>
        <h4 class="fw-bold mb-1" style="color:var(--sk-primary)"><i class="ti ti-speakerphone me-2"></i>Youth Announcements &amp; Event Advisories</h4>
        <div class="text-muted small">Broadcast information about Katipunan ng Kabataan assemblies, sports leagues, seminars, and youth programs.</div>
    </div>
    <a href="{{ route('sk.announcements.create') }}" class="btn text-white d-flex align-items-center gap-1" style="background:var(--sk-primary)">
        <i class="ti ti-plus"></i> Post Announcement
    </a>
</div>

{{-- Filters --}}
<div class="card-custom mb-3 p-3">
    <form method="GET" action="{{ route('sk.announcements.index') }}" class="row g-2 align-items-center">
        <div class="col-12 col-md-5">
            <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Announcement Types</option>
                @foreach(\App\Models\Announcement::TYPES as $t)
                    <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$t)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-5">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published / Live</option>
                <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
            </select>
        </div>
        <div class="col-12 col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-sm w-100 text-white" style="background:var(--sk-primary)">Filter</button>
            @if(request()->hasAny(['type', 'status']))
                <a href="{{ route('sk.announcements.index') }}" class="btn btn-outline-secondary btn-sm"><i class="ti ti-x"></i></a>
            @endif
        </div>
    </form>
</div>

{{-- Announcements List --}}
<div class="card-custom p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-custom-sk align-middle mb-0">
            <thead>
                <tr>
                    <th style="width:50px"></th>
                    <th>Announcement</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Published / Scheduled</th>
                    <th>Author</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($announcements as $ann)
                <tr>
                    <td>
                        @if($ann->image)
                            <img src="{{ asset('storage/'.$ann->image) }}" alt="" style="width:42px;height:42px;border-radius:8px;object-fit:cover;border:1px solid #e2e8f0">
                        @else
                            <div style="width:42px;height:42px;border-radius:8px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;color:#94a3b8;font-size:18px">
                                <i class="ti ti-photo-off"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('sk.announcements.show', $ann) }}" class="fw-bold text-dark text-decoration-none">
                            {{ $ann->title }}
                        </a>
                        <div class="text-muted small">{{ Str::limit(strip_tags($ann->body), 75) }}</div>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border" style="font-size:11px">{{ ucwords(str_replace('_',' ',$ann->announcement_type)) }}</span>
                    </td>
                    <td>
                        @if($ann->status === 'published')
                            <span class="badge bg-success" style="font-size:10.5px">Published</span>
                        @elseif($ann->status === 'scheduled')
                            <span class="badge text-white" style="font-size:10.5px;background:#0d9488">
                                <i class="ti ti-clock me-1"></i>Scheduled
                            </span>
                        @elseif($ann->status === 'draft')
                            <span class="badge bg-secondary" style="font-size:10.5px">Draft</span>
                        @else
                            <span class="badge bg-dark" style="font-size:10.5px">Archived</span>
                        @endif
                    </td>
                    <td>
                        @if($ann->status === 'scheduled' && $ann->published_at)
                            <span class="small" style="color:#0d9488;font-weight:600">
                                <i class="ti ti-calendar-time me-1"></i>{{ $ann->published_at->format('M d, Y g:i A') }}
                            </span>
                        @elseif($ann->published_at)
                            <span class="small">{{ $ann->published_at->format('M d, Y g:i A') }}</span>
                        @else
                            <span class="small text-muted">Not yet published</span>
                        @endif
                    </td>
                    <td>
                        <span class="small">{{ optional($ann->createdBy)->name ?? 'SK Official' }}</span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('sk.announcements.show', $ann) }}" class="btn btn-outline-secondary btn-sm py-1 px-2">
                                <i class="ti ti-eye"></i>
                            </a>
                            <a href="{{ route('sk.announcements.edit', $ann) }}" class="btn btn-sm py-1 px-2 text-white" style="background:var(--sk-primary)">
                                <i class="ti ti-edit"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="ti ti-bell-off fs-1 d-block mb-2 text-secondary"></i>
                        No announcements posted. <a href="{{ route('sk.announcements.create') }}" style="color:var(--sk-primary)">Create your first announcement!</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $announcements->links() }}
</div>
@endsection
