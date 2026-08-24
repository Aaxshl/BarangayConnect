@extends('layouts.admin')
@section('title','New Announcement')
@section('page-title','New Announcement')
@section('content')
<div class="mb-3">
    <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back to Announcements
    </a>
</div>
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card-custom">
            <form method="POST" action="{{ route('admin.announcements.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3"><label class="form-label">Title *</label><input type="text" name="title" class="form-control" value="{{ old('title') }}" required></div>
                <div class="mb-3"><label class="form-label">Type *</label><select name="announcement_type" class="form-select" required><option value="">Select type...</option>@foreach(\App\Models\Announcement::TYPES as $t)<option value="{{ $t }}">{{ ucwords(str_replace('_',' ',$t)) }}</option>@endforeach</select></div>
                <div class="mb-3"><label class="form-label">Content *</label><textarea name="body" class="form-control" rows="8" required>{{ old('body') }}</textarea></div>
                <div class="mb-3">
                    <label class="form-label">Banner Image <span class="text-muted">(optional)</span></label>
                    <input type="file" name="image" id="ann-image" accept="image/*" class="form-control" onchange="previewBanner(this)">
                    <img id="banner-preview" src="" class="d-none mt-2 rounded w-100" style="max-height:200px;object-fit:cover">
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-navy">Save as draft</button>
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
