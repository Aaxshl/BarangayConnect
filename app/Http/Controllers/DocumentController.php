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
        $validated['document_number'] = 'DOC-'.$year.'-'.str_pad($count,4,'0',STR_PAD_LEFT);
        $validated['issue_date'] = today();
        $validated['status']     = 'pending';
        $validated['issued_by']  = auth()->id();
        $document = Document::create($validated);
        return redirect()->route('admin.documents.show',$document)->with('success','Document request created.');
    }

    public function show(Document $document) {
        $document->load('resident','issuedBy');
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
                if ($document->status !== 'ready_for_pickup') {
                    return back()->with('error','Document must be ready for pickup before releasing.');
                }
                $document->update([
                    'status'      => 'released',
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
