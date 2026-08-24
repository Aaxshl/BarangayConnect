@extends('layouts.admin')
@section('title','Issue Document')
@section('page-title','Issue Document')
@section('content')
<div class="mb-3">
    <a href="{{ route('admin.documents.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back to Documents
    </a>
</div>
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card-custom">
            <form method="POST" action="{{ route('admin.documents.store') }}">
                @csrf
                @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
                <div class="mb-3">
                    <label class="form-label">Resident *</label>
                    <select name="resident_id" class="form-select @error('resident_id') is-invalid @enderror" required>
                        <option value="">Select resident...</option>
                        @foreach($residents as $r)
                        <option value="{{ $r->id }}" {{ (old('resident_id', request('resident'))==$r->id)?'selected':'' }}>{{ $r->full_name }} — {{ $r->address }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Document type *</label>
                    <select name="document_type" class="form-select @error('document_type') is-invalid @enderror" required>
                        <option value="">Select type...</option>
                        @foreach(\App\Models\Document::TYPES as $k => $v)
                        <option value="{{ $k }}" {{ old('document_type')==$k?'selected':'' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3"><label class="form-label">Purpose *</label><input type="text" name="purpose" class="form-control" value="{{ old('purpose') }}" placeholder="e.g. Employment, Bank requirement" required></div>
                <div class="mb-3"><label class="form-label">Number of copies</label><select name="number_of_copies" class="form-select"><option value="1">1 copy</option><option value="2">2 copies</option><option value="3">3 copies</option></select></div>
                <div class="mb-3"><label class="form-label">Remarks</label><textarea name="remarks" class="form-control" rows="2" placeholder="Additional notes...">{{ old('remarks') }}</textarea></div>
                <div class="d-flex gap-2 mt-2">
                    <button type="submit" class="btn btn-navy"><i class="ti ti-file-check me-1"></i>Issue Document</button>
                    <a href="{{ route('admin.documents.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
