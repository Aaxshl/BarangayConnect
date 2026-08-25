<?php
namespace App\Http\Controllers;
use App\Models\{Resident, CitizenRequest, Document, Announcement, Setting, Household};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ResidentPortalController extends Controller {
    public function home() {
        $announcements = Announcement::where('status','published')->latest()->limit(6)->get();
        $settings = Setting::all()->pluck('value','key')->toArray();
        return view('resident.home', compact('announcements', 'settings'));
    }
    public function login() { 
        return redirect()->route('login'); 
    }
    public function doLogin(Request $request) { 
        return app(AuthController::class)->login($request); 
    }
    public function logout(Request $request) { 
        return app(AuthController::class)->logout($request); 
    }
    public function register() { 
        $households = Household::orderBy('household_id')->get();
        $settings = Setting::all()->pluck('value','key')->toArray();
        return view('resident.register', compact('households', 'settings')); 
    }
    public function storeRegister(Request $request) {
        $validated = $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'middle_name'    => 'nullable|string|max:100',
            'birthdate'      => 'required|date|before:today',
            'gender'         => 'required|in:male,female',
            'civil_status'   => 'required|in:single,married,widowed,separated',
            'address'        => 'required|string|max:255',
            'purok'          => 'nullable|string|max:100',
            'zone'           => 'nullable|string|max:100',
            'contact_number' => 'required|string|max:20|unique:residents,contact_number',
            'occupation'     => 'nullable|string|max:100',
            'household_id'   => 'nullable|exists:households,id',
            'photo'          => 'nullable|image|max:2048',
            'password'       => 'required|min:6|confirmed',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('residents', 'public');
        }

        if (!empty($validated['birthdate'])) {
            $validated['age'] = Carbon::parse($validated['birthdate'])->age;
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['status']   = 'active';
        $resident = Resident::create($validated);

        $qrData = 'RES-' . str_pad($resident->id, 5, '0', STR_PAD_LEFT);
        $resident->update(['qr_code' => $qrData]);

        session(['resident_id' => $resident->id]);
        return redirect()->route('portal.dashboard')->with('success', 'Account registered successfully! Welcome to your resident dashboard.');
    }
    public function dashboard() {
        $resident = $this->getResident();
        $myRequests  = CitizenRequest::where('resident_id',$resident->id)->latest()->limit(5)->get();
        $myDocuments = Document::where('resident_id',$resident->id)->latest()->limit(5)->get();
        $announcements = Announcement::where('status','published')->latest()->limit(3)->get();
        return view('resident.dashboard', compact('resident','myRequests','myDocuments','announcements'));
    }
    public function requestForm() {
        return view('resident.request');
    }
    public function submitRequest(Request $request) {
        $validated = $request->validate([
            'document_type'  => 'required|in:'.implode(',',array_keys(Document::TYPES)),
            'purpose'        => 'required|string|max:255',
            'number_of_copies' => 'required|integer|min:1|max:5',
        ]);
        $resident = $this->getResident();
        $year  = date('Y');
        $count = Document::whereYear('created_at',$year)->count() + 1;
        $doc = Document::create([
            'document_number'  => 'DOC-'.$year.'-'.str_pad($count,4,'0',STR_PAD_LEFT),
            'resident_id'      => $resident->id,
            'document_type'    => $validated['document_type'],
            'purpose'          => $validated['purpose'],
            'number_of_copies' => $validated['number_of_copies'],
            'issue_date'       => today(),
            'status'           => 'pending',
        ]);
        return redirect()->route('portal.track.detail', $doc->document_number)
            ->with('success','Request submitted. Tracking: '.$doc->document_number);
    }
    public function reportForm() { return view('resident.report'); }
    public function submitReport(Request $request) {
        $validated = $request->validate([
            'request_type' => 'required|in:'.implode(',',CitizenRequest::TYPES),
            'description'  => 'required|string|min:10',
            'location'     => 'required|string',
            'latitude'     => 'nullable|numeric',
            'longitude'    => 'nullable|numeric',
        ]);
        $resident = $this->getResident();
        $tracking = CitizenRequest::generateTracking();
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('requests','public');
        }
        CitizenRequest::create(array_merge($validated,[
            'tracking_number' => $tracking,
            'resident_id'     => $resident->id,
            'status'          => 'pending',
        ]));
        return redirect()->route('portal.track.detail',$tracking)
            ->with('success','Report submitted. Tracking: '.$tracking);
    }
    public function track() {
        $resident = $this->getResident();
        $myRequests  = CitizenRequest::where('resident_id',$resident->id)->latest()->paginate(10);
        $myDocuments = Document::where('resident_id',$resident->id)->latest()->paginate(10);
        return view('resident.track', compact('resident','myRequests','myDocuments'));
    }
    public function trackDetail($tracking) {
        $resident = $this->getResident();
        $item = CitizenRequest::where('tracking_number',$tracking)
                    ->where('resident_id',$resident->id)->first()
              ?? Document::where('document_number',$tracking)
                    ->where('resident_id',$resident->id)->first();
        abort_unless($item, 404);
        return view('resident.track-detail', compact('item','tracking'));
    }
    public function announcements() {
        $announcements = Announcement::where('status','published')->latest()->paginate(10);
        return view('resident.announcements', compact('announcements'));
    }
    public function profile() {
        $resident = $this->getResident();
        return view('resident.profile', compact('resident'));
    }
    public function updateProfile(Request $request) {
        $resident = $this->getResident();
        $validated = $request->validate([
            'contact_number' => 'required|unique:residents,contact_number,'.$resident->id,
            'address'        => 'required|string',
        ]);
        $resident->update($validated);
        return back()->with('success','Profile updated.');
    }
    private function getResident() {
        return Resident::findOrFail(session('resident_id'));
    }
}
