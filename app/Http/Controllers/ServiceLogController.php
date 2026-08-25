<?php
namespace App\Http\Controllers;

use App\Models\{ServiceLog, Resident, User};
use Illuminate\Http\Request;

class ServiceLogController extends Controller {
    public function index(Request $request) {
        $query = ServiceLog::with('resident', 'assignedTo');

        $query->when($request->search, function ($q, $s) {
            $q->where(function ($sub) use ($s) {
                $sub->where('log_number', 'like', "%{$s}%")
                    ->orWhere('description', 'like', "%{$s}%")
                    ->orWhereHas('resident', fn($r) => $r->search($s));
            });
        });

        $query->when($request->type, fn($q, $t) => $q->where('service_type', $t));
        $query->when($request->status, fn($q, $s) => $q->where('status', $s));

        if ($request->status) {
            $logs = $query->latest('date_of_service')->paginate(15)->withQueryString();
            $completedLogs = collect();
        } else {
            $activeStatuses = ['pending', 'assigned', 'in_progress'];
            $completedStatuses = ['resolved', 'closed', 'cancelled'];

            $logs = (clone $query)->whereIn('status', $activeStatuses)
                ->latest('date_of_service')->paginate(15)->withQueryString();

            $completedLogs = (clone $query)->whereIn('status', $completedStatuses)
                ->latest('date_of_service')->limit(30)->get();
        }

        return view('admin.service-logs.index', compact('logs', 'completedLogs'));
    }

    public function create() {
        $residents = Resident::active()->orderBy('last_name')->get();
        $staff     = User::where('status', 'active')->get();
        return view('admin.service-logs.create', compact('residents', 'staff'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'service_type'    => 'required|in:' . implode(',', ServiceLog::TYPES),
            'resident_id'     => 'nullable|exists:residents,id',
            'description'     => 'required|string',
            'date_of_service' => 'required|date',
            'assigned_to'     => 'nullable|exists:users,id',
            'remarks'         => 'nullable|string',
        ]);

        $count = ServiceLog::count() + 1;
        $validated['log_number']  = 'SL-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        $validated['status']      = $request->filled('assigned_to') ? 'assigned' : 'pending';
        $validated['created_by']  = auth()->id();

        $serviceLog = ServiceLog::create($validated);

        return redirect()->route('admin.service-logs.show', $serviceLog)
            ->with('success', 'Service log entry created successfully.');
    }

    public function show(ServiceLog $serviceLog) {
        $serviceLog->load('resident', 'assignedTo', 'createdBy');
        $staff = User::where('status', 'active')->get();
        return view('admin.service-logs.show', compact('serviceLog', 'staff'));
    }

    public function edit(ServiceLog $serviceLog) {
        $residents = Resident::active()->orderBy('last_name')->get();
        $staff     = User::where('status', 'active')->get();
        return view('admin.service-logs.edit', compact('serviceLog', 'residents', 'staff'));
    }

    public function update(Request $request, ServiceLog $serviceLog) {
        $validated = $request->validate([
            'service_type'    => 'required|in:' . implode(',', ServiceLog::TYPES),
            'resident_id'     => 'nullable|exists:residents,id',
            'date_of_service' => 'required|date',
            'assigned_to'     => 'nullable|exists:users,id',
            'description'     => 'required|string',
            'remarks'         => 'nullable|string',
        ]);

        // If assigning for the first time while pending, advance to assigned
        if ($serviceLog->status === 'pending' && !empty($validated['assigned_to'])) {
            $validated['status'] = 'assigned';
        }

        $serviceLog->update($validated);
        return redirect()->route('admin.service-logs.show', $serviceLog)
            ->with('success', 'Service log details updated successfully.');
    }

    /**
     * Assign staff/personnel & schedule service date.
     */
    public function assign(Request $request, ServiceLog $serviceLog) {
        $validated = $request->validate([
            'assigned_to'     => 'required|exists:users,id',
            'date_of_service' => 'required|date',
            'remarks'         => 'nullable|string',
        ]);

        $updateData = [
            'assigned_to'     => $validated['assigned_to'],
            'date_of_service' => $validated['date_of_service'],
            'status'          => 'assigned',
        ];

        if ($request->filled('remarks')) {
            $updateData['remarks'] = $validated['remarks'];
        }

        $serviceLog->update($updateData);

        $assignedUser = User::find($validated['assigned_to']);
        return back()->with('success', 'Assigned to ' . $assignedUser->name . ' for ' . date('M d, Y', strtotime($validated['date_of_service'])) . '.');
    }

    /**
     * Progressive status transition handler.
     */
    public function updateStatus(Request $request, ServiceLog $serviceLog) {
        $action = $request->input('action');

        switch ($action) {
            case 'start':
                // assigned/pending -> in_progress
                if (!in_array($serviceLog->status, ['pending', 'assigned'])) {
                    return back()->with('error', 'Cannot start service in the current state.');
                }
                $serviceLog->update(['status' => 'in_progress']);
                return back()->with('success', 'Service action is now In Progress.');

            case 'resolve':
                // in_progress -> resolved
                if ($serviceLog->status !== 'in_progress') {
                    return back()->with('error', 'Service must be in progress before marking as completed/resolved.');
                }
                $request->validate([
                    'resolution_notes' => 'required|string|min:5',
                ]);
                $serviceLog->update([
                    'status'           => 'resolved',
                    'resolved_at'      => now(),
                    'resolution_notes' => $request->resolution_notes,
                ]);
                return back()->with('success', 'Service log marked as Resolved / Completed.');

            case 'close':
                // resolved -> closed
                if ($serviceLog->status !== 'resolved') {
                    return back()->with('error', 'Service must be resolved before closing.');
                }
                $serviceLog->update([
                    'status'    => 'closed',
                    'closed_at' => now(),
                ]);
                return back()->with('success', 'Service log closed and archived.');

            case 'reopen':
                // closed/resolved -> in_progress
                if (!in_array($serviceLog->status, ['resolved', 'closed', 'cancelled'])) {
                    return back()->with('error', 'Cannot reopen an active service log.');
                }
                $serviceLog->update([
                    'status'      => 'in_progress',
                    'resolved_at' => null,
                    'closed_at'   => null,
                ]);
                return back()->with('success', 'Service log reopened for further action.');

            case 'cancel':
                // active -> cancelled
                if (in_array($serviceLog->status, ['closed', 'cancelled'])) {
                    return back()->with('error', 'Cannot cancel a closed or already cancelled service log.');
                }
                $request->validate([
                    'cancellation_reason' => 'required|string|min:5',
                ]);
                $serviceLog->update([
                    'status'              => 'cancelled',
                    'cancellation_reason' => $request->cancellation_reason,
                ]);
                return back()->with('success', 'Service log cancelled / dismissed.');

            default:
                return back()->with('error', 'Invalid action.');
        }
    }

    public function destroy(ServiceLog $serviceLog) {
        $serviceLog->update(['status' => 'cancelled']);
        return redirect()->route('admin.service-logs.index')
            ->with('success', 'Service log cancelled.');
    }
}
