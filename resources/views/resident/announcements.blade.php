@extends('layouts.portal')
@section('title','Announcements')
@section('content')
<div class="container-fluid px-3 px-md-4 mt-4">
    <h2 class="section-title mb-4">Announcements</h2>
    @forelse($announcements as $ann)
    <div class="announce-card">
        <div class="announce-type">{{ ucwords(str_replace('_',' ',$ann->announcement_type)) }}</div>
        <div class="announce-title">{{ $ann->title }}</div>
        <p style="font-size:13.5px;color:#555;margin:6px 0 8px;line-height:1.6">{{ $ann->body }}</p>
        <div class="announce-date">Posted {{ $ann->published_at->format('M d, Y') }}</div>
    </div>
    @empty
    <p class="text-muted">No announcements at this time.</p>
    @endforelse
    <div class="mt-3">{{ $announcements->links() }}</div>
</div>
@endsection
