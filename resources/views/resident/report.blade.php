@extends('layouts.portal')
@section('title','Report an Issue')
@section('content')
<div class="container-fluid px-3 px-md-4 mt-4" style="max-width:680px">
    <h2 class="section-title mb-4">Report a community issue</h2>
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('portal.report.submit') }}" enctype="multipart/form-data">
        @csrf
        <div class="portal-card mb-3">
            <div class="mb-3">
                <label class="form-label">Issue type *</label>
                <select name="request_type" class="form-select @error('request_type') is-invalid @enderror" required>
                    <option value="">Select issue type...</option>
                    @foreach(\App\Models\CitizenRequest::TYPES as $t)
                        <option value="{{ $t }}" {{ old('request_type')==$t?'selected':'' }}>{{ ucwords(str_replace('_',' ',$t)) }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback">{{ $errors->first('request_type') }}</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Location *</label>
                <div class="d-flex gap-2">
                    <input type="text" name="location" id="location-text" class="form-control @error('location') is-invalid @enderror" placeholder="e.g. Purok 2, near sari-sari store" value="{{ old('location') }}" required>
                    <button type="button" id="get-location-btn" class="btn btn-outline-secondary btn-sm" style="white-space:nowrap"><i class="ti ti-current-location"></i> GPS</button>
                </div>
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
                <div class="invalid-feedback">{{ $errors->first('location') }}</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Description *</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" placeholder="Describe the issue in detail. The more specific, the faster it can be addressed." required>{{ old('description') }}</textarea>
                <div class="invalid-feedback">{{ $errors->first('description') }}</div>
            </div>
            <div class="mb-0">
                <label class="form-label">Photo <span class="text-muted">(optional)</span></label>
                <div class="upload-zone" onclick="document.getElementById('photo').click()">
                    <i class="ti ti-camera-plus"></i>Tap to take a photo or upload from your gallery
                </div>
                <input type="file" name="photo" id="photo" accept="image/*" class="d-none">
                <img id="photo-preview" src="" class="d-none mt-2 rounded" style="max-width:100%;max-height:200px">
            </div>
        </div>
        <button type="submit" class="btn-navy-full"><i class="ti ti-send"></i> Submit report</button>
    </form>
</div>
@endsection
