@extends('layouts.admin')
@section('title','QR Verification')
@section('page-title','QR Resident Verification')
@section('content')
<div class="row mt-2 justify-content-center">
    <div class="col-12 col-lg-7">
        <div class="card-custom">
            <h6 class="fw-semibold mb-3">Scan or enter resident ID / contact number</h6>
            @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
            @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
            <form method="POST" action="{{ route('admin.qr.verify') }}">
                @csrf
                <div class="d-flex gap-2 mb-3">
                    <input type="text" name="query" class="form-control" placeholder="RES-XXXX or contact number..." value="{{ old('query') }}" required autofocus>
                    <button type="submit" class="btn btn-navy" style="white-space:nowrap"><i class="ti ti-search me-1"></i>Verify</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="startQrScan()"><i class="ti ti-qrcode"></i></button>
                </div>
            </form>
            <div class="text-center py-4">
                <div style="width:120px;height:120px;background:#f4f6fa;border:1px solid #dde2ec;border-radius:10px;display:inline-flex;align-items:center;justify-content:center">
                    <i class="ti ti-qrcode" style="font-size:48px;color:#1a3a6b;opacity:0.3"></i>
                </div>
                <p class="text-muted mt-3" style="font-size:13px">Scan the resident QR code or type the resident ID to verify.</p>
            </div>
        </div>
    </div>
</div>
@endsection
