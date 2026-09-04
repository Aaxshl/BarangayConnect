@extends('layouts.sk')
@section('title', 'Youth Residents Directory')

@section('content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div>
        <h4 class="fw-bold mb-1 text-primary"><i class="ti ti-users me-2"></i>Youth Residents Directory (Ages 15–24)</h4>
        <div class="text-muted small">Registered youth members of the Katipunan ng Kabataan.</div>
    </div>
    <div>
        <span class="badge bg-primary fs-6 px-3 py-2">
            {{ number_format($youthResidents->total()) }} Youth Residents Found
        </span>
    </div>
</div>

{{-- Filters Card --}}
<div class="card-custom mb-3 p-3">
    <form method="GET" action="{{ route('sk.youth-residents.index') }}" class="row g-2 align-items-center">
        <div class="col-12 col-md-4">
            <div class="input-group input-group-sm">
                <span class="input-group-text"><i class="ti ti-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Search by name or contact..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-6 col-md-3">
            <select name="bracket" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Youth Brackets (15–24)</option>
                <option value="15-17" {{ request('bracket') === '15-17' ? 'selected' : '' }}>15–17 yrs (Adolescents &amp; Teens)</option>
                <option value="18-24" {{ request('bracket') === '18-24' ? 'selected' : '' }}>18–24 yrs (Young Adults / College)</option>
            </select>
        </div>
        <div class="col-6 col-md-2">
            <select name="gender" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Genders</option>
                <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Female</option>
            </select>
        </div>
        <div class="col-6 col-md-2">
            <select name="purok" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Puroks</option>
                @foreach($puroks as $p)
                    <option value="{{ $p }}" {{ request('purok') === $p ? 'selected' : '' }}>{{ $p }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-1 d-flex gap-1">
            <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
            @if(request()->hasAny(['search', 'bracket', 'gender', 'purok']))
                <a href="{{ route('sk.youth-residents.index') }}" class="btn btn-outline-secondary btn-sm" title="Clear Filters"><i class="ti ti-x"></i></a>
            @endif
        </div>
    </form>
</div>

{{-- Youth Table --}}
<div class="card-custom p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-custom-sk align-middle mb-0">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Age</th>
                    <th>Youth Cohort</th>
                    <th>Gender</th>
                    <th>Address / Purok</th>
                    <th>Contact #</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($youthResidents as $res)
                @php
                    $age = $res->age;
                    $badgeClass = 'bg-secondary';
                    $cohortLabel = 'Youth (15–24)';
                    if ($age >= 15 && $age <= 17) {
                        $badgeClass = 'bg-info text-dark';
                        $cohortLabel = '15–17 (Teens)';
                    } elseif ($age >= 18 && $age <= 24) {
                        $badgeClass = 'bg-primary';
                        $cohortLabel = '18–24 (Young Adult)';
                    }
                @endphp
                <tr>
                    <td>
                        <a href="{{ route('sk.youth-residents.show', $res) }}" class="fw-bold text-dark text-decoration-none">
                            {{ $res->full_name }}
                        </a>
                        @if($res->occupation)
                            <div class="text-muted small">{{ $res->occupation }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="fw-bold">{{ $age }} yrs</span>
                        <div class="text-muted small" style="font-size:11px">{{ optional($res->birthdate)->format('M d, Y') }}</div>
                    </td>
                    <td>
                        <span class="badge {{ $badgeClass }} badge-youth-bracket">{{ $cohortLabel }}</span>
                    </td>
                    <td>
                        <span class="text-capitalize">{{ $res->gender }}</span>
                    </td>
                    <td>
                        <div style="font-size:13px">{{ $res->address }}</div>
                        @if($res->purok)
                            <span class="badge bg-light text-secondary border" style="font-size:10.5px">{{ $res->purok }}</span>
                        @endif
                    </td>
                    <td>
                        <span style="font-family:monospace">{{ $res->contact_number ?: '—' }}</span>
                    </td>
                    <td>
                        <a href="{{ route('sk.youth-residents.show', $res) }}" class="btn btn-outline-primary btn-sm py-1 px-2" style="font-size:12px">
                            <i class="ti ti-eye me-1"></i>View Profile
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="ti ti-users-minus fs-1 d-block mb-2 text-secondary"></i>
                        No youth residents found matching your criteria.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $youthResidents->links() }}
</div>
@endsection
