@extends('layouts.admin')
@section('title','Households')
@section('page-title','Households')
@section('content')
<div class="d-flex justify-content-between align-items-center mt-2 mb-3">
    <div class="d-flex gap-3" style="font-size:13.5px;color:#888">
        <span>Total: <strong style="color:#1a1a2e">{{ $households->total() }}</strong></span>
    </div>
    @if(auth()->user()->canDo('residents.create_edit'))
    <a href="{{ route('admin.households.create') }}" class="btn btn-navy btn-sm"><i class="ti ti-plus me-1"></i>Register household</a>
    @endif
</div>
<div class="table-responsive-custom">
    <table class="table-custom">
        <thead><tr><th>Household ID</th><th>Head</th><th>Address</th><th>Purok / Zone</th><th>Members</th><th>Contact</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
            @forelse($households as $hh)
            <tr>
                <td style="font-family:monospace;font-size:12px">{{ $hh->household_id }}</td>
                <td>{{ optional($hh->head)->full_name ?? '—' }}</td>
                <td>{{ $hh->address }}</td>
                <td>{{ $hh->purok }} {{ $hh->zone }}</td>
                <td>{{ $hh->number_of_members }}</td>
                <td>{{ $hh->contact_number ?: '—' }}</td>
                <td><span class="badge-status badge-{{ $hh->status }}">{{ ucfirst($hh->status) }}</span></td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.households.show',$hh) }}" class="btn btn-sm btn-outline-secondary py-0 px-2" title="View"><i class="ti ti-eye"></i></a>
                        @if(auth()->user()->canDo('residents.create_edit'))
                        <a href="{{ route('admin.households.edit',$hh) }}" class="btn btn-sm btn-outline-navy py-0 px-2" title="Edit"><i class="ti ti-pencil"></i></a>
                        @endif
                    </div>
                </td>
            </tr>
            @empty<tr><td colspan="8" class="text-center py-4 text-muted">No households found.</td></tr>@endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $households->links() }}</div>
@endsection
