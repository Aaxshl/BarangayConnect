<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\{Resident, Announcement, User, SkProgram};

class SkController extends Controller {

    /**
     * Sangguniang Kabataan Executive Dashboard
     */
    public function dashboard() {
        $user = Auth::user();

        // Calculate youth demographic population (15 to 24 years old)
        $youthQuery = Resident::active()->where(function($q) {
            $minDate = now()->subYears(25)->addDay()->startOfDay();
            $maxDate = now()->subYears(15)->endOfDay();
            $q->whereBetween('birthdate', [$minDate, $maxDate])
              ->orWhere(function($sq) {
                  $sq->whereNull('birthdate')->whereBetween('age', [15, 24]);
              });
        });

        $totalYouth = (clone $youthQuery)->count();

        // Sub-brackets (15-17 Teens, 18-24 Young Adults)
        $teensCount = Resident::active()->where(function($q) {
            $minDate = now()->subYears(18)->addDay()->startOfDay();
            $maxDate = now()->subYears(15)->endOfDay();
            $q->whereBetween('birthdate', [$minDate, $maxDate])
              ->orWhere(function($sq) {
                  $sq->whereNull('birthdate')->whereBetween('age', [15, 17]);
              });
        })->count();

        $youngAdultsCount = Resident::active()->where(function($q) {
            $minDate = now()->subYears(25)->addDay()->startOfDay();
            $maxDate = now()->subYears(18)->endOfDay();
            $q->whereBetween('birthdate', [$minDate, $maxDate])
              ->orWhere(function($sq) {
                  $sq->whereNull('birthdate')->whereBetween('age', [18, 24]);
              });
        })->count();

        // SK Programs metrics
        $activeProgramsCount    = SkProgram::whereIn('status', ['approved', 'ongoing'])->count();
        $proposedProgramsCount  = SkProgram::where('status', 'proposed')->count();
        $completedProgramsCount = SkProgram::where('status', 'completed')->count();
        $totalBudgetAllocated   = SkProgram::whereIn('status', ['approved', 'ongoing', 'completed'])->sum('budget');

        // Recent & upcoming items
        $recentPrograms = SkProgram::with('coordinator', 'createdBy')->latest()->limit(6)->get();
        $skAnnouncements = Announcement::sk()->latest()->limit(4)->get();

        return view('sk.dashboard', compact(
            'user',
            'totalYouth',
            'teensCount',
            'youngAdultsCount',
            'activeProgramsCount',
            'proposedProgramsCount',
            'completedProgramsCount',
            'totalBudgetAllocated',
            'recentPrograms',
            'skAnnouncements'
        ));
    }

    /**
     * Youth Residents Directory (Ages 15 to 24)
     */
    public function youthResidents(Request $request) {
        $query = Resident::active()->with('household')->where(function($q) {
            $minDate = now()->subYears(25)->addDay()->startOfDay();
            $maxDate = now()->subYears(15)->endOfDay();
            $q->whereBetween('birthdate', [$minDate, $maxDate])
              ->orWhere(function($sq) {
                  $sq->whereNull('birthdate')->whereBetween('age', [15, 24]);
              });
        });

        // Search
        if ($request->filled('search')) {
            $term = $request->input('search');
            $query->where(function($q) use ($term) {
                $q->where('first_name', 'like', "%{$term}%")
                  ->orWhere('last_name', 'like', "%{$term}%")
                  ->orWhere('contact_number', 'like', "%{$term}%")
                  ->orWhere('purok', 'like', "%{$term}%");
            });
        }

        // Sub-bracket filter
        if ($request->filled('bracket')) {
            $bracket = $request->input('bracket');
            if ($bracket === '15-17') {
                $query->where(function($q) {
                    $minDate = now()->subYears(18)->addDay()->startOfDay();
                    $maxDate = now()->subYears(15)->endOfDay();
                    $q->whereBetween('birthdate', [$minDate, $maxDate])
                      ->orWhere(function($sq) { $sq->whereNull('birthdate')->whereBetween('age', [15, 17]); });
                });
            } elseif ($bracket === '18-24') {
                $query->where(function($q) {
                    $minDate = now()->subYears(25)->addDay()->startOfDay();
                    $maxDate = now()->subYears(18)->endOfDay();
                    $q->whereBetween('birthdate', [$minDate, $maxDate])
                      ->orWhere(function($sq) { $sq->whereNull('birthdate')->whereBetween('age', [18, 24]); });
                });
            }
        }

        // Gender filter
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Purok filter
        if ($request->filled('purok')) {
            $query->where('purok', $request->purok);
        }

        $youthResidents = $query->orderBy('last_name')->paginate(15)->withQueryString();
        $puroks = Resident::active()->whereNotNull('purok')->distinct()->pluck('purok');

        return view('sk.youth-residents.index', compact('youthResidents', 'puroks'));
    }

    /**
     * Youth Resident Profile Details
     */
    public function youthResidentShow(Resident $resident) {
        $resident->load('household', 'documents', 'serviceLogs');
        return view('sk.youth-residents.show', compact('resident'));
    }
}
