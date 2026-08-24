<?php
namespace App\Http\Controllers;
use App\Models\{ServiceLog, Resident, User};
use Illuminate\Http\Request;

class ServiceLogController extends Controller {
    public function index(Request $request) {
        $logs = ServiceLog::with('resident','assignedTo')
            ->when($request->type,   fn($q,$t) => $q->where('service_type',$t))
            ->when($request->status, fn($q,$s) => $q->where('status',$s))
            ->latest()->paginate(15)->withQueryString();
        return view('admin.service-logs.index', compact('logs'));
    }
    public function create() {
        $residents = Resident::active()->orderBy('last_name')->get();
        $staff     = User::where('status','active')->get();
        return view('admin.service-logs.create', compact('residents','staff'));
    }
    public function store(Request $request) {
        $validated = $request->validate([
            'service_type'    => 'required|in:'.implode(',',ServiceLog::TYPES),
            'resident_id'     => 'nullable|exists:residents,id',
            'description'     => 'required|string',
            'date_of_service' => 'required|date',
            'assigned_to'     => 'nullable|exists:users,id',
            'remarks'         => 'nullable|string',
        ]);
        $count = ServiceLog::count() + 1;
        $validated['log_number']  = 'SL-'.str_pad($count,4,'0',STR_PAD_LEFT);
        $validated['status']      = 'pending';
        $validated['created_by']  = auth()->id();
        ServiceLog::create($validated);
        return redirect()->route('admin.service-logs.index')->with('success','Service log created.');
    }
    public function show(ServiceLog $serviceLog) {
        $serviceLog->load('resident','assignedTo');
        return view('admin.service-logs.show', compact('serviceLog'));
    }
    public function edit(ServiceLog $serviceLog) {
        $residents = Resident::active()->orderBy('last_name')->get();
        $staff     = User::where('status','active')->get();
        return view('admin.service-logs.edit', compact('serviceLog','residents','staff'));
    }
    public function update(Request $request, ServiceLog $serviceLog) {
        $validated = $request->validate([
            'service_type'    => 'required|in:'.implode(',',ServiceLog::TYPES),
            'status'          => 'required|in:'.implode(',',ServiceLog::STATUSES),
            'description'     => 'required|string',
            'date_of_service' => 'required|date',
            'assigned_to'     => 'nullable|exists:users,id',
            'remarks'         => 'nullable|string',
        ]);
        $serviceLog->update($validated);
        return redirect()->route('admin.service-logs.show',$serviceLog)->with('success','Service log updated.');
    }
    public function destroy(ServiceLog $serviceLog) {
        $serviceLog->update(['status' => 'closed']);
        return back()->with('success','Service log closed.');
    }
}
