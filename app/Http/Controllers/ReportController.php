<?php
namespace App\Http\Controllers;
use App\Models\{Resident, Household, Document, ServiceLog, CitizenRequest};
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller {
    public function index() {
        $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();
        $dateDiffExpr = $driver === 'sqlite'
            ? 'AVG(julianday(resolved_at) - julianday(created_at)) as avg_days'
            : 'AVG(DATEDIFF(resolved_at, created_at)) as avg_days';

        return view('admin.reports.index', [
            'total_residents'   => Resident::count(),
            'male_count'        => Resident::where('gender','male')->count(),
            'female_count'      => Resident::where('gender','female')->count(),
            'minor_count'       => Resident::where('age','<',18)->count(),
            'senior_count'      => Resident::where('age','>=',60)->count(),
            'total_households'  => Household::count(),
            'total_docs'        => Document::count(),
            'docs_this_month'   => Document::whereMonth('created_at',now()->month)->count(),
            'total_requests'    => CitizenRequest::count(),
            'resolved_requests' => CitizenRequest::where('status','resolved')->count(),
            'pending_requests'  => CitizenRequest::whereIn('status',['pending','under_review'])->count(),
            'avg_resolve_days'  => CitizenRequest::whereNotNull('resolved_at')
                ->selectRaw($dateDiffExpr)
                ->value('avg_days'),
            'doc_by_type'       => Document::selectRaw('document_type, count(*) as total')
                ->groupBy('document_type')->pluck('total','document_type'),
            'issues_by_type'    => CitizenRequest::selectRaw('request_type, count(*) as total')
                ->groupBy('request_type')->orderByDesc('total')->get(),
        ]);
    }
    public function export(Request $request, $type, $format) {
        $data = match($type) {
            'residents'  => Resident::with('household')->get(),
            'households' => Household::with('head','members')->get(),
            'documents'  => Document::with('resident','issuedBy')->get(),
            'services'   => ServiceLog::with('resident','assignedTo')->get(),
            'requests'   => CitizenRequest::with('resident','assignedTo')->get(),
            default      => collect(),
        };
        if ($format === 'pdf') {
            $pdf = Pdf::loadView("admin.reports.exports.{$type}", compact('data'));
            return $pdf->download("{$type}-report.pdf");
        }
        // Excel export would use Maatwebsite\Excel here
        return back()->with('info','Export generated.');
    }
}
