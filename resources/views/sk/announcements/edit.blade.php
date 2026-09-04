@extends('layouts.sk')
@section('title', 'Edit Announcement')

@section('content')
<div class="mb-3">
    <a href="{{ route('sk.announcements.show', $announcement) }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back to Announcement
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card-custom p-4">
            <h5 class="fw-bold mb-1" style="color:var(--sk-primary)"><i class="ti ti-edit me-2"></i>Edit Announcement</h5>
            <div class="text-muted small mb-4">Modify the title, text, or publication status of this youth announcement.</div>

            <form method="POST" action="{{ route('sk.announcements.update', $announcement) }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-12 col-md-8">
                        <label class="form-label fw-semibold">Announcement Title *</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $announcement->title) }}" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Type *</label>
                        <select name="announcement_type" class="form-select @error('announcement_type') is-invalid @enderror" required>
                            @foreach(\App\Models\Announcement::TYPES as $t)
                                <option value="{{ $t }}" {{ old('announcement_type', $announcement->announcement_type) === $t ? 'selected' : '' }}>
                                    {{ ucwords(str_replace('_',' ',$t)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('announcement_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Announcement Content *</label>
                        <textarea name="body" rows="6" class="form-control @error('body') is-invalid @enderror" required>{{ old('body', $announcement->body) }}</textarea>
                        @error('body') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Current Image --}}
                    @if($announcement->image)
                    <div class="col-12">
                        <label class="form-label fw-semibold">Current Image</label>
                        <div class="mb-2">
                            <img src="{{ asset('storage/'.$announcement->image) }}" alt="{{ $announcement->title }}" class="img-fluid rounded border" style="max-height:200px;object-fit:cover">
                        </div>
                    </div>
                    @endif

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">{{ $announcement->image ? 'Replace Image / Poster' : 'Banner Image / Poster (Optional)' }}</label>
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" id="imageInput">
                        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div id="imagePreview" class="mt-2 d-none">
                            <img id="previewImg" src="" alt="Preview" class="img-fluid rounded border" style="max-height:200px;object-fit:cover">
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Publishing Status *</label>
                        <select name="status" id="statusSelect" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="published" {{ old('status', $announcement->status) === 'published' ? 'selected' : '' }}>Published (Live)</option>
                            <option value="scheduled" {{ old('status', $announcement->status) === 'scheduled' ? 'selected' : '' }}>Schedule for Later</option>
                            <option value="draft" {{ old('status', $announcement->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="archived" {{ old('status', $announcement->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Scheduled Date/Time --}}
                    @php
                        $showSchedule = old('status', $announcement->status) === 'scheduled';
                        $scheduleVal = old('published_at', $announcement->published_at ? $announcement->published_at->format('Y-m-d\TH:i') : '');
                    @endphp
                    <div class="col-12 col-md-6" id="scheduleDateContainer" style="{{ $showSchedule ? '' : 'display:none' }}">
                        <label class="form-label fw-semibold"><i class="ti ti-calendar-time me-1"></i>Scheduled Date & Time *</label>
                        <input type="datetime-local" name="published_at" id="publishedAtInput" class="form-control @error('published_at') is-invalid @enderror" value="{{ $scheduleVal }}">
                        @error('published_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text text-muted"><i class="ti ti-info-circle me-1"></i>The announcement will automatically go live at this date and time.</div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('sk.announcements.show', $announcement) }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn text-white px-4" style="background:var(--sk-primary)">
                        <i class="ti ti-device-floppy me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
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
