@extends('layouts.sk')
@section('title', 'Manage SK Councilors')

@section('content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div>
        <h4 class="fw-bold mb-1 text-primary"><i class="ti ti-user-check me-2"></i>Sangguniang Kabataan Council Members</h4>
        <div class="text-muted small">Manage official SK Councilor user accounts, assignments, and portal access permissions.</div>
    </div>
    <a href="{{ route('sk.councilors.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
        <i class="ti ti-user-plus"></i> Add SK Councilor
    </a>
</div>

{{-- Councilors Table --}}
<div class="card-custom p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-custom-sk align-middle mb-0">
            <thead>
                <tr>
                    <th>Council Member</th>
                    <th>Email Address</th>
                    <th>Contact Number</th>
                    <th class="text-center">Coordinated Programs</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($councilors as $c)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg, #0d9488, #0f766e);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px">
                                {{ substr($c->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="fw-bold text-dark">{{ $c->name }}</div>
                                <span class="badge bg-light text-secondary border" style="font-size:10.5px">SK Councilor</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span style="font-size:13px">{{ $c->email }}</span>
                    </td>
                    <td>
                        <span style="font-family:monospace;font-size:13px">{{ $c->contact_number ?: '—' }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-info text-dark px-2 py-1" style="font-size:11px">
                            {{ $c->coordinated_count }} {{ Str::plural('program', $c->coordinated_count) }}
                        </span>
                    </td>
                    <td>
                        @if($c->status === 'active')
                            <span class="badge bg-success" style="font-size:10.5px">Active</span>
                        @else
                            <span class="badge bg-secondary" style="font-size:10.5px">Inactive / Suspended</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1 align-items-center">
                            <a href="{{ route('sk.councilors.edit', $c) }}" class="btn btn-outline-primary btn-sm py-1 px-2" title="Edit details & password">
                                <i class="ti ti-edit"></i> Edit
                            </a>

                            {{-- Status Toggle --}}
                            <form method="POST" action="{{ route('sk.councilors.toggle', $c) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm py-1 px-2 {{ $c->status === 'active' ? 'btn-outline-warning' : 'btn-outline-success' }}" title="{{ $c->status === 'active' ? 'Deactivate account' : 'Activate account' }}">
                                    <i class="ti {{ $c->status === 'active' ? 'ti-user-off' : 'ti-user-check' }}"></i>
                                    {{ $c->status === 'active' ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>

                            {{-- Delete --}}
                            <form method="POST" action="{{ route('sk.councilors.destroy', $c) }}" onsubmit="return confirm('Remove SK Councilor {{ $c->name }} from the council? This action cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm py-1 px-2" title="Delete account">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="ti ti-users fs-1 d-block mb-2 text-secondary"></i>
                        No SK Councilors registered yet. <a href="{{ route('sk.councilors.create') }}">Add the first SK Councilor!</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $councilors->links() }}
</div>
@endsection
