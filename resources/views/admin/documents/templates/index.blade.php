@extends('layouts.admin')
@section('title','Document Templates')
@section('page-title','Document Templates & Customization')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <a href="{{ route('admin.documents.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back to Documents
    </a>
    <div>
        <a href="{{ route('admin.documents.index') }}" class="btn btn-outline-navy btn-sm">View Issued Documents</a>
    </div>
</div>

<div class="card-custom mb-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h6 class="fw-bold mb-0">Customizable Document Templates</h6>
            <div class="text-muted small">Manage header, body text, footer notes, signatories, and Barangay logo display per document type.</div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table align-middle text-nowrap mb-0" style="font-size:13.5px">
            <thead class="bg-light">
                <tr>
                    <th>Document Type</th>
                    <th>Template Title</th>
                    <th>Logo Display</th>
                    <th>Signatory Title</th>
                    <th>Last Updated</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($types as $key => $name)
                @php $tpl = $templates[$key]; @endphp
                <tr>
                    <td>
                        <span class="fw-bold text-navy">{{ $name }}</span>
                        <div class="text-muted small">Type: {{ $key }}</div>
                    </td>
                    <td>
                        <span class="fw-medium">{{ $tpl->title }}</span>
                    </td>
                    <td>
                        @if($tpl->custom_logo)
                            <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="ti ti-photo me-1"></i>Custom Logo</span>
                        @elseif($tpl->show_logo)
                            <span class="badge bg-info-subtle text-info border border-info-subtle"><i class="ti ti-check me-1"></i>System Logo</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border"><i class="ti ti-x me-1"></i>Hidden</span>
                        @endif
                    </td>
                    <td>{{ $tpl->signatory_title ?: 'Barangay Captain' }}</td>
                    <td class="text-muted small">{{ $tpl->updated_at->format('M d, Y g:i A') }}</td>
                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-1">
                            <a href="{{ route('admin.documents.templates.edit', $key) }}" class="btn btn-navy btn-sm py-1 px-2">
                                <i class="ti ti-pencil me-1"></i> Edit Template
                            </a>
                            <form method="POST" action="{{ route('admin.documents.templates.reset', $key) }}" onsubmit="return confirm('Reset this template to default content?')">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary btn-sm py-1 px-2" title="Reset to default">
                                    <i class="ti ti-rotate-2"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
