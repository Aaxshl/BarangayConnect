@extends('layouts.admin')
@section('title','Add User')
@section('page-title','Add User')
@section('content')
<div class="mb-3">
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back to Users
    </a>
</div>
<div class="row justify-content-center">
    <div class="col-12 col-lg-7">
        <div class="card-custom">
            @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="mb-3"><label class="form-label">Full name *</label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
                <div class="mb-3"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" value="{{ old('email') }}" required></div>
                <div class="mb-3"><label class="form-label">Role *</label><select name="role" class="form-select" required><option value="">Select role...</option>@foreach(\App\Models\User::ROLES as $r)<option value="{{ $r }}">{{ ucfirst($r) }}</option>@endforeach</select></div>
                <div class="mb-3"><label class="form-label">Contact number</label><input type="text" name="contact_number" class="form-control" value="{{ old('contact_number') }}"></div>
                <div class="mb-3"><label class="form-label">Password *</label><input type="password" name="password" class="form-control" required></div>
                <div class="mb-4"><label class="form-label">Confirm password *</label><input type="password" name="password_confirmation" class="form-control" required></div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-navy">Create user</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
