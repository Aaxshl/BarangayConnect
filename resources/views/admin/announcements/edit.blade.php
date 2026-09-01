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
    <div class="col-12 col-lg-9">
        <div class="card-custom">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Edit Announcement</h5>
                <span class="badge-status badge-{{ $announcement->status }}">{{ ucfirst($announcement->status) }}</span>
            </div>

            <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Title --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Title *</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                        value="{{ old('title', $announcement->title) }}" required>
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Type --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Category / Announcement Type *</label>
                    <select name="announcement_type" class="form-select @error('announcement_type') is-invalid @enderror" required>
                        @foreach(\App\Models\Announcement::TYPES as $t)
                            <option value="{{ $t }}" {{ old('announcement_type', $announcement->announcement_type) == $t ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_',' ',$t)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('announcement_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Content Body --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Announcement Content / Body *</label>
                    <textarea name="body" class="form-control @error('body') is-invalid @enderror" rows="8" required>{{ old('body', $announcement->body) }}</textarea>
                    @error('body') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Banner Image --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">Banner Image</label>
                    @if($announcement->image)
                        <div class="mb-2 p-2 border rounded bg-light" id="current-image-box">
                            <div class="text-muted small mb-1">Current Banner Image:</div>
                            <img src="{{ asset('storage/'.$announcement->image) }}" class="rounded w-100 mb-2" style="max-height:200px;object-fit:cover">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="remove_image">
                                <label class="form-check-label text-danger small fw-semibold" for="remove_image">
                                    <i class="ti ti-trash me-1"></i>Delete current banner image
                                </label>
                            </div>
                        </div>
                    @endif
                    <input type="file" name="image" id="ann-image" accept="image/*" class="form-control" onchange="previewBanner(this)">
                    <div class="form-text">Upload a new image to replace the current one.</div>
                    
                    <div id="preview-container" class="d-none mt-2 position-relative">
                        <img id="banner-preview" src="" class="rounded w-100" style="max-height:220px;object-fit:cover;border:1px solid #e2e8f0;">
                    </div>
                </div>

                {{-- Status & Scheduling Options --}}
                <div class="card p-3 mb-4 bg-light" style="border: 1px solid #cbd5e1; border-radius: 12px;">
                    <label class="form-label fw-bold text-dark mb-2"><i class="ti ti-settings me-1 text-primary"></i>Update Status &amp; Scheduling</label>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-12 col-md-3">
                            <div class="p-2 border rounded bg-white h-100">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="action" id="act_keep" value="keep" 
                                        {{ old('action', 'keep') === 'keep' ? 'checked' : '' }} onchange="toggleScheduleDate()">
                                    <label class="form-check-label fw-semibold" for="act_keep" style="font-size:12.5px">
                                        Keep Current Status ({{ ucfirst($announcement->status) }})
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-3">
                            <div class="p-2 border rounded bg-white h-100">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="action" id="act_publish" value="publish_now" 
                                        {{ old('action') === 'publish_now' ? 'checked' : '' }} onchange="toggleScheduleDate()">
                                    <label class="form-check-label fw-semibold" for="act_publish" style="font-size:12.5px">
                                        <i class="ti ti-broadcast text-success me-1"></i>Publish Immediately
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-3">
                            <div class="p-2 border rounded bg-white h-100">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="action" id="act_schedule" value="schedule" 
                                        {{ old('action') === 'schedule' || $announcement->status === 'scheduled' ? 'checked' : '' }} onchange="toggleScheduleDate()">
                                    <label class="form-check-label fw-semibold" for="act_schedule" style="font-size:12.5px">
                                        <i class="ti ti-calendar-time text-primary me-1"></i>Schedule for Later
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-3">
                            <div class="p-2 border rounded bg-white h-100">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="action" id="act_draft" value="draft" 
                                        {{ old('action') === 'draft' ? 'checked' : '' }} onchange="toggleScheduleDate()">
                                    <label class="form-check-label fw-semibold" for="act_draft" style="font-size:12.5px">
                                        <i class="ti ti-file-pencil text-warning me-1"></i>Move to Draft
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Schedule Date & Time Picker --}}
                    <div id="schedule-date-group" class="{{ old('action') === 'schedule' || $announcement->status === 'scheduled' ? '' : 'd-none' }} pt-2 border-top">
                        <label class="form-label fw-semibold small text-primary">
                            <i class="ti ti-clock me-1"></i>Publish Date &amp; Time
                        </label>
                        <div style="max-width: 300px;">
                            <input type="datetime-local" name="scheduled_at" id="scheduled_at" class="form-control form-control-sm"
                                value="{{ old('scheduled_at', $announcement->published_at ? $announcement->published_at->format('Y-m-d\TH:i') : now()->addDay()->format('Y-m-d\T08:00')) }}">
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex gap-2 pt-2 border-top">
                    <button type="submit" class="btn btn-navy">
                        <i class="ti ti-check me-1"></i>Save Changes
                    </button>
                    <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary">
                        Cancel
                    </a>
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
            document.getElementById('preview-container').classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function toggleScheduleDate() {
    const isSchedule = document.getElementById('act_schedule').checked;
    const scheduleGroup = document.getElementById('schedule-date-group');
    if (isSchedule) {
        scheduleGroup.classList.remove('d-none');
    } else {
        scheduleGroup.classList.add('d-none');
    }
}
</script>
@endpush
@endsection
