@extends('layouts.admin')
@section('title','Edit Announcement')
@section('page-title','Edit Announcement')
@section('content')
<div class="mb-3">
    <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back to Announcements
    </a>
</div>
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card-custom">
            <form method="POST" action="{{ route('admin.announcements.update',$announcement) }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="mb-3"><label class="form-label">Title *</label><input type="text" name="title" class="form-control" value="{{ old('title',$announcement->title) }}" required></div>
                <div class="mb-3"><label class="form-label">Type *</label><select name="announcement_type" class="form-select" required>@foreach(\App\Models\Announcement::TYPES as $t)<option value="{{ $t }}" {{ $announcement->announcement_type==$t?'selected':'' }}>{{ ucwords(str_replace('_',' ',$t)) }}</option>@endforeach</select></div>
                <div class="mb-3"><label class="form-label">Content *</label><textarea name="body" class="form-control" rows="8" required>{{ old('body',$announcement->body) }}</textarea></div>
                <div class="mb-3">
                    <label class="form-label">Banner Image</label>
                    @if($announcement->image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/'.$announcement->image) }}" class="rounded w-100 mb-1" style="max-height:160px;object-fit:cover">
                            <div class="text-muted small">Current banner. Upload a new one to replace it.</div>
                        </div>
                    @endif
                    <input type="file" name="image" id="ann-image" accept="image/*" class="form-control" onchange="previewBanner(this)">
                    <img id="banner-preview" src="" class="d-none mt-2 rounded w-100" style="max-height:200px;object-fit:cover">
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-navy">Update</button>
                    <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
function previewBanner(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.getElementById('banner-preview');
            img.src = e.target.result;
            img.classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection
