<?php
namespace App\Http\Controllers;
use App\Models\{Resident, CitizenRequest, Document, Announcement, Setting, Household, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ResidentPortalController extends Controller {
    public function home() {
        $announcements = Announcement::published()->latest('published_at')->get();
        $settings = Setting::all()->pluck('value','key')->toArray();
        return view('resident.home', compact('announcements', 'settings'));
    }
    public function about() {
        $settings = Setting::all()->pluck('value','key')->toArray();
        $councilors = User::whereIn('role', ['captain', 'secretary', 'councilor'])->where('status', 'active')->get();
        return view('resident.about', compact('settings', 'councilors'));
    }
    public function careers() {
        return redirect()->route('portal.announcements', ['tab' => 'careers']);
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
        $myRequests  = CitizenRequest::where('resident_id',$resident->id)
            ->with('assignedTo')
            ->withCount(['comments' => fn($q) => $q->where('is_internal', false)])
            ->latest()
            ->limit(5)
            ->get();
        $myDocuments = Document::where('resident_id',$resident->id)->latest()->limit(5)->get();
        $announcements = Announcement::published()->latest('published_at')->limit(3)->get();
        return view('resident.dashboard', compact('resident','myRequests','myDocuments','announcements'));
    }
    public function requestForm() {
        $fees = Setting::getDocumentFees();
        $gcash = Setting::getGcashSettings();
        return view('resident.request', compact('fees', 'gcash'));
    }
    public function submitRequest(Request $request) {
        $validated = $request->validate([
            'document_type'     => 'required|in:'.implode(',',array_keys(Document::TYPES)),
            'purpose'           => 'required|string|max:255',
            'number_of_copies'  => 'required|integer|min:1|max:5',
            'payment_method'    => 'required|in:cash,gcash',
            'payment_reference' => 'nullable|required_if:payment_method,gcash|string|max:100',
            'payment_proof'     => 'nullable|required_if:payment_method,gcash|image|max:5120',
        ]);

        $resident = $this->getResident();
        $unitFee = Setting::getFeeFor($validated['document_type']);
        $totalFee = $unitFee * (int)$validated['number_of_copies'];

        $paymentMethod = $totalFee <= 0 ? 'free' : $validated['payment_method'];
        $paymentStatus = 'unpaid';

        if ($totalFee <= 0) {
            $paymentStatus = 'waived';
        } elseif ($paymentMethod === 'gcash' && $request->hasFile('payment_proof')) {
            $paymentStatus = 'pending_verification';
        }

        $proofPath = null;
        if ($request->hasFile('payment_proof')) {
            $proofPath = $request->file('payment_proof')->store('payment-proofs', 'public');
        }

        $year  = date('Y');
        $count = Document::whereYear('created_at',$year)->count() + 1;
        $doc = Document::create([
            'document_number'   => 'DOC-'.$year.'-'.str_pad($count,4,'0',STR_PAD_LEFT),
            'resident_id'       => $resident->id,
            'document_type'     => $validated['document_type'],
            'purpose'           => $validated['purpose'],
            'number_of_copies'  => $validated['number_of_copies'],
            'fee'               => $totalFee,
            'payment_method'    => $paymentMethod,
            'payment_status'    => $paymentStatus,
            'payment_reference' => $validated['payment_reference'] ?? null,
            'payment_proof'     => $proofPath,
            'issue_date'        => today(),
            'status'            => 'pending',
        ]);
        return redirect()->route('portal.track.detail', $doc->document_number)
            ->with('success','Request submitted successfully! Tracking: '.$doc->document_number);
    }
    public function reportForm() { return view('resident.report'); }
    public function submitReport(Request $request) {
        $validated = $request->validate([
            'request_type' => 'required|in:'.implode(',',CitizenRequest::TYPES),
            'description'  => 'required|string|min:10',
            'location'     => 'required|string',
            'latitude'     => 'nullable|numeric',
            'longitude'    => 'nullable|numeric',
            'priority'     => 'nullable|in:low,medium,high,urgent',
        ]);
        $resident = $this->getResident();
        $tracking = CitizenRequest::generateTracking();
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('requests','public');
        }
        CitizenRequest::create(array_merge($validated,[
            'tracking_number' => $tracking,
            'resident_id'     => $resident->id,
            'priority'        => $request->input('priority', 'medium'),
            'status'          => 'pending',
        ]));
        return redirect()->route('portal.track.detail',$tracking)
            ->with('success','Report submitted. Tracking: '.$tracking);
    }
    public function track(Request $request) {
        $resident = $this->getResident();

        // Active vs Completed Documents
        $activeDocStatuses = ['pending', 'under_review', 'processing', 'ready_for_pickup'];
        $completedDocStatuses = ['released', 'cancelled'];

        $activeDocuments = Document::with('issuedBy')->where('resident_id', $resident->id)
            ->whereIn('status', $activeDocStatuses)
            ->latest()
            ->get();

        $completedDocuments = Document::with('issuedBy')->where('resident_id', $resident->id)
            ->whereIn('status', $completedDocStatuses)
            ->latest()
            ->get();

        // Active vs Resolved/Closed Reports
        $activeReportStatuses = ['pending', 'under_review', 'assigned', 'in_progress'];
        $completedReportStatuses = ['resolved', 'closed', 'rejected', 'cancelled'];

        $activeReports = CitizenRequest::with([
            'assignedTo',
            'comments' => fn($q) => $q->where('is_internal', false)->with(['user', 'resident'])->oldest(),
        ])->where('resident_id', $resident->id)
            ->whereIn('status', $activeReportStatuses)
            ->latest()
            ->get();

        $completedReports = CitizenRequest::with([
            'assignedTo',
            'comments' => fn($q) => $q->where('is_internal', false)->with(['user', 'resident'])->oldest(),
        ])->where('resident_id', $resident->id)
            ->whereIn('status', $completedReportStatuses)
            ->latest()
            ->get();

        $totalDocs = $activeDocuments->count() + $completedDocuments->count();
        $totalReports = $activeReports->count() + $completedReports->count();
        $gcash = Setting::getGcashSettings();

        return view('resident.track', compact(
            'resident',
            'gcash',
            'activeDocuments',
            'completedDocuments',
            'activeReports',
            'completedReports',
            'totalDocs',
            'totalReports'
        ));
    }
    public function trackDetail($tracking) {
        $resident = $this->getResident();
        $cleanTracking = trim($tracking);
        $item = CitizenRequest::where(function($q) use ($cleanTracking) {
                        $q->where('tracking_number', $cleanTracking)
                          ->orWhere('tracking_number', strtoupper($cleanTracking));
                    })
                    ->where('resident_id', $resident->id)
                    ->with(['comments' => function($q) {
                        $q->where('is_internal', false)->with(['user', 'resident'])->oldest();
                    }, 'assignedTo'])
                    ->first()
              ?? Document::where(function($q) use ($cleanTracking) {
                        $q->where('document_number', $cleanTracking)
                          ->orWhere('document_number', strtoupper($cleanTracking));
                    })
                    ->where('resident_id', $resident->id)
                    ->with('issuedBy')
                    ->first();
        abort_unless($item, 404);
        return view('resident.track-detail', compact('item','tracking'));
    }
    public function uploadPaymentProof(Request $request, $tracking) {
        $resident = $this->getResident();
        $cleanTracking = trim($tracking);
        $document = Document::where(function($q) use ($cleanTracking) {
                $q->where('document_number', $cleanTracking)
                  ->orWhere('document_number', strtoupper($cleanTracking));
            })
            ->where('resident_id', $resident->id)
            ->firstOrFail();

        $request->validate([
            'payment_reference' => 'required|string|max:100',
            'payment_proof'     => 'required|image|max:5120',
        ]);

        $proofPath = $request->file('payment_proof')->store('payment-proofs', 'public');

        $document->update([
            'payment_method'      => 'gcash',
            'payment_reference'   => $request->payment_reference,
            'payment_proof'       => $proofPath,
            'payment_status'      => 'pending_verification',
            'payment_notes'       => null, // Clear prior decline note
        ]);

        return back()->with('success', 'Payment proof submitted successfully! The Barangay Office will review your transaction.');
    }

    public function storeComment(Request $request, $tracking) {
        $resident = $this->getResident();
        $cleanTracking = trim($tracking);
        $citizenRequest = CitizenRequest::where(function($q) use ($cleanTracking) {
                $q->where('tracking_number', $cleanTracking)
                  ->orWhere('tracking_number', strtoupper($cleanTracking));
            })
            ->where('resident_id', $resident->id)
            ->firstOrFail();

        $request->validate([
            'message'    => 'required|string|max:3000',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,pdf,doc,docx|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('comment-attachments', 'public');
        }

        $comment = $citizenRequest->comments()->create([
            'resident_id' => $resident->id,
            'sender_type' => 'resident',
            'message'     => $request->message,
            'attachment'  => $attachmentPath,
            'is_internal' => false,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Your message has been sent to the Barangay Office.',
                'request_id' => $citizenRequest->id,
                'comments_count' => $citizenRequest->comments()->where('is_internal', false)->count(),
                'comment' => [
                    'id' => $comment->id,
                    'message' => $comment->message,
                    'attachment_url' => $comment->attachment ? asset('storage/'.$comment->attachment) : null,
                    'created_at_human' => 'Just now',
                ],
            ]);
        }

        $prev = url()->previous();
        if (str_contains($prev, '/portal/track') && !str_contains($prev, '/portal/track/')) {
            return redirect($prev . '#repModal-' . $citizenRequest->id)
                ->with('open_modal', 'repModal-' . $citizenRequest->id)
                ->with('success', 'Your message has been sent to the Barangay Office.');
        }

        return back()->with('success', 'Your message has been sent to the Barangay Office.');
    }
    public function announcements(Request $request) {
        $query = Announcement::published();

        if ($request->source === 'sk') {
            $query->sk();
        } elseif ($request->source === 'barangay') {
            $query->barangay();
        }

        if ($request->filled('type')) {
            $query->where('announcement_type', $request->type);
        }

        $announcements = $query->latest('published_at')->paginate(12)->withQueryString();

        $counts = [
            'all'      => Announcement::published()->count(),
            'barangay' => Announcement::published()->barangay()->count(),
            'sk'       => Announcement::published()->sk()->count(),
        ];

        $settings = Setting::all()->pluck('value','key')->toArray();

        return view('resident.announcements', compact('announcements', 'counts', 'settings'));
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
