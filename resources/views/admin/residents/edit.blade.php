@extends('layouts.admin')
@section('title','Edit Resident')
@section('page-title','Edit Resident')
@section('content')
<div class="mb-3">
    <a href="{{ route('admin.residents.show', $resident) }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back to Resident
    </a>
</div>
<div class="row justify-content-center">
    <div class="col-12 col-lg-9">
        <div class="card-custom">
            <form method="POST" action="{{ route('admin.residents.update',$resident) }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                {{-- Photo Upload --}}
                <div class="mb-4 d-flex flex-column align-items-center gap-3">
                    <div id="photo-preview-wrap" style="width:100px;height:100px;border-radius:50%;overflow:hidden;background:#e6f1fb;display:flex;align-items:center;justify-content:center;border:3px solid #c8dff5;font-size:36px;color:#185fa5">
                        @if($resident->photo)
                            <img id="photo-preview" src="{{ asset('storage/'.$resident->photo) }}" style="width:100%;height:100%;object-fit:cover">
                        @else
                            <i class="ti ti-user" id="photo-icon"></i>
                            <img id="photo-preview" src="" class="d-none" style="width:100%;height:100%;object-fit:cover">
                        @endif
                    </div>
                    <div>
                        <label for="photo" class="btn btn-outline-secondary btn-sm">
                            <i class="ti ti-camera me-1"></i> Change Photo
                        </label>
                        <input type="file" name="photo" id="photo" accept="image/*" class="d-none" onchange="previewPhoto(this)">
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-4"><label class="form-label">First Name *</label><input type="text" name="first_name" class="form-control" value="{{ old('first_name',$resident->first_name) }}" required></div>
                    <div class="col-md-4"><label class="form-label">Last Name *</label><input type="text" name="last_name" class="form-control" value="{{ old('last_name',$resident->last_name) }}" required></div>
                    <div class="col-md-4"><label class="form-label">Middle Name</label><input type="text" name="middle_name" class="form-control" value="{{ old('middle_name',$resident->middle_name) }}"></div>
                    <div class="col-md-4"><label class="form-label">Birthdate *</label><input type="date" name="birthdate" class="form-control" value="{{ old('birthdate', $resident->birthdate?->format('Y-m-d')) }}" required></div>
                    <div class="col-md-4"><label class="form-label">Gender *</label><select name="gender" class="form-select" required><option value="male" {{ $resident->gender=='male'?'selected':'' }}>Male</option><option value="female" {{ $resident->gender=='female'?'selected':'' }}>Female</option></select></div>
                    <div class="col-md-4"><label class="form-label">Civil Status *</label><select name="civil_status" class="form-select" required><option value="single" {{ $resident->civil_status=='single'?'selected':'' }}>Single</option><option value="married" {{ $resident->civil_status=='married'?'selected':'' }}>Married</option><option value="widowed" {{ $resident->civil_status=='widowed'?'selected':'' }}>Widowed</option><option value="separated" {{ $resident->civil_status=='separated'?'selected':'' }}>Separated</option></select></div>
                    <div class="col-12"><label class="form-label">Address *</label><input type="text" name="address" class="form-control" value="{{ old('address',$resident->address) }}" required></div>
                    <div class="col-md-4"><label class="form-label">Purok</label><input type="text" name="purok" class="form-control" value="{{ old('purok',$resident->purok) }}"></div>
                    <div class="col-md-4"><label class="form-label">Zone</label><input type="text" name="zone" class="form-control" value="{{ old('zone',$resident->zone) }}"></div>
                    <div class="col-md-4"><label class="form-label">Contact Number</label><input type="text" name="contact_number" class="form-control" value="{{ old('contact_number',$resident->contact_number) }}"></div>
                    <div class="col-md-4"><label class="form-label">Occupation</label><input type="text" name="occupation" class="form-control" value="{{ old('occupation',$resident->occupation) }}"></div>
                    <div class="col-md-4"><label class="form-label">Household</label><select name="household_id" class="form-select"><option value="">None</option>@foreach($households as $hh)<option value="{{ $hh->id }}" {{ $resident->household_id==$hh->id?'selected':'' }}>{{ $hh->household_id }}</option>@endforeach</select></div>
                    <div class="col-md-4"><label class="form-label">Status</label><select name="status" class="form-select"><option value="active" {{ $resident->status=='active'?'selected':'' }}>Active</option><option value="inactive" {{ $resident->status=='inactive'?'selected':'' }}>Inactive</option></select></div>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-navy">Update Resident</button>
                    <a href="{{ route('admin.residents.show',$resident) }}" class="btn btn-outline-secondary">Cancel</a>
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
            const icon = document.getElementById('photo-icon');
            if (icon) icon.classList.add('d-none');
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
