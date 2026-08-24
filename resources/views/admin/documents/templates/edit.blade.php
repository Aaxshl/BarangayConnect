@extends('layouts.admin')
@section('title','Edit Document Template')
@section('page-title','Edit Template — ' . $typeName)
@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center">
    <a href="{{ route('admin.documents.templates.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back to Templates
    </a>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-8">
        <div class="card-custom">
            <form method="POST" action="{{ route('admin.documents.templates.update', $type) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Document Title (Printed on Certificate) *</label>
                    <input type="text" name="title" class="form-control fw-bold" value="{{ old('title', $template->title) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Header Text / Official Subtitle</label>
                    <textarea name="header_text" class="form-control" rows="3" placeholder="Republic of the Philippines...">{{ old('header_text', $template->header_text) }}</textarea>
                    <div class="form-text">Displayed above document title on printed PDF.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Body Content Template *</label>
                    <textarea name="body_template" id="bodyTemplateArea" class="form-control font-monospace" rows="10" required style="font-size:13px;line-height:1.5">{{ old('body_template', $template->body_template) }}</textarea>
                    <div class="form-text mt-1">Use the quick placeholder tags listed on the right to dynamically substitute resident details.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Footer / Validity Notes</label>
                    <textarea name="footer_text" class="form-control" rows="2" placeholder="This document is not valid without official seal...">{{ old('footer_text', $template->footer_text) }}</textarea>
                </div>

                <hr class="my-4">
                <h6 class="fw-bold mb-3"><i class="ti ti-photo me-1"></i>Barangay Logo & Header Styling</h6>

                <div class="mb-3 form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="show_logo" id="showLogoSwitch" value="1" {{ old('show_logo', $template->show_logo)?'checked':'' }}>
                    <label class="form-check-label fw-medium" for="showLogoSwitch">Display Barangay Logo on Printed Document Header</label>
                </div>

                <div class="mb-3">
                    <label class="form-label">Custom Logo Image (Optional)</label>
                    <input type="file" name="custom_logo" class="form-control" accept="image/*">
                    <div class="form-text">Upload a specific seal/logo for this template. If left empty, system Barangay logo from Settings will be used.</div>
                    @if($template->custom_logo)
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $template->custom_logo) }}" height="50" class="border rounded p-1">
                        <span class="text-muted small ms-2">Current Custom Logo</span>
                    </div>
                    @endif
                </div>

                <hr class="my-4">
                <h6 class="fw-bold mb-3"><i class="ti ti-signature me-1"></i>Signatory Block</h6>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Signatory Title</label>
                        <input type="text" name="signatory_title" class="form-control" value="{{ old('signatory_title', $template->signatory_title ?: 'Barangay Captain') }}" placeholder="Barangay Captain">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Signatory Name Override (Optional)</label>
                        <input type="text" name="signatory_name" class="form-control" value="{{ old('signatory_name', $template->signatory_name) }}" placeholder="Leave blank to use Captain Name from Settings">
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-navy"><i class="ti ti-check me-1"></i>Save Template Changes</button>
                    <a href="{{ route('admin.documents.templates.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Available Placeholders Reference -->
    <div class="col-12 col-lg-4">
        <div class="card-custom sticky-top" style="top:80px">
            <h6 class="fw-bold mb-2"><i class="ti ti-code me-1"></i>Available Dynamic Tags</h6>
            <p class="text-muted small mb-3">Click any placeholder tag to copy/insert it into your body template:</p>

            <div class="d-flex flex-column gap-2">
                @foreach($placeholders as $tag => $desc)
                <div class="p-2 border rounded bg-light hover-shadow" style="cursor:pointer" onclick="insertTag('{{ $tag }}')">
                    <code class="fw-bold text-primary" style="font-size:12px">{{ $tag }}</code>
                    <div class="text-muted small" style="font-size:11px">{{ $desc }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
function insertTag(tag) {
    const area = document.getElementById('bodyTemplateArea');
    const start = area.selectionStart;
    const end = area.selectionEnd;
    const text = area.value;
    area.value = text.substring(0, start) + tag + text.substring(end);
    area.focus();
    area.selectionStart = area.selectionEnd = start + tag.length;
}
</script>
@endsection
