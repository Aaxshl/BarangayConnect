@extends('layouts.portal')
@section('title','Request a Document')
@section('content')
<div class="container-fluid px-3 px-md-4 mt-4" style="max-width:680px">
    <h2 class="section-title mb-4">Request a document</h2>
    <form method="POST" action="{{ route('portal.request.submit') }}">
        @csrf
        <input type="hidden" name="document_type" id="selected_doc_type" value="{{ old('document_type') }}">
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="portal-card mb-3">
            <div class="portal-card-title">Select document type</div>
            <div class="doc-type-grid">
                @foreach(\App\Models\Document::TYPES as $k => $v)
                <div class="doc-type-card {{ old('document_type')==$k ? 'selected' : '' }}" data-type="{{ $k }}" onclick="selectDoc(this)">
                    <i class="ti ti-file-certificate"></i>
                    <div class="doc-type-title">{{ $v }}</div>
                </div>
                @endforeach
            </div>
        </div>

        <div id="doc-request-fields" class="{{ old('document_type') ? '' : 'd-none' }}">
            <div class="portal-card mb-3">
                <div class="info-banner mb-3">
                    <i class="ti ti-info-circle"></i>
                    <span>Selected: <strong id="selected-doc-label">{{ old('document_type') ? \App\Models\Document::TYPES[old('document_type')] : '' }}</strong></span>
                </div>
                <div class="mb-3"><label class="form-label">Purpose *</label><input type="text" name="purpose" class="form-control" placeholder="e.g. Employment requirements" value="{{ old('purpose') }}" required></div>
                <div class="mb-0"><label class="form-label">Number of copies</label>
                    <select name="number_of_copies" class="form-select">
                        <option value="1" {{ old('number_of_copies',1)==1?'selected':'' }}>1 copy</option>
                        <option value="2" {{ old('number_of_copies')==2?'selected':'' }}>2 copies</option>
                        <option value="3" {{ old('number_of_copies')==3?'selected':'' }}>3 copies</option>
                    </select>
                </div>
            </div>
            <div class="portal-card mb-3">
                <div class="portal-card-title">Your details</div>
                @php $res = \App\Models\Resident::find(session('resident_id')); @endphp
                <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:13.5px"><span class="text-muted">Name</span><span>{{ $res->full_name }}</span></div>
                <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:13.5px"><span class="text-muted">Address</span><span>{{ $res->address }}</span></div>
                <div class="d-flex justify-content-between py-2" style="font-size:13.5px"><span class="text-muted">Resident ID</span><span style="font-family:monospace">RES-{{ str_pad($res->id,4,'0',STR_PAD_LEFT) }}</span></div>
            </div>
            <button type="submit" class="btn-navy-full"><i class="ti ti-send"></i> Submit request</button>
        </div>
    </form>
</div>
@endsection
