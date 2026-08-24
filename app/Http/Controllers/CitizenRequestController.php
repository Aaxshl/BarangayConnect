<?php
namespace App\Http\Controllers;
use App\Models\{CitizenRequest, ServiceLog, User};
use Illuminate\Http\Request;

class CitizenRequestController extends Controller {
    public function index(Request $request) {
        $requests = CitizenRequest::with('resident','assignedTo')
            ->when($request->status, fn($q,$s) => $q->where('status',$s))
            ->when($request->type, fn($q,$t) => $q->where('request_type',$t))
            ->when($request->search, fn($q,$s) => $q->where('tracking_number','like',"%{$s}%"))
            ->latest()->paginate(15)->withQueryString();
        $staff = User::where('status','active')->get();
        return view('admin.citizen-requests.index', compact('requests','staff'));
    }
    public function show(CitizenRequest $citizenRequest) {
        $citizenRequest->load('resident','assignedTo');
        // Mark as viewed to clear unread indicator
        if (!$citizenRequest->viewed_at) {
            $citizenRequest->update(['viewed_at' => now()]);
        }
        $staff = User::where('status','active')->get();
        return view('admin.citizen-requests.show', compact('citizenRequest','staff'));
    }
    public function assign(Request $request, CitizenRequest $citizenRequest) {
        $request->validate(['assigned_to' => 'required|exists:users,id']);
        $citizenRequest->update([
            'assigned_to' => $request->assigned_to,
            'status'      => in_array($citizenRequest->status, ['pending']) ? 'assigned' : $citizenRequest->status,
        ]);
        return back()->with('success','Request assigned.');
    }
    public function updateStatus(Request $request, CitizenRequest $citizenRequest) {
        $request->validate([
            'status'          => 'required|in:'.implode(',',CitizenRequest::STATUSES),
            'resolution_note' => 'nullable|string|max:1000',
        ]);
        $data = [
            'status'          => $request->status,
            'resolution_note' => $request->resolution_note,
        ];
        if ($request->status === 'resolved' && !$citizenRequest->resolved_at) {
            $data['resolved_at'] = now();
        }
        $citizenRequest->update($data);
        return back()->with('success','Status updated to "' . ucwords(str_replace('_',' ',$request->status)) . '".');
    }
    public function convertToServiceLog(CitizenRequest $citizenRequest) {
        $count = ServiceLog::count() + 1;
        ServiceLog::create([
            'log_number'      => 'SL-'.str_pad($count,4,'0',STR_PAD_LEFT),
            'service_type'    => 'complaint',
            'resident_id'     => $citizenRequest->resident_id,
            'description'     => $citizenRequest->description,
            'date_of_service' => today(),
            'status'          => 'in_progress',
            'assigned_to'     => $citizenRequest->assigned_to,
            'created_by'      => auth()->id(),
        ]);
        $citizenRequest->update(['status' => 'in_progress']);
        return back()->with('success','Converted to service log.');
    }
    public function destroy(CitizenRequest $citizenRequest) {
        $citizenRequest->update(['status' => 'closed']);
        return redirect()->route('admin.citizen-requests.index')->with('success','Request closed.');
    }
}
