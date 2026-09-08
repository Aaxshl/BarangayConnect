<?php
namespace App\Http\Controllers;
use App\Models\{Document, Resident, Setting};
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentController extends Controller {
    public function index(Request $request) {
        $query = Document::with('resident','issuedBy');
        $query->when($request->type,   fn($q,$t) => $q->where('document_type',$t))
              ->when($request->status, fn($q,$s) => $q->where('status',$s))
              ->when($request->search, fn($q,$s) => $q->whereHas('resident', fn($r) => $r->search($s)));

        if ($request->status) {
            $documents = $query->latest()->paginate(15)->withQueryString();
            $completedDocuments = collect();
        } else {
            $activeStatuses    = ['pending','under_review','processing','ready_for_pickup'];
            $completedStatuses = ['released','cancelled'];
            $documents         = (clone $query)->whereIn('status', $activeStatuses)
                                    ->latest()->paginate(15)->withQueryString();
            $completedDocuments = (clone $query)->whereIn('status', $completedStatuses)
                                    ->latest()->limit(30)->get();
        }
        return view('admin.documents.index', compact('documents','completedDocuments'));
    }

    public function create() {
        $residents = Resident::active()->orderBy('last_name')->get();
        return view('admin.documents.create', compact('residents'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'resident_id'      => 'required|exists:residents,id',
            'document_type'    => 'required|in:'.implode(',',array_keys(Document::TYPES)),
            'purpose'          => 'required|string|max:255',
            'number_of_copies' => 'required|integer|min:1|max:10',
            'remarks'          => 'nullable|string',
        ]);
        $year  = date('Y');
        $count = Document::whereYear('created_at',$year)->count() + 1;
        $unitFee = Setting::getFeeFor($validated['document_type']);
        $totalFee = $unitFee * (int)$validated['number_of_copies'];

        $validated['document_number'] = 'DOC-'.$year.'-'.str_pad($count,4,'0',STR_PAD_LEFT);
        $validated['issue_date']     = today();
        $validated['status']         = 'pending';
        $validated['issued_by']      = auth()->id();
        $validated['fee']            = $totalFee;
        $validated['payment_method'] = $totalFee <= 0 ? 'free' : 'cash';
        $validated['payment_status'] = $totalFee <= 0 ? 'waived' : 'unpaid';

        $document = Document::create($validated);
        return redirect()->route('admin.documents.show',$document)->with('success','Document request created.');
    }

    public function show(Document $document) {
        $document->load('resident','issuedBy','paymentVerifiedBy');
        // Auto-transition: pending → under_review when admin first views
        if ($document->status === 'pending') {
            $document->update(['status' => 'under_review', 'viewed_at' => now()]);
            $document->refresh();
        } elseif (!$document->viewed_at) {
            $document->update(['viewed_at' => now()]);
        }
        return view('admin.documents.show', compact('document'));
    }

    /**
     * Progressive status update — context-sensitive next-step actions only.
     */
    public function updateStatus(Request $request, Document $document) {
        $action = $request->input('action');

        switch ($action) {
            case 'approve':
                if ($document->status !== 'under_review') {
                    return back()->with('error','Document must be under review before approving.');
                }
                $document->update(['status' => 'processing', 'issued_by' => auth()->id()]);
                return back()->with('success','Document approved and now being processed.');

            case 'mark_ready':
                if ($document->status !== 'processing') {
                    return back()->with('error','Document must be processing before marking as ready.');
                }
                $document->update(['status' => 'ready_for_pickup']);
                return back()->with('success','Document marked as ready for pickup.');

            case 'release':
                if (in_array($document->status, ['released','cancelled'])) {
                    return back()->with('error','Cannot release a completed or cancelled document.');
                }

                // If document has a fee and is unpaid/declined, check if staff is confirming cash collection
                if (!$document->isPaidOrWaived()) {
                    if ($request->input('confirm_cash_payment') == '1') {
                        $document->update([
                            'payment_method'      => 'cash',
                            'payment_status'      => 'verified',
                            'payment_verified_at' => now(),
                            'payment_verified_by' => auth()->id(),
                            'payment_notes'       => $request->input('payment_notes') ?: 'Cash collected on counter upon release.',
                        ]);
                    } else {
                        return back()->with('error', 'Cannot release document: Payment has not been verified yet. Please verify payment or confirm cash collection.');
                    }
                }

                $document->update([
                    'status'      => 'released',
                    'viewed_at'   => $document->viewed_at ?? now(),
                    'issued_by'   => $document->issued_by ?? auth()->id(),
                    'released_at' => now(),
                    'remarks'     => $request->input('remarks', $document->remarks),
                ]);
                return back()->with('success','Document released to the resident successfully.');

            case 'reject':
                if (in_array($document->status, ['released','cancelled'])) {
                    return back()->with('error','Cannot reject a completed or cancelled document.');
                }
                $request->validate(['rejection_reason' => 'required|string|min:5']);
                $document->update([
                    'status'           => 'cancelled',
                    'rejection_reason' => $request->rejection_reason,
                ]);
                return back()->with('success','Document request has been rejected.');

            default:
                return back()->with('error','Invalid action.');
        }
    }

    /**
     * Verify, waive, mark cash paid, or decline payment proof.
     */
    public function verifyPayment(Request $request, Document $document) {
        $action = $request->input('action');

        switch ($action) {
            case 'verify':
                $document->update([
                    'payment_status'      => 'verified',
                    'payment_verified_at' => now(),
                    'payment_verified_by' => auth()->id(),
                    'payment_notes'       => $request->input('notes', 'Payment proof verified and confirmed.'),
                ]);
                return back()->with('success', 'Payment verified successfully.');

            case 'mark_cash':
                $document->update([
                    'payment_method'      => 'cash',
                    'payment_status'      => 'verified',
                    'payment_verified_at' => now(),
                    'payment_verified_by' => auth()->id(),
                    'payment_notes'       => $request->input('notes', 'Cash payment collected on counter.'),
                ]);
                return back()->with('success', 'Cash payment confirmed and marked as verified.');

            case 'waive':
                $document->update([
                    'payment_status'      => 'waived',
                    'payment_verified_at' => now(),
                    'payment_verified_by' => auth()->id(),
                    'payment_notes'       => $request->input('notes', 'Document fee waived / exempted by authority.'),
                ]);
                return back()->with('success', 'Document processing fee waived.');

            case 'decline':
                $request->validate([
                    'payment_notes' => 'required|string|min:5',
                ], [
                    'payment_notes.required' => 'Please provide an explanation note on why the payment proof is being declined.',
                ]);

                $document->update([
                    'payment_status'      => 'declined',
                    'payment_verified_at' => now(),
                    'payment_verified_by' => auth()->id(),
                    'payment_notes'       => $request->input('payment_notes'),
                ]);

                // Send notification to resident if email is available
                if ($document->resident && !empty($document->resident->email)) {
                    try {
                        $document->resident->notify(new \App\Notifications\PaymentProofDeclinedNotification($document));
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::warning('Failed to dispatch payment proof declined email: ' . $e->getMessage());
                    }
                }

                return back()->with('success', 'Payment proof declined. Explanation note has been recorded and the resident can submit a corrected proof.');

            default:
                return back()->with('error', 'Invalid payment action.');
        }
    }

    public function update(Request $request, Document $document) {
        $document->update($request->only('remarks'));
        return back()->with('success','Remarks updated.');
    }

    public function destroy(Document $document) {
        $document->update(['status' => 'cancelled']);
        return redirect()->route('admin.documents.index')->with('success','Document cancelled.');
    }

    public function print(Document $document) {
        $document->load('resident');
        $settings = Setting::all()->pluck('value','key');
        $template = \App\Models\DocumentTemplate::getTemplateFor($document->document_type);

        $placeholders = [
            '{RESIDENT_NAME}'    => strtoupper($document->resident->full_name),
            '{CIVIL_STATUS}'     => $document->resident->civil_status,
            '{RESIDENT_ADDRESS}' => $document->resident->address . ($document->resident->purok ? ', '.$document->resident->purok : ''),
            '{PURPOSE}'          => $document->purpose,
            '{DOC_NUMBER}'       => $document->document_number,
            '{ISSUE_DATE}'       => $document->issue_date->format('F d, Y'),
            '{BARANGAY_NAME}'    => $settings['barangay_name'] ?? 'Barangay San Jose',
            '{BARANGAY_ADDRESS}' => $settings['barangay_address'] ?? 'San Pedro City, Laguna',
            '{CAPTAIN_NAME}'     => $template->signatory_name ?: ($settings['captain_name'] ?? 'Barangay Captain'),
        ];

        $renderedBody   = str_replace(array_keys($placeholders), array_values($placeholders), $template->body_template);
        $renderedHeader = str_replace(array_keys($placeholders), array_values($placeholders), $template->header_text);
        $renderedFooter = str_replace(array_keys($placeholders), array_values($placeholders), $template->footer_text);

        $logoUrl = null;
        if ($template->custom_logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($template->custom_logo)) {
            $logoUrl = public_path('storage/'.$template->custom_logo);
        } elseif ($template->show_logo && isset($settings['barangay_logo']) && file_exists(public_path('storage/'.$settings['barangay_logo']))) {
            $logoUrl = public_path('storage/'.$settings['barangay_logo']);
        } elseif ($template->show_logo && file_exists(public_path('images/logo.png'))) {
            $logoUrl = public_path('images/logo.png');
        }

        $pdf = Pdf::loadView('admin.documents.print', compact(
            'document','settings','template','renderedBody','renderedHeader','renderedFooter','logoUrl'
        ));
        return $pdf->stream("document-{$document->document_number}.pdf");
    }

    public function reissue(Document $document) {
        $new = $document->replicate();
        $year  = date('Y');
        $count = Document::whereYear('created_at',$year)->count() + 1;
        $new->document_number  = 'DOC-'.$year.'-'.str_pad($count,4,'0',STR_PAD_LEFT);
        $new->issue_date       = today();
        $new->status           = 'pending';
        $new->issued_by        = auth()->id();
        $new->viewed_at        = null;
        $new->released_at      = null;
        $new->rejection_reason = null;
        $new->save();
        return redirect()->route('admin.documents.show',$new)->with('success','Document reissued as a new request.');
    }
}
