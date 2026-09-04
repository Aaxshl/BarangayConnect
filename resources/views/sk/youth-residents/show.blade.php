@extends('layouts.sk')
@section('title', $resident->full_name . ' — Youth Profile')

@section('content')
<div class="mb-3">
    <a href="{{ route('sk.youth-residents.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back to Youth Directory
    </a>
</div>

<div class="row g-3">
    {{-- Left: Youth Profile Summary Card --}}
    <div class="col-12 col-lg-4">
        <div class="card-custom text-center py-4">
            <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg, #0d9488, #0f766e);color:#fff;display:flex;align-items:center;justify-content:center;font-size:32px;font-weight:700;margin:0 auto 12px">
                {{ substr($resident->first_name, 0, 1) }}{{ substr($resident->last_name, 0, 1) }}
            </div>
            <h5 class="fw-bold mb-1">{{ $resident->full_name }}</h5>
            <div class="text-muted small mb-2">{{ $resident->occupation ?: 'Youth Resident' }}</div>

            @php
                $age = $resident->age;
                $badgeClass = 'bg-secondary';
                $cohortLabel = 'Youth (15–24)';
                if ($age >= 15 && $age <= 17) {
                    $badgeClass = 'bg-info text-dark';
                    $cohortLabel = '15–17 yrs (Adolescent / Teen)';
                } elseif ($age >= 18 && $age <= 24) {
                    $badgeClass = 'bg-primary';
                    $cohortLabel = '18–24 yrs (Young Adult / College)';
                }
            @endphp
            <div class="d-flex justify-content-center gap-1 mb-3">
                <span class="badge {{ $badgeClass }} px-2 py-1">{{ $cohortLabel }}</span>
                <span class="badge bg-success px-2 py-1">KK Member</span>
            </div>

            <hr class="my-3">

            <div class="text-start" style="font-size:13.5px">
                <div class="mb-2"><span class="text-muted d-block small">Contact Number</span> <strong>{{ $resident->contact_number ?: 'Not provided' }}</strong></div>
                <div class="mb-2"><span class="text-muted d-block small">Civil Status</span> <strong class="text-capitalize">{{ $resident->civil_status }}</strong></div>
                <div class="mb-2"><span class="text-muted d-block small">Gender</span> <strong class="text-capitalize">{{ $resident->gender }}</strong></div>
                <div class="mb-0"><span class="text-muted d-block small">Registered Since</span> <strong>{{ $resident->created_at->format('M d, Y') }}</strong></div>
            </div>
        </div>
    </div>

    {{-- Right: Address & Household, Activity History --}}
    <div class="col-12 col-lg-8">
        {{-- Residence Details --}}
        <div class="card-custom mb-3">
            <h6 class="fw-bold text-primary mb-3"><i class="ti ti-home me-2"></i>Household &amp; Residence Information</h6>
            <div class="row g-3" style="font-size:13.5px">
                <div class="col-md-6">
                    <span class="text-muted d-block small">Barangay Address</span>
                    <div class="fw-semibold">{{ $resident->address }}</div>
                </div>
                <div class="col-md-3">
                    <span class="text-muted d-block small">Purok</span>
                    <div class="fw-semibold">{{ $resident->purok ?: '—' }}</div>
                </div>
                <div class="col-md-3">
                    <span class="text-muted d-block small">Zone</span>
                    <div class="fw-semibold">{{ $resident->zone ?: '—' }}</div>
                </div>
                <div class="col-md-6">
                    <span class="text-muted d-block small">Household ID</span>
                    <div class="fw-semibold">
                        @if($resident->household)
                            <code>{{ $resident->household->household_id }}</code> ({{ $resident->household->number_of_members }} members)
                        @else
                            <span class="text-muted">Unassigned</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <span class="text-muted d-block small">Birthdate &amp; Exact Age</span>
                    <div class="fw-semibold">
                        {{ optional($resident->birthdate)->format('F d, Y') }} ({{ $resident->age }} years old)
                    </div>
                </div>
            </div>
        </div>

        {{-- Issued Documents History --}}
        <div class="card-custom">
            <h6 class="fw-bold text-primary mb-3"><i class="ti ti-file-text me-2"></i>Barangay Document Requests History</h6>
            <div class="table-responsive">
                <table class="table table-sm table-custom-sk align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Doc No.</th>
                            <th>Type</th>
                            <th>Purpose</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($resident->documents as $doc)
                        <tr>
                            <td style="font-family:monospace;font-size:12px">{{ $doc->document_number }}</td>
                            <td>{{ \App\Models\Document::TYPES[$doc->document_type] ?? $doc->document_type }}</td>
                            <td>{{ Str::limit($doc->purpose, 30) }}</td>
                            <td><span class="badge-status badge-{{ $doc->status }}" style="font-size:10px">{{ ucfirst($doc->status) }}</span></td>
                            <td style="font-size:12px">{{ $doc->issue_date->format('M d, Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-3 text-muted small">No document requests on record.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
