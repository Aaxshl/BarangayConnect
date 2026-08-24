@extends('layouts.admin')
@section('title','Register Household')
@section('page-title','Register Household')
@section('content')
<div class="row mt-2 justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card-custom">
            <form method="POST" action="{{ route('admin.households.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Household ID *</label><input type="text" name="household_id" class="form-control" placeholder="e.g. HH-0001" value="{{ old('household_id') }}" required></div>
                    <div class="col-md-6"><label class="form-label">Contact number</label><input type="text" name="contact_number" class="form-control" value="{{ old('contact_number') }}"></div>
                    <div class="col-12"><label class="form-label">Address *</label><input type="text" name="address" class="form-control" value="{{ old('address') }}" required></div>
                    <div class="col-md-6"><label class="form-label">Purok</label><input type="text" name="purok" class="form-control" value="{{ old('purok') }}"></div>
                    <div class="col-md-6"><label class="form-label">Zone</label><input type="text" name="zone" class="form-control" value="{{ old('zone') }}"></div>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-navy">Register household</button>
                    <a href="{{ route('admin.households.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
