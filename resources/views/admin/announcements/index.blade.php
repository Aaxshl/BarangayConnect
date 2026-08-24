@extends('layouts.admin')
@section('title','Announcements')
@section('page-title','Announcements')
@section('content')
<div class="d-flex justify-content-between align-items-center mt-2 mb-3">
    <span class="text-muted small">{{ $announcements->total() }} total</span>
    <a href="{{ route('admin.announcements.create') }}" class="btn btn-navy btn-sm"><i class="ti ti-plus me-1"></i>New Announcement</a>
</div>
@forelse($announcements as $ann)
<div class="card-custom mb-3">
    <div class="d-flex justify-content-between flex-wrap gap-2">
        <div>
            <div style="font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:#185fa5;margin-bottom:4px">{{ ucwords(str_replace('_',' ',$ann->announcement_type)) }}</div>
            <h6 class="mb-1">{{ $ann->title }}</h6>
            <p class="mb-2 text-muted" style="font-size:13.5px">{{ Str::limit($ann->body,150) }}</p>
            <small class="text-muted">Posted {{ $ann->created_at->format('M d, Y') }} by {{ optional($ann->createdBy)->name }}</small>
        </div>
        <div class="d-flex flex-column align-items-end gap-2">
            <span class="badge-status badge-{{ $ann->status }}">{{ ucfirst($ann->status) }}</span>
            <div class="d-flex gap-1 mt-auto">
                <a href="{{ route('admin.announcements.edit',$ann) }}" class="btn btn-sm btn-outline-navy py-0 px-2"><i class="ti ti-pencil"></i></a>
                @if($ann->status !== 'published')
                    <form method="POST" action="{{ route('admin.announcements.publish',$ann) }}">@csrf<button class="btn btn-sm btn-navy py-0 px-2">Publish</button></form>
                @else
                    <form method="POST" action="{{ route('admin.announcements.archive',$ann) }}">@csrf<button class="btn btn-sm btn-outline-secondary py-0 px-2">Archive</button></form>
                @endif
            </div>
        </div>
    </div>
</div>
@empty
<div class="text-center py-5 text-muted"><i class="ti ti-speakerphone" style="font-size:40px;opacity:0.3;display:block;margin-bottom:10px"></i>No announcements yet.</div>
@endforelse
<div class="mt-3">{{ $announcements->links() }}</div>
@endsection
