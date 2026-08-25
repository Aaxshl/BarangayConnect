<?php
namespace App\Http\Controllers;

use App\Models\{Resident, Household, Document, ServiceLog, CitizenRequest};
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class ReportController extends Controller {
    public function index() {
        $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();
        $dateDiffExpr = $driver === 'sqlite'
            ? 'AVG(julianday(resolved_at) - julianday(created_at)) as avg_days'
            : 'AVG(DATEDIFF(resolved_at, created_at)) as avg_days';

        return view('admin.reports.index', [
            'total_residents'   => Resident::count(),
            'male_count'        => Resident::where('gender', 'male')->count(),
            'female_count'      => Resident::where('gender', 'female')->count(),
            'minor_count'       => Resident::where('age', '<', 18)->count(),
            'senior_count'      => Resident::where('age', '>=', 60)->count(),
            'total_households'  => Household::count(),
            'total_docs'        => Document::count(),
            'docs_this_month'   => Document::whereMonth('created_at', now()->month)->count(),
            'total_requests'    => CitizenRequest::count(),
            'resolved_requests' => CitizenRequest::where('status', 'resolved')->count(),
            'pending_requests'  => CitizenRequest::whereIn('status', ['pending', 'under_review'])->count(),
            'avg_resolve_days'  => CitizenRequest::whereNotNull('resolved_at')
                ->selectRaw($dateDiffExpr)
                ->value('avg_days'),
            'doc_by_type'       => Document::selectRaw('document_type, count(*) as total')
                ->groupBy('document_type')->pluck('total', 'document_type'),
            'issues_by_type'    => CitizenRequest::selectRaw('request_type, count(*) as total')
                ->groupBy('request_type')->orderByDesc('total')->get(),
        ]);
    }

    public function residents() {
        return redirect()->route('admin.reports.index');
    }

    public function households() {
        return redirect()->route('admin.reports.index');
    }

    public function documents() {
        return redirect()->route('admin.reports.index');
    }

    public function services() {
        return redirect()->route('admin.reports.index');
    }

    /**
     * Single File Export for PDF and Excel (CSV)
     */
    public function export(Request $request, $type, $format) {
        $data = $this->getReportData($type);
        $timestamp = now()->format('Y-m-d_His');

        if ($format === 'pdf') {
            $viewName = "admin.reports.exports.{$type}";
            if (!view()->exists($viewName)) {
                return back()->with('error', "Export template for '{$type}' not found.");
            }
            $pdf = Pdf::loadView($viewName, compact('data'));
            $pdf->setPaper('a4', in_array($type, ['residents', 'requests']) ? 'landscape' : 'portrait');
            return $pdf->download("{$type}_report_{$timestamp}.pdf");
        }

        if ($format === 'excel' || $format === 'csv') {
            $filename = "{$type}_report_{$timestamp}.csv";
            $csvContent = $this->generateCsvContent($type, $data);

            return response($csvContent, 200, [
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                'Pragma'              => 'no-cache',
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
                'Expires'             => '0',
            ]);
        }

        return back()->with('error', 'Unsupported export format.');
    }

    /**
     * Batch Export Selected Reports as a ZIP Archive
     */
    public function exportZip(Request $request) {
        $reports = $request->input('reports', []);
        $format  = $request->input('format', 'both'); // 'pdf', 'excel', 'both'

        if (empty($reports) || !is_array($reports)) {
            return back()->with('error', 'Please select at least one report to include in the ZIP package.');
        }

        $timestamp   = now()->format('Y-m-d_His');
        $zipFileName = "Barangay_Reports_Archive_{$timestamp}.zip";
        $tempZipPath = storage_path('app/' . $zipFileName);

        $zip = new ZipArchive();
        if ($zip->open($tempZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Unable to create ZIP archive on the server.');
        }

        foreach ($reports as $type) {
            $data = $this->getReportData($type);
            if ($data->isEmpty()) {
                continue;
            }

            // PDF format
            if ($format === 'pdf' || $format === 'both') {
                $viewName = "admin.reports.exports.{$type}";
                if (view()->exists($viewName)) {
                    $pdf = Pdf::loadView($viewName, compact('data'));
                    $pdf->setPaper('a4', in_array($type, ['residents', 'requests']) ? 'landscape' : 'portrait');
                    $folder = $format === 'both' ? 'PDF_Reports/' : '';
                    $zip->addFromString("{$folder}{$type}_report_{$timestamp}.pdf", $pdf->output());
                }
            }

            // Excel format
            if ($format === 'excel' || $format === 'both') {
                $csvContent = $this->generateCsvContent($type, $data);
                $folder = $format === 'both' ? 'Excel_Reports/' : '';
                $zip->addFromString("{$folder}{$type}_report_{$timestamp}.csv", $csvContent);
            }
        }

        $zip->close();

        if (!file_exists($tempZipPath) || filesize($tempZipPath) === 0) {
            return back()->with('error', 'No records found to archive.');
        }

        return response()->download($tempZipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    /**
     * Retrieve Eloquent dataset by report type
     */
    protected function getReportData(string $type) {
        return match ($type) {
            'residents'  => Resident::with('household')->latest()->get(),
            'households' => Household::with('head', 'members')->latest()->get(),
            'documents'  => Document::with('resident', 'issuedBy')->latest()->get(),
            'services'   => ServiceLog::with('resident', 'assignedTo')->latest()->get(),
            'requests'   => CitizenRequest::with('resident', 'assignedTo')->latest()->get(),
            default      => collect(),
        };
    }

    /**
     * Generate CSV string with UTF-8 BOM for Microsoft Excel compatibility
     */
    protected function generateCsvContent(string $type, $data): string {
        $output = fopen('php://temp', 'r+');
        // UTF-8 BOM
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        switch ($type) {
            case 'residents':
                fputcsv($output, ['#', 'Last Name', 'First Name', 'Middle Name', 'Age', 'Gender', 'Civil Status', 'Address', 'Purok', 'Zone', 'Contact Number', 'Occupation', 'Status', 'Registered Date']);
                foreach ($data as $i => $r) {
                    fputcsv($output, [
                        $i + 1,
                        $r->last_name,
                        $r->first_name,
                        $r->middle_name,
                        $r->age,
                        ucfirst($r->gender),
                        ucfirst($r->civil_status),
                        $r->address,
                        $r->purok,
                        $r->zone,
                        $r->contact_number,
                        $r->occupation,
                        ucfirst($r->status),
                        optional($r->created_at)->format('Y-m-d H:i:s'),
                    ]);
                }
                break;

            case 'documents':
                fputcsv($output, ['#', 'Document No.', 'Document Type', 'Resident Name', 'Purpose', 'Copies', 'Date Requested', 'Status', 'Issued By', 'Released Date', 'Remarks']);
                foreach ($data as $i => $doc) {
                    fputcsv($output, [
                        $i + 1,
                        $doc->document_number,
                        Document::TYPES[$doc->document_type] ?? $doc->document_type,
                        optional($doc->resident)->full_name ?? '—',
                        $doc->purpose,
                        $doc->number_of_copies,
                        optional($doc->issue_date)->format('Y-m-d'),
                        ucwords(str_replace('_', ' ', $doc->status)),
                        optional($doc->issuedBy)->name ?? '—',
                        optional($doc->released_at)->format('Y-m-d H:i:s') ?: '—',
                        $doc->remarks,
                    ]);
                }
                break;

            case 'requests':
                fputcsv($output, ['#', 'Tracking No.', 'Issue Type', 'Resident Name', 'Location', 'Status', 'Assigned To', 'Date Submitted', 'Resolved Date', 'Resolution Notes', 'Description']);
                foreach ($data as $i => $req) {
                    fputcsv($output, [
                        $i + 1,
                        $req->tracking_number,
                        ucwords(str_replace('_', ' ', $req->request_type)),
                        optional($req->resident)->full_name ?? 'Anonymous / Walk-in',
                        $req->location,
                        ucwords(str_replace('_', ' ', $req->status)),
                        optional($req->assignedTo)->name ?? 'Unassigned',
                        optional($req->created_at)->format('Y-m-d H:i:s'),
                        optional($req->resolved_at)->format('Y-m-d H:i:s') ?: '—',
                        $req->resolution_note,
                        $req->description,
                    ]);
                }
                break;

            case 'services':
                fputcsv($output, ['#', 'Log Number', 'Service Type', 'Resident / Party', 'Date of Service', 'Status', 'Assigned Staff', 'Description', 'Resolution Notes', 'Remarks']);
                foreach ($data as $i => $log) {
                    fputcsv($output, [
                        $i + 1,
                        $log->log_number,
                        ucwords(str_replace('_', ' ', $log->service_type)),
                        optional($log->resident)->full_name ?? 'General Service',
                        optional($log->date_of_service)->format('Y-m-d'),
                        ucwords(str_replace('_', ' ', $log->status)),
                        optional($log->assignedTo)->name ?? 'Unassigned',
                        $log->description,
                        $log->resolution_notes,
                        $log->remarks,
                    ]);
                }
                break;

            case 'households':
                fputcsv($output, ['#', 'Household No.', 'Head of Household', 'Address', 'Purok', 'Zone', 'Total Members']);
                foreach ($data as $i => $hh) {
                    fputcsv($output, [
                        $i + 1,
                        $hh->household_number,
                        optional($hh->head)->full_name ?? 'Unassigned',
                        $hh->address,
                        $hh->purok,
                        $hh->zone,
                        $hh->members ? $hh->members->count() : 0,
                    ]);
                }
                break;
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }
}
