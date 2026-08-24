<?php
namespace App\Http\Controllers;
use App\Models\{Resident, Household, Document, ServiceLog, CitizenRequest, Announcement};

class DashboardController extends Controller {
    public function index() {
        $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();
        $monthExpr = $driver === 'sqlite' ? "cast(strftime('%m', created_at) as integer) as month" : "MONTH(created_at) as month";

        $data = [
            'total_residents'  => Resident::where('status','active')->count(),
            'total_households' => Household::count(),
            'docs_this_month'  => Document::whereMonth('created_at', now()->month)->count(),
            'pending_requests' => CitizenRequest::whereIn('status',['pending','under_review'])->count(),
            'urgent_requests'  => CitizenRequest::where('status','pending')
                                    ->where('created_at','<=', now()->subDays(3))->count(),
            'recent_activity'  => $this->getRecentActivity(),
            'doc_type_counts'  => Document::whereMonth('created_at', now()->month)
                                    ->selectRaw('document_type, count(*) as total')
                                    ->groupBy('document_type')->pluck('total','document_type'),
            'top_issues'       => CitizenRequest::selectRaw('request_type, count(*) as total')
                                    ->groupBy('request_type')->orderByDesc('total')->limit(5)->get(),
            'monthly_requests' => CitizenRequest::selectRaw("{$monthExpr}, count(*) as total")
                                    ->whereYear('created_at', now()->year)
                                    ->groupBy('month')->orderBy('month')->get(),
            'announcements'    => Announcement::where('status','published')->latest()->limit(3)->get(),
        ];
        return view('admin.dashboard', $data);
    }

    private function getRecentActivity() {
        $activities = collect();
        Document::with('resident')->latest()->limit(5)->get()->each(function($d) use (&$activities) {
            $activities->push(['type'=>'document','text'=>"Document issued — {$d->resident->full_name}",'time'=>$d->created_at]);
        });
        CitizenRequest::latest()->limit(5)->get()->each(function($r) use (&$activities) {
            $activities->push(['type'=>'request','text'=>"Request {$r->tracking_number} — {$r->status}",'time'=>$r->updated_at]);
        });
        ServiceLog::latest()->limit(5)->get()->each(function($l) use (&$activities) {
            $activities->push(['type'=>'service','text'=>"Service log #{$l->log_number} — {$l->service_type}",'time'=>$l->created_at]);
        });
        return $activities->sortByDesc('time')->take(10)->values();
    }
}
