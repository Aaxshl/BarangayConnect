<?php
namespace App\Http\Controllers;

use App\Models\{CitizenRequest, ServiceLog, User};
use Illuminate\Http\Request;

class CitizenRequestController extends Controller {
    public function index(Request $request) {
        $statusFilter = $request->query('status');
        $typeFilter   = $request->query('type');
        $searchFilter = $request->query('search');

        $query = CitizenRequest::with('resident', 'assignedTo')
            ->when($typeFilter, fn($q, $t) => $q->where('request_type', $t))
            ->when($searchFilter, function ($q, $s) {
                $q->where(function($sub) use ($s) {
                    $sub->where('tracking_number', 'like', "%{$s}%")
                        ->orWhere('location', 'like', "%{$s}%")
                        ->orWhere('description', 'like', "%{$s}%");
                });
            });

        if ($statusFilter) {
            $requests = (clone $query)->where('status', $statusFilter)->latest()->paginate(15)->withQueryString();
            $resolvedRequests = collect();
            $hasSplitView = false;
        } else {
            $requests = (clone $query)->whereNotIn('status', ['resolved', 'closed'])->latest()->paginate(15)->withQueryString();
            $resolvedRequests = (clone $query)->whereIn('status', ['resolved', 'closed'])->latest()->get();
            $hasSplitView = true;
        }

        $activeCount   = CitizenRequest::whereNotIn('status', ['resolved', 'closed'])->count();
        $resolvedCount = CitizenRequest::whereIn('status', ['resolved', 'closed'])->count();

        $staff = User::where('status', 'active')->get();
        return view('admin.citizen-requests.index', compact('requests', 'resolvedRequests', 'hasSplitView', 'activeCount', 'resolvedCount', 'staff'));
    }

    public function show(CitizenRequest $citizenRequest) {
        $citizenRequest->load('resident','assignedTo');

        // Automatically transition 'pending' -> 'under_review' when admin views it
        if ($citizenRequest->status === 'pending') {
            $citizenRequest->update([
                'status'    => 'under_review',
                'viewed_at' => now(),
            ]);
        } elseif (!$citizenRequest->viewed_at) {
            $citizenRequest->update(['viewed_at' => now()]);
        }

        $staff = User::where('status','active')->get();
        return view('admin.citizen-requests.show', compact('citizenRequest','staff'));
    }

    public function assign(Request $request, CitizenRequest $citizenRequest) {
        $request->validate(['assigned_to' => 'required|exists:users,id']);
        
        $newStatus = in_array($citizenRequest->status, ['pending', 'under_review']) ? 'assigned' : $citizenRequest->status;

        $citizenRequest->update([
            'assigned_to' => $request->assigned_to,
            'status'      => $newStatus,
        ]);

        $assignedUser = User::find($request->assigned_to);
        $assignedName = $assignedUser ? $assignedUser->name : 'Staff';

        return back()->with('success', "Assigned to {$assignedName}. Status set to " . ucwords(str_replace('_',' ',$newStatus)) . ".");
    }

    public function updateStatus(Request $request, CitizenRequest $citizenRequest) {
        $request->validate([
            'status'          => 'required|in:'.implode(',', CitizenRequest::STATUSES),
            'resolution_note' => 'nullable|string|max:2000',
        ]);

        if ($request->status === 'in_progress' && !$citizenRequest->assigned_to) {
            return back()->with('error', 'Please assign a personnel first before starting the investigation / work.');
        }

        $data = [
            'status' => $request->status,
        ];

        if ($request->filled('resolution_note')) {
            $data['resolution_note'] = $request->resolution_note;
        }

        if ($request->status === 'resolved') {
            $data['resolved_at'] = now();
            if ($request->filled('resolution_note')) {
                $data['resolution_note'] = $request->resolution_note;
            }
        } elseif ($request->status !== 'resolved' && $citizenRequest->status === 'resolved') {
            // Reopening or moving back
            $data['resolved_at'] = null;
        }

        $citizenRequest->update($data);

        return back()->with('success', 'Status updated to "' . ucwords(str_replace('_',' ',$request->status)) . '".');
    }

    public function convertToServiceLog(CitizenRequest $citizenRequest) {
        $count = ServiceLog::count() + 1;
        ServiceLog::create([
            'log_number'      => 'SL-'.str_pad($count,4,'0',STR_PAD_LEFT),
            'service_type'    => 'complaint',
            'resident_id'     => $citizenRequest->resident_id,
            'description'     => $citizenRequest->description,
            'date_of_service' => today(),
            'status'          => $citizenRequest->assigned_to ? 'assigned' : 'pending',
            'assigned_to'     => $citizenRequest->assigned_to,
            'created_by'      => auth()->id(),
        ]);

        $citizenRequest->update(['status' => 'in_progress']);
        return back()->with('success','Converted to official blotter/service log record.');
    }

    public function destroy(CitizenRequest $citizenRequest) {
        $citizenRequest->update(['status' => 'closed']);
        return redirect()->route('admin.citizen-requests.index')->with('success','Request closed.');
    }
}
