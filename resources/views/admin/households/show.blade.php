@extends('layouts.admin')
@section('title','Household Record')
@section('page-title','Household Record — ' . $household->household_id)
@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <a href="{{ route('admin.households.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back to Households
    </a>
    <a href="{{ route('admin.households.edit', $household) }}" class="btn btn-navy btn-sm">
        <i class="ti ti-pencil me-1"></i> Edit Household Profile
    </a>
</div>

<div class="row g-3">
    <!-- Household Summary -->
    <div class="col-12 col-lg-4">
        <div class="card-custom text-center mb-3">
            <div style="width:64px;height:64px;border-radius:12px;background:#e6f1fb;display:flex;align-items:center;justify-content:center;font-size:26px;color:#185fa5;margin:0 auto 12px">
                <i class="ti ti-home"></i>
            </div>
            <h5 class="fw-bold mb-1">{{ $household->household_id }}</h5>
            <div class="text-muted small mb-3">{{ $household->address }}</div>
            <div class="d-flex justify-content-center gap-2">
                <span class="badge bg-light text-dark border"><i class="ti ti-users me-1"></i>{{ $household->members->count() }} Members</span>
                @if($household->purok)<span class="badge bg-light text-dark border">{{ $household->purok }}</span>@endif
                @if($household->zone)<span class="badge bg-light text-dark border">{{ $household->zone }}</span>@endif
            </div>
        </div>

        <div class="card-custom mb-3">
            <h6 class="fw-semibold mb-3">Household Information</h6>
            <div class="row g-2" style="font-size:13.5px">
                <div class="col-12"><span class="text-muted">Full Address</span><div class="fw-medium">{{ $household->address }}</div></div>
                <div class="col-6"><span class="text-muted">Purok</span><div class="fw-medium">{{ $household->purok ?: '—' }}</div></div>
                <div class="col-6"><span class="text-muted">Zone</span><div class="fw-medium">{{ $household->zone ?: '—' }}</div></div>
                <div class="col-12"><span class="text-muted">Contact Number</span><div class="fw-medium">{{ $household->contact_number ?: '—' }}</div></div>
                <div class="col-12"><span class="text-muted">Registered On</span><div class="fw-medium">{{ $household->created_at->format('M d, Y') }}</div></div>
            </div>
        </div>

        <!-- Head of Household -->
        <div class="card-custom">
            <h6 class="fw-semibold mb-2">Head of Household</h6>
            @if($household->head)
            <div class="d-flex align-items-center gap-3 pt-2">
                <div style="width:42px;height:42px;border-radius:50%;background:#185fa5;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700">
                    {{ substr($household->head->first_name,0,1) }}{{ substr($household->head->last_name,0,1) }}
                </div>
                <div>
                    <a href="{{ route('admin.residents.show', $household->head) }}" class="fw-bold text-navy text-decoration-none d-block">
                        {{ $household->head->full_name }}
                    </a>
                    <span class="text-muted small">{{ $household->head->contact_number ?: 'No contact' }}</span>
                </div>
            </div>
            @else
            <div class="alert alert-warning py-2 mb-0 small">No Head of Household assigned yet.</div>
            @endif
        </div>
    </div>

    <!-- Members List & Assignment -->
    <div class="col-12 col-lg-8">
        <div class="card-custom mb-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <h6 class="fw-semibold mb-0">Household Members ({{ $household->members->count() }})</h6>
                <button type="button" class="btn btn-navy btn-sm" data-bs-toggle="modal" data-bs-target="#assignMemberModal">
                    <i class="ti ti-user-plus me-1"></i> Add Member
                </button>
            </div>

            <div class="table-responsive">
                <table class="table align-middle text-nowrap mb-0" style="font-size:13px">
                    <thead class="bg-light">
                        <tr>
                            <th>Resident Name</th>
                            <th>Role</th>
                            <th>Age / Gender</th>
                            <th>Civil Status</th>
                            <th>Occupation</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($household->members as $member)
                        <tr>
                            <td>
                                <a href="{{ route('admin.residents.show', $member) }}" class="fw-semibold text-decoration-none">
                                    {{ $member->full_name }}
                                </a>
                            </td>
                            <td>
                                @if($household->head_resident_id == $member->id)
                                    <span class="badge bg-primary">Head</span>
                                @else
                                    <span class="badge bg-light text-dark border">Member</span>
                                @endif
                            </td>
                            <td>{{ $member->birthdate ? $member->birthdate->age . ' yrs / ' . ucfirst($member->gender) : ucfirst($member->gender) }}</td>
                            <td>{{ ucfirst($member->civil_status) }}</td>
                            <td>{{ $member->occupation ?: '—' }}</td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('admin.households.remove', [$household, $member]) }}" onsubmit="return confirm('Remove resident from this household?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2" title="Remove from household">
                                        <i class="ti ti-user-minus"></i> Remove
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No members assigned to this household yet. Click "Add Member" to assign residents.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Assign Member Modal -->
<div class="modal fade" id="assignMemberModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Assign Resident to Household</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.households.assign', $household) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Resident</label>
                        <select name="resident_id" class="form-select" required>
                            <option value="">Choose resident to add...</option>
                            @foreach($unassignedResidents as $r)
                            <option value="{{ $r->id }}">{{ $r->full_name }} — {{ $r->address }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Only active residents currently without a household are listed.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-navy">Assign Member</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
