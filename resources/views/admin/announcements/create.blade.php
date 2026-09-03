@extends('layouts.admin')
@section('title','New Announcement')
@section('page-title','Create Announcement')
@section('content')
<div class="mb-3">
    <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back to Announcements
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-9">
        <div class="card-custom">
            <form method="POST" action="{{ route('admin.announcements.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- Title --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Title *</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                        value="{{ old('title') }}" placeholder="e.g. Annual Barangay General Assembly & Health Mission" required autofocus>
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Type --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Category / Announcement Type *</label>
                    <select name="announcement_type" class="form-select @error('announcement_type') is-invalid @enderror" required>
                        <option value="">Select category...</option>
                        @foreach(\App\Models\Announcement::TYPES as $t)
                            <option value="{{ $t }}" {{ old('announcement_type') == $t ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_',' ',$t)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('announcement_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Content Body --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Announcement Content / Body *</label>
                    <textarea name="body" class="form-control @error('body') is-invalid @enderror" rows="8" 
                        placeholder="Write the full details of the announcement, event schedule, reminders, guidelines, or instructions for the residents..." required>{{ old('body') }}</textarea>
                    @error('body') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Banner Image Upload --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">Banner Image <span class="text-muted fw-normal">(Optional — recommended for prominent display)</span></label>
                    <input type="file" name="image" id="ann-image" accept="image/*" class="form-control" onchange="previewBanner(this)">
                    <div class="form-text">Supported formats: JPG, PNG, WEBP up to 4MB.</div>
                    
                    <div id="preview-container" class="d-none mt-2 position-relative">
                        <img id="banner-preview" src="" class="rounded w-100" style="max-height:240px;object-fit:cover;border:1px solid #e2e8f0;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" onclick="removePreview()" title="Remove Image">
                            <i class="ti ti-trash me-1"></i>Remove
                        </button>
                    </div>
                </div>

                {{-- Publishing Options / Scheduling --}}
                <div class="card p-3 mb-4 bg-light" style="border: 1px solid #cbd5e1; border-radius: 12px;">
                    <label class="form-label fw-bold text-dark mb-2"><i class="ti ti-send me-1 text-primary"></i>Publishing &amp; Scheduling Options</label>
                    
                    <div class="row g-2 mb-3">
                        @if(auth()->user()->canDo('announcements.publish'))
                        <div class="col-12 col-md-4">
                            <div class="p-2 border rounded bg-white h-100">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="action" id="act_publish" value="publish_now" 
                                        {{ old('action', 'publish_now') === 'publish_now' ? 'checked' : '' }} onchange="toggleScheduleDate()">
                                    <label class="form-check-label fw-semibold" for="act_publish" style="font-size:13px">
                                        <i class="ti ti-broadcast text-success me-1"></i>Publish Immediately
                                    </label>
                                    <div class="text-muted small" style="font-size:11.5px">Goes live on the resident portal right away.</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="p-2 border rounded bg-white h-100">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="action" id="act_schedule" value="schedule" 
                                        {{ old('action') === 'schedule' ? 'checked' : '' }} onchange="toggleScheduleDate()">
                                    <label class="form-check-label fw-semibold" for="act_schedule" style="font-size:13px">
                                        <i class="ti ti-calendar-time text-primary me-1"></i>Schedule for Later
                                    </label>
                                    <div class="text-muted small" style="font-size:11.5px">Set a specific future date and time to publish.</div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="col-12 {{ auth()->user()->canDo('announcements.publish') ? 'col-md-4' : 'col-12' }}">
                            <div class="p-2 border rounded bg-white h-100">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="action" id="act_draft" value="draft" 
                                        {{ !auth()->user()->canDo('announcements.publish') || old('action') === 'draft' ? 'checked' : '' }} onchange="toggleScheduleDate()">
                                    <label class="form-check-label fw-semibold" for="act_draft" style="font-size:13px">
                                        <i class="ti ti-file-pencil text-warning me-1"></i>Save as Draft
                                    </label>
                                    <div class="text-muted small" style="font-size:11.5px">Keep private and finish editing later.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Schedule Date & Time Picker (Shown only when 'Schedule' is selected) --}}
                    <div id="schedule-date-group" class="{{ old('action') === 'schedule' ? '' : 'd-none' }} pt-2 border-top">
                        <label class="form-label fw-semibold small text-primary">
                            <i class="ti ti-clock me-1"></i>Select Scheduled Publish Date &amp; Time *
                        </label>
                        <div style="max-width: 300px;">
                            <input type="datetime-local" name="scheduled_at" id="scheduled_at" class="form-control form-control-sm @error('scheduled_at') is-invalid @enderror"
                                value="{{ old('scheduled_at', now()->addDay()->format('Y-m-d\T08:00')) }}">
                            @error('scheduled_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-text" style="font-size:12px">The announcement will be automatically released to residents at this scheduled timestamp.</div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex gap-2 pt-2 border-top">
                    <button type="submit" class="btn btn-navy">
                        <i class="ti ti-check me-1"></i>Submit Announcement
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

function removePreview() {
    const input = document.getElementById('ann-image');
    input.value = '';
    document.getElementById('banner-preview').src = '';
    document.getElementById('preview-container').classList.add('d-none');
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
