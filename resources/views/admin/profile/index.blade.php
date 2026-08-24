@extends('layouts.admin')
@section('title','My Profile')
@section('page-title','My Profile')
@section('content')
<div class="row mt-2">
    <div class="col-12">
        <div class="p-4 rounded-3 mb-3 d-flex align-items-center gap-3" style="background:#1a3a6b">
            <div style="width:56px;height:56px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;color:#fff">{{ substr($user->name,0,2) }}</div>
            <div><div style="font-size:18px;font-weight:600;color:#fff">{{ $user->name }}</div><div style="font-size:13px;color:rgba(255,255,255,0.65)">{{ ucfirst($user->role) }}</div></div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="card-custom">
            <h6 class="fw-semibold mb-3">Account details</h6>
            <form method="POST" action="{{ route('admin.profile.update') }}">
                @csrf @method('PUT')
                <div class="mb-3"><label class="form-label">Full name</label><input type="text" name="name" class="form-control" value="{{ $user->name }}"></div>
                <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ $user->email }}"></div>
                <div class="mb-3"><label class="form-label">Contact number</label><input type="text" name="contact_number" class="form-control" value="{{ $user->contact_number }}"></div>
                <button type="submit" class="btn btn-navy btn-sm">Update profile</button>
            </form>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="card-custom">
            <h6 class="fw-semibold mb-3">Change password</h6>
            <form method="POST" action="{{ route('admin.profile.password') }}">
                @csrf @method('PUT')
                <div class="mb-3"><label class="form-label">Current password</label><input type="password" name="current_password" class="form-control"></div>
                <div class="mb-3"><label class="form-label">New password</label><input type="password" name="password" class="form-control"></div>
                <div class="mb-3"><label class="form-label">Confirm new password</label><input type="password" name="password_confirmation" class="form-control"></div>
                <button type="submit" class="btn btn-navy btn-sm">Update password</button>
            </form>
        </div>
    </div>
</div>
@endsection
