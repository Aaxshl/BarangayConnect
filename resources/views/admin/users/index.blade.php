@extends('layouts.admin')
@section('title','Users')
@section('page-title','User Management')
@section('content')
<div class="d-flex justify-content-end mt-2 mb-3">
    <a href="{{ route('admin.users.create') }}" class="btn btn-navy btn-sm"><i class="ti ti-user-plus me-1"></i>Add User</a>
</div>
<div class="table-responsive-custom">
    <table class="table-custom">
        <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Contact</th><th>Last login</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td class="fw-medium">{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td><span class="badge-status badge-{{ $user->role === 'administrator' ? 'processing' : 'pending' }}">{{ ucfirst($user->role) }}</span></td>
                <td>{{ $user->contact_number ?: '—' }}</td>
                <td>{{ $user->updated_at->format('M d, Y') }}</td>
                <td><span class="badge-status badge-{{ $user->status }}">{{ ucfirst($user->status) }}</span></td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.users.edit',$user) }}" class="btn btn-sm btn-outline-navy py-0 px-2"><i class="ti ti-pencil"></i></a>
                        @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.deactivate',$user) }}">@csrf
                                <button class="btn btn-sm btn-outline-secondary py-0 px-2" title="{{ $user->status==='active'?'Deactivate':'Activate' }}">
                                    <i class="ti ti-{{ $user->status==='active'?'user-off':'user-check' }}"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty<tr><td colspan="7" class="text-center py-4 text-muted">No users found.</td></tr>@endforelse
        </tbody>
    </table>
</div>
@endsection
