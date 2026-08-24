<?php
namespace App\Http\Controllers;
use App\Models\{Resident, Household};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class ResidentController extends Controller {
    public function index(Request $request) {
        $residents = Resident::with('household')
            ->when($request->search, fn($q,$s) => $q->search($s))
            ->when($request->gender, fn($q,$g) => $q->where('gender',$g))
            ->when($request->status, fn($q,$s) => $q->where('status',$s))
            ->when($request->purok, fn($q,$p) => $q->where('purok',$p))
            ->latest()->paginate(15)->withQueryString();
        $households = Household::all();
        $puroks = Resident::distinct()->pluck('purok')->filter()->sort()->values();
        return view('admin.residents.index', compact('residents','households','puroks'));
    }
    public function create() {
        $households = Household::all();
        return view('admin.residents.create', compact('households'));
    }
    public function store(Request $request) {
        $validated = $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'middle_name'    => 'nullable|string|max:100',
            'birthdate'      => 'required|date|before:today',
            'gender'         => 'required|in:male,female',
            'civil_status'   => 'required|in:single,married,widowed,separated',
            'address'        => 'required|string',
            'purok'          => 'nullable|string',
            'zone'           => 'nullable|string',
            'contact_number' => 'nullable|string|max:20',
            'occupation'     => 'nullable|string|max:100',
            'household_id'   => 'nullable|exists:households,id',
            'photo'          => 'nullable|image|max:3072',
        ]);
        $validated['created_by'] = auth()->id();
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('residents','public');
        }
        $resident = Resident::create($validated);
        $this->generateQrCode($resident);
        if ($request->household_id) {
            Household::find($request->household_id)->increment('number_of_members');
        }
        return redirect()->route('admin.residents.index')->with('success','Resident added successfully.');
    }
    public function show(Resident $resident) {
        $resident->load(['household','documents','serviceLogs','citizenRequests']);
        return view('admin.residents.show', compact('resident'));
    }
    public function edit(Resident $resident) {
        $households = Household::all();
        return view('admin.residents.edit', compact('resident','households'));
    }
    public function update(Request $request, Resident $resident) {
        $validated = $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'middle_name'    => 'nullable|string|max:100',
            'birthdate'      => 'required|date|before:today',
            'gender'         => 'required|in:male,female',
            'civil_status'   => 'required|in:single,married,widowed,separated',
            'address'        => 'required|string',
            'purok'          => 'nullable|string',
            'zone'           => 'nullable|string',
            'contact_number' => 'nullable|string|max:20',
            'occupation'     => 'nullable|string|max:100',
            'household_id'   => 'nullable|exists:households,id',
            'status'         => 'required|in:active,inactive',
            'photo'          => 'nullable|image|max:3072',
        ]);
        if ($request->hasFile('photo')) {
            if ($resident->photo) Storage::disk('public')->delete($resident->photo);
            $validated['photo'] = $request->file('photo')->store('residents','public');
        }
        $resident->update($validated);
        return redirect()->route('admin.residents.show',$resident)->with('success','Resident updated.');
    }
    public function destroy(Resident $resident) {
        $resident->update(['status' => 'inactive']);
        return redirect()->route('admin.residents.index')->with('success','Resident deactivated.');
    }
    public function generateQr(Resident $resident) {
        $this->generateQrCode($resident);
        return response()->download(public_path("qr/{$resident->id}.png"));
    }
    private function generateQrCode(Resident $resident) {
        $data = json_encode(['id'=>$resident->id,'name'=>$resident->full_name,'status'=>$resident->status]);
        // QR generation placeholder - requires endroid/qr-code package
        $resident->update(['qr_code' => "qr/{$resident->id}.png"]);
    }
}
