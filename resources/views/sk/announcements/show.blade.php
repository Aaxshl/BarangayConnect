@extends('layouts.sk')
@section('title', $announcement->title)

@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <a href="{{ route('sk.announcements.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back to Announcements
    </a>
    <div class="d-flex gap-2">
        <a href="{{ route('sk.announcements.edit', $announcement) }}" class="btn btn-sm text-white" style="background:var(--sk-primary)">
            <i class="ti ti-edit me-1"></i> Edit
        </a>
        <form method="POST" action="{{ route('sk.announcements.destroy', $announcement) }}" onsubmit="return confirm('Are you sure you want to delete this announcement?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm">
                <i class="ti ti-trash me-1"></i> Delete
            </button>
        </form>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-9">
        <div class="card-custom p-4">
            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                <span class="badge bg-light text-dark border">{{ ucwords(str_replace('_',' ',$announcement->announcement_type)) }}</span>
                @if($announcement->status === 'published')
                    <span class="badge bg-success">Live Published</span>
                @elseif($announcement->status === 'scheduled')
                    <span class="badge text-white" style="background:#0d9488">
                        <i class="ti ti-clock me-1"></i>Scheduled
                    </span>
                @elseif($announcement->status === 'draft')
                    <span class="badge bg-secondary">Draft</span>
                @else
                    <span class="badge bg-dark">Archived</span>
                @endif
            </div>

            <h3 class="fw-bold mb-2">{{ $announcement->title }}</h3>
            <div class="text-muted small mb-4 d-flex flex-wrap gap-2 align-items-center">
                <span><i class="ti ti-user me-1"></i>Posted by {{ optional($announcement->createdBy)->name ?? 'SK Official' }}</span>
                <span>&bull;</span>
                <span><i class="ti ti-calendar me-1"></i>{{ $announcement->created_at->format('F d, Y g:i A') }}</span>
                @if($announcement->status === 'scheduled' && $announcement->published_at)
                    <span>&bull;</span>
                    <span style="color:#0d9488;font-weight:600">
                        <i class="ti ti-calendar-time me-1"></i>Goes live: {{ $announcement->published_at->format('F d, Y g:i A') }}
                    </span>
                @endif
            </div>

            @if($announcement->image)
            <div class="mb-4 text-center">
                <img src="{{ asset('storage/' . $announcement->image) }}" class="img-fluid rounded border" style="max-height:400px;object-fit:cover" alt="{{ $announcement->title }}">
            </div>
            @endif

            <div class="p-3 bg-white border rounded" style="line-height:1.8;font-size:15px;white-space:pre-line">
                {{ $announcement->body }}
            </div>
        </div>
    </div>
</div>
@endsection
