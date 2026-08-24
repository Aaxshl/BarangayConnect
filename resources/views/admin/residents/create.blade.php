@extends('layouts.admin')
@section('title','Add Resident')
@section('page-title','Add Resident')
@section('content')
<div class="mb-3">
    <a href="{{ route('admin.residents.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back to Residents
    </a>
</div>
<div class="row justify-content-center">
    <div class="col-12 col-lg-9">
        <div class="card-custom">
            <form method="POST" action="{{ route('admin.residents.store') }}" enctype="multipart/form-data">
                @csrf
                {{-- Photo Upload --}}
                <div class="mb-4 d-flex flex-column align-items-center gap-3">
                    <div id="photo-preview-wrap" style="width:100px;height:100px;border-radius:50%;overflow:hidden;background:#e6f1fb;display:flex;align-items:center;justify-content:center;border:3px solid #c8dff5;font-size:36px;color:#185fa5">
                        <i class="ti ti-user" id="photo-icon"></i>
                        <img id="photo-preview" src="" class="d-none" style="width:100%;height:100%;object-fit:cover">
                    </div>
                    <div>
                        <label for="photo" class="btn btn-outline-secondary btn-sm">
                            <i class="ti ti-camera me-1"></i> Upload Photo
                        </label>
                        <input type="file" name="photo" id="photo" accept="image/*" class="d-none" onchange="previewPhoto(this)">
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-4"><label class="form-label">First Name *</label><input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required><div class="invalid-feedback">{{ $errors->first('first_name') }}</div></div>
                    <div class="col-md-4"><label class="form-label">Last Name *</label><input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required><div class="invalid-feedback">{{ $errors->first('last_name') }}</div></div>
                    <div class="col-md-4"><label class="form-label">Middle Name</label><input type="text" name="middle_name" class="form-control" value="{{ old('middle_name') }}"></div>
                    <div class="col-md-4"><label class="form-label">Birthdate *</label><input type="date" name="birthdate" class="form-control @error('birthdate') is-invalid @enderror" value="{{ old('birthdate') }}" required><div class="invalid-feedback">{{ $errors->first('birthdate') }}</div></div>
                    <div class="col-md-4"><label class="form-label">Gender *</label><select name="gender" class="form-select @error('gender') is-invalid @enderror" required><option value="">Select...</option><option value="male" {{ old('gender')=='male'?'selected':'' }}>Male</option><option value="female" {{ old('gender')=='female'?'selected':'' }}>Female</option></select><div class="invalid-feedback">{{ $errors->first('gender') }}</div></div>
                    <div class="col-md-4"><label class="form-label">Civil Status *</label><select name="civil_status" class="form-select @error('civil_status') is-invalid @enderror" required><option value="">Select...</option><option value="single">Single</option><option value="married">Married</option><option value="widowed">Widowed</option><option value="separated">Separated</option></select><div class="invalid-feedback">{{ $errors->first('civil_status') }}</div></div>
                    <div class="col-12"><label class="form-label">Address *</label><input type="text" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address') }}" required></div>
                    <div class="col-md-4"><label class="form-label">Purok</label><input type="text" name="purok" class="form-control" value="{{ old('purok') }}" placeholder="e.g. Purok 1"></div>
                    <div class="col-md-4"><label class="form-label">Zone</label><input type="text" name="zone" class="form-control" value="{{ old('zone') }}" placeholder="e.g. Zone A"></div>
                    <div class="col-md-4"><label class="form-label">Contact Number</label><input type="text" name="contact_number" class="form-control" value="{{ old('contact_number') }}" placeholder="09XXXXXXXXX"></div>
                    <div class="col-md-6"><label class="form-label">Occupation</label><input type="text" name="occupation" class="form-control" value="{{ old('occupation') }}"></div>
                    <div class="col-md-6"><label class="form-label">Household</label><select name="household_id" class="form-select"><option value="">None / Unassigned</option>@foreach($households as $hh)<option value="{{ $hh->id }}" {{ old('household_id')==$hh->id?'selected':'' }}>{{ $hh->household_id }} — {{ $hh->address }}</option>@endforeach</select></div>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-navy">Save Resident</button>
                    <a href="{{ route('admin.residents.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('photo-icon').classList.add('d-none');
            const img = document.getElementById('photo-preview');
            img.src = e.target.result;
            img.classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection
