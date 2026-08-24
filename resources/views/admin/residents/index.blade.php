@extends('layouts.admin')
@section('title','Residents')
@section('page-title','Residents')
@section('content')
<div class="d-flex align-items-center justify-content-between mt-2 mb-3 flex-wrap gap-2">
    <form method="GET" class="d-flex gap-2 flex-wrap">
        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name, contact..." value="{{ request('search') }}" style="width:200px">
        <select name="gender" class="form-select form-select-sm" style="width:120px">
            <option value="">All genders</option>
            <option value="male" {{ request('gender')=='male'?'selected':'' }}>Male</option>
            <option value="female" {{ request('gender')=='female'?'selected':'' }}>Female</option>
        </select>
        <select name="status" class="form-select form-select-sm" style="width:120px">
            <option value="">All statuses</option>
            <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
            <option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>Inactive</option>
        </select>
        <button type="submit" class="btn btn-navy btn-sm">Filter</button>
        @if(request()->hasAny(['search','gender','status']))
            <a href="{{ route('admin.residents.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
        @endif
    </form>
    <a href="{{ route('admin.residents.create') }}" class="btn btn-navy btn-sm"><i class="ti ti-user-plus me-1"></i>Add Resident</a>
</div>
<div class="table-responsive-custom">
    <table class="table-custom">
        <thead><tr><th>Name</th><th>Age</th><th>Gender</th><th>Civil Status</th><th>Purok</th><th>Contact</th><th>Household</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
            @forelse($residents as $r)
            <tr>
                <td><a href="{{ route('admin.residents.show',$r) }}" style="color:#1a3a6b;font-weight:500">{{ $r->full_name }}</a></td>
                <td>{{ $r->birthdate ? $r->birthdate->age : '—' }}</td>
                <td>{{ ucfirst($r->gender) }}</td>
                <td>{{ ucfirst($r->civil_status) }}</td>
                <td>{{ $r->purok ?: '—' }}</td>
                <td>{{ $r->contact_number ?: '—' }}</td>
                <td>{{ optional($r->household)->household_id ?: '—' }}</td>
                <td><span class="badge-status badge-{{ $r->status }}">{{ ucfirst($r->status) }}</span></td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.residents.show',$r) }}" class="btn btn-sm btn-outline-secondary py-0 px-2"><i class="ti ti-eye"></i></a>
                        <a href="{{ route('admin.residents.edit',$r) }}" class="btn btn-sm btn-outline-navy py-0 px-2"><i class="ti ti-pencil"></i></a>
                        <a href="{{ route('admin.residents.qr',$r) }}" class="btn btn-sm btn-outline-secondary py-0 px-2"><i class="ti ti-qrcode"></i></a>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center py-4 text-muted">No residents found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
    <small class="text-muted">Showing {{ $residents->firstItem() }}–{{ $residents->lastItem() }} of {{ $residents->total() }}</small>
    {{ $residents->links() }}
</div>
@endsection
