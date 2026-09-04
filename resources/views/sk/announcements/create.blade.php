@extends('layouts.sk')
@section('title', 'Post Youth Announcement')

@section('content')
<div class="mb-3">
    <a href="{{ route('sk.announcements.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back to Announcements
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card-custom p-4">
            <h5 class="fw-bold mb-1" style="color:var(--sk-primary)"><i class="ti ti-speakerphone me-2"></i>Post SK Youth Announcement / Advisory</h5>
            <div class="text-muted small mb-4">Inform the youth and Katipunan ng Kabataan members about upcoming activities, assemblies, and notices.</div>

            <form method="POST" action="{{ route('sk.announcements.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-md-8">
                        <label class="form-label fw-semibold">Announcement Title *</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" placeholder="e.g. Call for Participants: 2026 Youth Leadership Summit" value="{{ old('title') }}" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Type *</label>
                        <select name="announcement_type" class="form-select @error('announcement_type') is-invalid @enderror" required>
                            @foreach(\App\Models\Announcement::TYPES as $t)
                                <option value="{{ $t }}" {{ old('announcement_type', 'community_event') === $t ? 'selected' : '' }}>
                                    {{ ucwords(str_replace('_',' ',$t)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('announcement_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Announcement Content *</label>
                        <textarea name="body" rows="6" class="form-control @error('body') is-invalid @enderror" placeholder="Write the announcement details, requirements, dates, and instructions..." required>{{ old('body') }}</textarea>
                        @error('body') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Banner Image / Poster (Optional)</label>
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" id="imageInput">
                        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        {{-- Image Preview --}}
                        <div id="imagePreview" class="mt-2 d-none">
                            <img id="previewImg" src="" alt="Preview" class="img-fluid rounded border" style="max-height:200px;object-fit:cover">
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Publishing Status *</label>
                        <select name="status" id="statusSelect" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="published" {{ old('status', 'published') === 'published' ? 'selected' : '' }}>Publish Immediately (Live)</option>
                            <option value="scheduled" {{ old('status') === 'scheduled' ? 'selected' : '' }}>Schedule for Later</option>
                            <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Save as Draft</option>
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Scheduled Date/Time --}}
                    <div class="col-12 col-md-6" id="scheduleDateContainer" style="{{ old('status') === 'scheduled' ? '' : 'display:none' }}">
                        <label class="form-label fw-semibold"><i class="ti ti-calendar-time me-1"></i>Scheduled Date & Time *</label>
                        <input type="datetime-local" name="published_at" id="publishedAtInput" class="form-control @error('published_at') is-invalid @enderror" value="{{ old('published_at') }}" min="{{ now()->addMinutes(5)->format('Y-m-d\TH:i') }}">
                        @error('published_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text text-muted"><i class="ti ti-info-circle me-1"></i>The announcement will automatically go live at this date and time.</div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('sk.announcements.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn text-white px-4" style="background:var(--sk-primary)">
                        <i class="ti ti-send me-1"></i> Post Announcement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle scheduled date field
    document.getElementById('statusSelect').addEventListener('change', function() {
        const container = document.getElementById('scheduleDateContainer');
        const input = document.getElementById('publishedAtInput');
        if (this.value === 'scheduled') {
            container.style.display = '';
            input.required = true;
        } else {
            container.style.display = 'none';
            input.required = false;
        }
    });

    // Image preview
    document.getElementById('imageInput').addEventListener('change', function(e) {
        const preview = document.getElementById('imagePreview');
        const img = document.getElementById('previewImg');
        if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                img.src = ev.target.result;
                preview.classList.remove('d-none');
            };
            reader.readAsDataURL(e.target.files[0]);
        } else {
            preview.classList.add('d-none');
        }
    });
</script>
@endpush
