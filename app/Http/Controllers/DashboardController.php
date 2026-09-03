<?php
namespace App\Http\Controllers;

use App\Models\{Resident, Household, Document, ServiceLog, CitizenRequest, Announcement, User, Setting};
use Illuminate\Http\Request;

class DashboardController extends Controller {
    public function index() {
        $user = auth()->user();

        // Personalized time-of-day greeting
        $hour = now()->hour;
        $timeGreeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');

        $cleanName = preg_replace('/^(Hon\.|Kap\.|Sec\.|Atty\.)\s+/i', '', $user->name);
        $roleTitle = match($user->role) {
            'captain'       => 'Kap. ' . $cleanName,
            'councilor'     => 'Kagawad ' . $cleanName,
            'secretary'     => 'Secretary ' . $cleanName,
            'administrator' => 'Admin ' . $cleanName,
            'staff'         => 'Officer ' . $cleanName,
            default         => $user->name,
        };

        $banner = [
            'greeting'   => "{$timeGreeting}, {$roleTitle}!",
            'date_str'   => now()->format('l, F d, Y'),
            'role_label' => $user->role_label,
            'role'       => $user->role,
        ];

        // Gather role-tailored datasets
        if ($user->isCaptain()) {
            $data = $this->getCaptainData();
        } elseif ($user->isAdmin()) {
            $data = $this->getAdminData();
        } elseif ($user->isSecretary()) {
            $data = $this->getSecretaryData();
        } elseif ($user->role === 'councilor') {
            $data = $this->getCouncilorData();
        } elseif ($user->isStaff()) {
            $data = $this->getStaffData($user->id);
        } else {
            $data = $this->getCaptainData();
        }

        $data['banner'] = $banner;
        $data['currentRole'] = $user->role;

        return view('admin.dashboard', $data);
    }

    /**
     * Punong Barangay (Captain): Executive Command Center
     */
    private function getCaptainData(): array {
        return [
            'total_residents'  => Resident::where('status', 'active')->count(),
            'total_households' => Household::count(),
            'docs_this_month'  => Document::whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)->count(),
            'pending_requests' => CitizenRequest::whereIn('status', ['pending', 'under_review'])->count(),
            'urgent_requests'  => CitizenRequest::where('status', 'pending')
                                    ->where('created_at', '<=', now()->subDays(3))->count(),
            'active_services'  => ServiceLog::whereIn('status', ['pending', 'in_progress'])->count(),
            'recent_activity'  => $this->getRecentActivity(8),
            'doc_type_counts'  => Document::whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)
                                    ->selectRaw('document_type, count(*) as total')
                                    ->groupBy('document_type')->pluck('total', 'document_type'),
            'top_issues'       => CitizenRequest::selectRaw('request_type, count(*) as total')
                                    ->groupBy('request_type')->orderByDesc('total')->limit(5)->get(),
            'announcements'    => Announcement::published()->latest('published_at')->limit(3)->get(),
        ];
    }

    /**
     * System Administrator: IT Health, Accounts & Security
     */
    private function getAdminData(): array {
        $settings = Setting::all()->pluck('value', 'key');
        $userCounts = User::selectRaw('role, count(*) as total, sum(case when status="active" then 1 else 0 end) as active_total')
                        ->groupBy('role')->get()->keyBy('role');

        return [
            'total_users'      => User::count(),
            'active_users'     => User::where('status', 'active')->count(),
            'total_residents'  => Resident::where('status', 'active')->count(),
            'total_households' => Household::count(),
            'maintenance_mode' => ($settings['maintenance_mode'] ?? '0') === '1',
            'system_users'     => User::latest()->limit(8)->get(),
            'user_counts'      => $userCounts,
            'recent_activity'  => $this->getRecentActivity(8),
            'php_version'      => PHP_VERSION,
            'db_driver'        => strtoupper(\Illuminate\Support\Facades\DB::connection()->getDriverName()),
            'announcements'    => Announcement::latest()->limit(3)->get(),
        ];
    }

    /**
     * Barangay Secretary: Operational Worklists (Documents & Requests)
     */
    private function getSecretaryData(): array {
        return [
            'pending_docs'        => Document::where('status', 'pending')->count(),
            'under_review_docs'   => Document::where('status', 'under_review')->count(),
            'processing_docs'     => Document::where('status', 'processing')->count(),
            'ready_pickup_docs'   => Document::where('status', 'ready_for_pickup')->count(),
            'pending_requests'    => CitizenRequest::whereIn('status', ['pending', 'under_review'])->count(),
            'urgent_requests'     => CitizenRequest::where('status', 'pending')
                                        ->where('created_at', '<=', now()->subDays(3))->count(),
            'draft_announcements' => Announcement::where('status', 'draft')->count(),
            'actionable_docs'     => Document::with('resident')
                                        ->whereIn('status', ['pending', 'under_review', 'processing'])
                                        ->latest()->limit(6)->get(),
            'actionable_requests' => CitizenRequest::with('resident')
                                        ->whereIn('status', ['pending', 'under_review'])
                                        ->latest()->limit(6)->get(),
            'ready_pickup_list'   => Document::with('resident')
                                        ->where('status', 'ready_for_pickup')
                                        ->latest()->limit(5)->get(),
        ];
    }

    /**
     * Barangay Councilor (Kagawad): Legislative Oversight & Demographics
     */
    private function getCouncilorData(): array {
        $activeResidents = Resident::where('status', 'active')->get(['id', 'birthdate', 'age', 'gender']);
        $totalResidents = $activeResidents->count();
        $totalHouseholds = Household::count();

        // Gender breakdown
        $males = $activeResidents->where('gender', 'male')->count();
        $females = $activeResidents->where('gender', 'female')->count();
        $malePct = $totalResidents > 0 ? round(($males / $totalResidents) * 100, 1) : 0;
        $femalePct = $totalResidents > 0 ? round(($females / $totalResidents) * 100, 1) : 0;

        // Demographic Age Brackets calculation
        $bracketCategories = Setting::getAgeBrackets();
        $demographics = [];

        foreach ($bracketCategories as $cat) {
            $catName = $cat['category'] ?? 'Demographics';
            $catBrackets = [];
            foreach ($cat['brackets'] ?? [] as $b) {
                $min = (int) ($b['min'] ?? 0);
                $max = isset($b['max']) && $b['max'] !== '' && $b['max'] !== null ? (int)$b['max'] : null;
                $label = $b['label'] ?? '';

                $count = $activeResidents->filter(function($res) use ($min, $max) {
                    $age = $res->age;
                    if ($age === null) return false;
                    if ($max !== null) {
                        return $age >= $min && $age <= $max;
                    }
                    return $age >= $min;
                })->count();

                $pct = $totalResidents > 0 ? round(($count / $totalResidents) * 100, 1) : 0;

                $catBrackets[] = [
                    'min'        => $min,
                    'max'        => $max,
                    'label'      => $label,
                    'count'      => $count,
                    'percentage' => $pct,
                ];
            }
            $demographics[] = [
                'category' => $catName,
                'brackets' => $catBrackets,
            ];
        }

        $complaintsThisMonth = CitizenRequest::whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)->count();
        $resolvedThisMonth   = CitizenRequest::where('status', 'resolved')
                                ->whereMonth('updated_at', now()->month)
                                ->whereYear('updated_at', now()->year)->count();

        return [
            'total_residents'  => $totalResidents,
            'total_households' => $totalHouseholds,
            'complaints_month' => $complaintsThisMonth,
            'resolved_month'   => $resolvedThisMonth,
            'males'            => $males,
            'females'          => $females,
            'male_pct'         => $malePct,
            'female_pct'       => $femalePct,
            'demographics'     => $demographics,
            'top_issues'       => CitizenRequest::selectRaw('request_type, count(*) as total')
                                    ->groupBy('request_type')->orderByDesc('total')->limit(5)->get(),
            'announcements'    => Announcement::published()->latest('published_at')->limit(4)->get(),
        ];
    }

    /**
     * Barangay Staff / Tanod: Field Tasks & Personal Queue
     */
    private function getStaffData(int $userId): array {
        $myLogsQuery = ServiceLog::where('assigned_to', $userId);
        $myRequestsQuery = CitizenRequest::where('assigned_to', $userId);

        $completedThisMonth = (clone $myLogsQuery)->where('status', 'completed')
                                ->whereMonth('updated_at', now()->month)
                                ->whereYear('updated_at', now()->year)->count()
                            + (clone $myRequestsQuery)->where('status', 'resolved')
                                ->whereMonth('updated_at', now()->month)
                                ->whereYear('updated_at', now()->year)->count();

        return [
            'my_active_logs'     => (clone $myLogsQuery)->whereIn('status', ['pending', 'in_progress'])->count(),
            'my_active_requests' => (clone $myRequestsQuery)->whereIn('status', ['pending', 'under_review'])->count(),
            'completed_month'    => $completedThisMonth,
            'assigned_logs'      => (clone $myLogsQuery)->with('resident')
                                    ->whereIn('status', ['pending', 'in_progress'])
                                    ->latest()->limit(6)->get(),
            'assigned_requests'  => (clone $myRequestsQuery)->with('resident')
                                    ->whereIn('status', ['pending', 'under_review'])
                                    ->latest()->limit(6)->get(),
            'recent_completed'   => (clone $myLogsQuery)->with('resident')
                                    ->where('status', 'completed')
                                    ->latest('updated_at')->limit(4)->get(),
        ];
    }

    /**
     * Helper to collect recent activities across modules
     */
    private function getRecentActivity(int $limit = 8) {
        $activities = collect();

        Document::with('resident')->latest()->limit($limit)->get()->each(function($d) use (&$activities) {
            $resName = optional($d->resident)->full_name ?? 'Resident';
            $activities->push([
                'type' => 'document',
                'text' => "Document {$d->document_number} — {$resName}",
                'time' => $d->created_at,
                'url'  => route('admin.documents.show', $d->id),
            ]);
        });

        CitizenRequest::latest()->limit($limit)->get()->each(function($r) use (&$activities) {
            $typeLabel = ucwords(str_replace('_', ' ', $r->request_type));
            $activities->push([
                'type' => 'request',
                'text' => "Request {$r->tracking_number} ({$typeLabel}) — " . ucfirst($r->status),
                'time' => $r->updated_at ?? $r->created_at,
                'url'  => route('admin.citizen-requests.show', $r->id),
            ]);
        });

        ServiceLog::latest()->limit($limit)->get()->each(function($l) use (&$activities) {
            $activities->push([
                'type' => 'service',
                'text' => "Service Log #{$l->log_number} — {$l->service_type} (" . ucfirst($l->status) . ")",
                'time' => $l->created_at,
                'url'  => route('admin.service-logs.show', $l->id),
            ]);
        });

        return $activities->sortByDesc('time')->take($limit)->values();
    }
}
