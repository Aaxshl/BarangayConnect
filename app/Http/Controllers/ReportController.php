<?php
namespace App\Http\Controllers;

use App\Models\{Resident, Household, Document, ServiceLog, CitizenRequest, User};
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller {
    public function index() {
        $driver = DB::connection()->getDriverName();
        $dateDiffExpr = $driver === 'sqlite'
            ? 'AVG(julianday(resolved_at) - julianday(created_at)) as avg_days'
            : 'AVG(DATEDIFF(resolved_at, created_at)) as avg_days';

        // ── Processing Time Analytics ──
        $procDiffExpr = $driver === 'sqlite'
            ? 'AVG(julianday(released_at) - julianday(issue_date)) as avg_days'
            : 'AVG(DATEDIFF(released_at, issue_date)) as avg_days';

        $procByTypeDiffExpr = $driver === 'sqlite'
            ? 'document_type, AVG(julianday(released_at) - julianday(issue_date)) as avg_days, COUNT(*) as total'
            : 'document_type, AVG(DATEDIFF(released_at, issue_date)) as avg_days, COUNT(*) as total';

        // ── Revenue Queries ──
        $paidStatuses = ['verified', 'waived'];

        $revenueTotal = Document::whereIn('payment_status', $paidStatuses)
            ->where('fee', '>', 0)->sum('fee');

        $revenueCash = Document::where('payment_status', 'verified')
            ->where('payment_method', 'cash')->where('fee', '>', 0)->sum('fee');

        $revenueGcash = Document::where('payment_status', 'verified')
            ->where('payment_method', 'gcash')->where('fee', '>', 0)->sum('fee');

        $revenueThisMonth = Document::whereIn('payment_status', $paidStatuses)
            ->where('fee', '>', 0)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('fee');

        $revenueLastMonth = Document::whereIn('payment_status', $paidStatuses)
            ->where('fee', '>', 0)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('fee');

        $waivedTotal = Document::where('payment_status', 'waived')
            ->where('fee', '>', 0)->sum('fee');

        $pendingPayments = Document::where('payment_status', 'pending_verification')->count();
        $unpaidDocuments = Document::where('payment_status', 'unpaid')
            ->where('fee', '>', 0)->count();

        // Revenue by document type
        $revenueByType = Document::whereIn('payment_status', $paidStatuses)
            ->where('fee', '>', 0)
            ->selectRaw('document_type, SUM(fee) as total_revenue, 
                SUM(CASE WHEN payment_method = \'cash\' AND payment_status = \'verified\' THEN fee ELSE 0 END) as cash_revenue,
                SUM(CASE WHEN payment_method = \'gcash\' AND payment_status = \'verified\' THEN fee ELSE 0 END) as gcash_revenue,
                SUM(CASE WHEN payment_status = \'waived\' THEN fee ELSE 0 END) as waived_revenue,
                COUNT(*) as doc_count')
            ->groupBy('document_type')
            ->get();

        // Monthly revenue trend (last 12 months)
        $revenueByMonth = collect();
        for ($i = 11; $i >= 0; $i--) {
            $monthDate = now()->subMonths($i);
            $monthLabel = $monthDate->format('M Y');
            $monthCash = Document::where('payment_status', 'verified')
                ->where('payment_method', 'cash')
                ->where('fee', '>', 0)
                ->whereMonth('created_at', $monthDate->month)
                ->whereYear('created_at', $monthDate->year)
                ->sum('fee');
            $monthGcash = Document::where('payment_status', 'verified')
                ->where('payment_method', 'gcash')
                ->where('fee', '>', 0)
                ->whereMonth('created_at', $monthDate->month)
                ->whereYear('created_at', $monthDate->year)
                ->sum('fee');
            $monthWaived = Document::where('payment_status', 'waived')
                ->where('fee', '>', 0)
                ->whereMonth('created_at', $monthDate->month)
                ->whereYear('created_at', $monthDate->year)
                ->sum('fee');
            $revenueByMonth->push([
                'label' => $monthLabel,
                'short' => $monthDate->format('M'),
                'cash'  => (float) $monthCash,
                'gcash' => (float) $monthGcash,
                'waived' => (float) $monthWaived,
                'total' => (float) $monthCash + (float) $monthGcash + (float) $monthWaived,
            ]);
        }
        $maxMonthlyRevenue = $revenueByMonth->max('total') ?: 1;

        // Processing time analytics
        $avgProcessingDays = Document::where('status', 'released')
            ->whereNotNull('released_at')
            ->selectRaw($procDiffExpr)
            ->value('avg_days');

        $processingByType = Document::where('status', 'released')
            ->whereNotNull('released_at')
            ->selectRaw($procByTypeDiffExpr)
            ->groupBy('document_type')
            ->get();

        // Staff productivity
        $staffProductivity = User::whereIn('role', ['captain', 'administrator', 'secretary', 'staff'])
            ->where('is_active', true)
            ->get()
            ->map(function ($user) use ($driver) {
                $procDiff = $driver === 'sqlite'
                    ? 'AVG(julianday(released_at) - julianday(issue_date)) as avg_days'
                    : 'AVG(DATEDIFF(released_at, issue_date)) as avg_days';

                $processed = Document::where('issued_by', $user->id)->count();
                $released = Document::where('issued_by', $user->id)
                    ->where('status', 'released')->count();
                $avgDays = Document::where('issued_by', $user->id)
                    ->where('status', 'released')
                    ->whereNotNull('released_at')
                    ->selectRaw($procDiff)
                    ->value('avg_days');
                $paymentsVerified = Document::where('payment_verified_by', $user->id)
                    ->whereIn('payment_status', ['verified', 'waived'])->count();

                return [
                    'name'              => $user->name,
                    'role'              => $user->role_label ?? ucfirst($user->role),
                    'processed'         => $processed,
                    'released'          => $released,
                    'avg_days'          => $avgDays ? round($avgDays, 1) : null,
                    'payments_verified' => $paymentsVerified,
                ];
            })
            ->filter(fn($s) => $s['processed'] > 0 || $s['payments_verified'] > 0)
            ->sortByDesc('processed')
            ->values();

        return view('admin.reports.index', [
            'total_residents'     => Resident::count(),
            'male_count'          => Resident::where('gender', 'male')->count(),
            'female_count'        => Resident::where('gender', 'female')->count(),
            'minor_count'         => Resident::where('age', '<', 18)->count(),
            'senior_count'        => Resident::where('age', '>=', 60)->count(),
            'total_households'    => Household::count(),
            'total_docs'          => Document::count(),
            'docs_this_month'     => Document::whereMonth('created_at', now()->month)->count(),
            'total_requests'      => CitizenRequest::count(),
            'resolved_requests'   => CitizenRequest::where('status', 'resolved')->count(),
            'pending_requests'    => CitizenRequest::whereIn('status', ['pending', 'under_review'])->count(),
            'avg_resolve_days'    => CitizenRequest::whereNotNull('resolved_at')
                ->selectRaw($dateDiffExpr)->value('avg_days'),
            'doc_by_type'         => Document::selectRaw('document_type, count(*) as total')
                ->groupBy('document_type')->pluck('total', 'document_type'),
            'issues_by_type'      => CitizenRequest::selectRaw('request_type, count(*) as total')
                ->groupBy('request_type')->orderByDesc('total')->get(),
            // Revenue data
            'revenue_total'       => (float) $revenueTotal,
            'revenue_cash'        => (float) $revenueCash,
            'revenue_gcash'       => (float) $revenueGcash,
            'revenue_this_month'  => (float) $revenueThisMonth,
            'revenue_last_month'  => (float) $revenueLastMonth,
            'waived_total'        => (float) $waivedTotal,
            'pending_payments'    => $pendingPayments,
            'unpaid_documents'    => $unpaidDocuments,
            'revenue_by_type'     => $revenueByType,
            'revenue_by_month'    => $revenueByMonth,
            'max_monthly_revenue' => $maxMonthlyRevenue,
            // Processing time
            'avg_processing_days' => $avgProcessingDays ? round($avgProcessingDays, 1) : null,
            'processing_by_type'  => $processingByType,
            // Staff productivity
            'staff_productivity'  => $staffProductivity,
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
     * Export Revenue Report (PDF or CSV) with optional date range filter
     */
    public function exportRevenue(Request $request) {
        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');
        $docType  = $request->input('document_type');
        $format   = $request->input('format', 'pdf');

        $query = Document::with('resident', 'issuedBy', 'paymentVerifiedBy')
            ->whereIn('payment_status', ['verified', 'waived'])
            ->where('fee', '>', 0);

        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom . ' 00:00:00');
        }
        if ($dateTo) {
            $query->where('created_at', '<=', $dateTo . ' 23:59:59');
        }
        if ($docType) {
            $query->where('document_type', $docType);
        }

        $data = $query->latest()->get();
        $timestamp = now()->format('Y-m-d_His');

        // Calculate summary for PDF
        $summary = [
            'total'     => $data->sum('fee'),
            'cash'      => $data->where('payment_method', 'cash')->where('payment_status', 'verified')->sum('fee'),
            'gcash'     => $data->where('payment_method', 'gcash')->where('payment_status', 'verified')->sum('fee'),
            'waived'    => $data->where('payment_status', 'waived')->sum('fee'),
            'count'     => $data->count(),
            'date_from' => $dateFrom,
            'date_to'   => $dateTo,
        ];

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('admin.reports.exports.revenue', compact('data', 'summary'));
            $pdf->setPaper('a4', 'landscape');
            return $pdf->download("revenue_report_{$timestamp}.pdf");
        }

        // CSV export
        $output = fopen('php://temp', 'r+');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($output, ['#', 'Document No.', 'Document Type', 'Resident Name', 'Fee (₱)', 'Payment Method', 'Payment Status', 'Reference No.', 'Date Requested', 'Date Released', 'Verified By']);

        foreach ($data as $i => $doc) {
            fputcsv($output, [
                $i + 1,
                $doc->document_number,
                Document::TYPES[$doc->document_type] ?? $doc->document_type,
                optional($doc->resident)->full_name ?? '—',
                number_format($doc->fee, 2),
                Document::PAYMENT_METHODS[$doc->payment_method] ?? ucfirst($doc->payment_method),
                ucwords(str_replace('_', ' ', $doc->payment_status)),
                $doc->payment_reference ?: '—',
                optional($doc->issue_date)->format('Y-m-d'),
                optional($doc->released_at)->format('Y-m-d H:i:s') ?: '—',
                optional($doc->paymentVerifiedBy)->name ?? '—',
            ]);
        }

        // Summary rows
        fputcsv($output, []);
        fputcsv($output, ['', '', '', 'TOTAL COLLECTED', number_format($summary['total'], 2)]);
        fputcsv($output, ['', '', '', 'Cash Payments', number_format($summary['cash'], 2)]);
        fputcsv($output, ['', '', '', 'GCash Payments', number_format($summary['gcash'], 2)]);
        fputcsv($output, ['', '', '', 'Waived Fees', number_format($summary['waived'], 2)]);

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        $filename = "revenue_report_{$timestamp}.csv";
        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ]);
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
            // Special handling for revenue report
            if ($type === 'revenue') {
                $revenueData = Document::with('resident', 'issuedBy', 'paymentVerifiedBy')
                    ->whereIn('payment_status', ['verified', 'waived'])
                    ->where('fee', '>', 0)
                    ->latest()->get();

                $summary = [
                    'total'     => $revenueData->sum('fee'),
                    'cash'      => $revenueData->where('payment_method', 'cash')->where('payment_status', 'verified')->sum('fee'),
                    'gcash'     => $revenueData->where('payment_method', 'gcash')->where('payment_status', 'verified')->sum('fee'),
                    'waived'    => $revenueData->where('payment_status', 'waived')->sum('fee'),
                    'count'     => $revenueData->count(),
                    'date_from' => null,
                    'date_to'   => null,
                ];

                if (($format === 'pdf' || $format === 'both') && view()->exists('admin.reports.exports.revenue')) {
                    $pdf = Pdf::loadView('admin.reports.exports.revenue', ['data' => $revenueData, 'summary' => $summary]);
                    $pdf->setPaper('a4', 'landscape');
                    $folder = $format === 'both' ? 'PDF_Reports/' : '';
                    $zip->addFromString("{$folder}revenue_report_{$timestamp}.pdf", $pdf->output());
                }

                if ($format === 'excel' || $format === 'both') {
                    $csvContent = $this->generateRevenueCsv($revenueData, $summary);
                    $folder = $format === 'both' ? 'Excel_Reports/' : '';
                    $zip->addFromString("{$folder}revenue_report_{$timestamp}.csv", $csvContent);
                }
                continue;
            }

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
     * Generate revenue CSV content
     */
    protected function generateRevenueCsv($data, $summary): string {
        $output = fopen('php://temp', 'r+');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($output, ['#', 'Document No.', 'Document Type', 'Resident Name', 'Fee (PHP)', 'Payment Method', 'Payment Status', 'Reference No.', 'Date Requested', 'Date Released', 'Verified By']);

        foreach ($data as $i => $doc) {
            fputcsv($output, [
                $i + 1,
                $doc->document_number,
                Document::TYPES[$doc->document_type] ?? $doc->document_type,
                optional($doc->resident)->full_name ?? '—',
                number_format($doc->fee, 2),
                Document::PAYMENT_METHODS[$doc->payment_method] ?? ucfirst($doc->payment_method),
                ucwords(str_replace('_', ' ', $doc->payment_status)),
                $doc->payment_reference ?: '—',
                optional($doc->issue_date)->format('Y-m-d'),
                optional($doc->released_at)->format('Y-m-d H:i:s') ?: '—',
                optional($doc->paymentVerifiedBy)->name ?? '—',
            ]);
        }

        fputcsv($output, []);
        fputcsv($output, ['', '', '', 'TOTAL COLLECTED', number_format($summary['total'], 2)]);
        fputcsv($output, ['', '', '', 'Cash Payments', number_format($summary['cash'], 2)]);
        fputcsv($output, ['', '', '', 'GCash Payments', number_format($summary['gcash'], 2)]);
        fputcsv($output, ['', '', '', 'Waived Fees', number_format($summary['waived'], 2)]);

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        return $csv;
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
                fputcsv($output, ['#', 'Document No.', 'Document Type', 'Resident Name', 'Purpose', 'Copies', 'Fee (PHP)', 'Payment Method', 'Payment Status', 'Date Requested', 'Status', 'Issued By', 'Released Date', 'Remarks']);
                foreach ($data as $i => $doc) {
                    fputcsv($output, [
                        $i + 1,
                        $doc->document_number,
                        Document::TYPES[$doc->document_type] ?? $doc->document_type,
                        optional($doc->resident)->full_name ?? '—',
                        $doc->purpose,
                        $doc->number_of_copies,
                        number_format($doc->fee, 2),
                        Document::PAYMENT_METHODS[$doc->payment_method] ?? ucfirst($doc->payment_method),
                        ucwords(str_replace('_', ' ', $doc->payment_status)),
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
