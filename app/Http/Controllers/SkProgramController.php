<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{SkProgram, User};

class SkProgramController extends Controller {

    public function index(Request $request) {
        $query = SkProgram::with('coordinator', 'createdBy');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%")
                  ->orWhere('location', 'like', "%{$term}%");
            });
        }

        $programs = $query->latest('start_date')->paginate(12)->withQueryString();

        // Statistical summary
        $stats = [
            'total'     => SkProgram::count(),
            'active'    => SkProgram::whereIn('status', ['approved', 'ongoing'])->count(),
            'completed' => SkProgram::where('status', 'completed')->count(),
            'budget'    => SkProgram::whereIn('status', ['approved', 'ongoing', 'completed'])->sum('budget'),
        ];

        return view('sk.programs.index', compact('programs', 'stats'));
    }

    public function create() {
        $coordinators = User::whereIn('role', ['sk_chairman', 'sk_councilor', 'staff'])->where('status', 'active')->get();
        return view('sk.programs.create', compact('coordinators'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'category'            => 'required|in:' . implode(',', array_keys(SkProgram::CATEGORIES)),
            'description'         => 'required|string',
            'location'            => 'nullable|string|max:255',
            'budget'              => 'nullable|numeric|min:0',
            'target_participants' => 'nullable|integer|min:0',
            'start_date'          => 'required|date',
            'end_date'            => 'nullable|date|after_or_equal:start_date',
            'coordinator_id'      => 'nullable|exists:users,id',
            'status'              => 'nullable|in:' . implode(',', array_keys(SkProgram::STATUSES)),
        ]);

        $validated['created_by'] = auth()->id();
        $validated['budget'] = $validated['budget'] ?? 0.00;

        // Status rule: Only SK Chairman, Captain, or Admin can set status directly to approved/ongoing
        if (!empty($validated['status'])) {
            if (!in_array(auth()->user()->role, ['sk_chairman', 'captain', 'administrator']) && $validated['status'] !== 'proposed') {
                $validated['status'] = 'proposed';
            }
        } else {
            $validated['status'] = in_array(auth()->user()->role, ['sk_chairman', 'captain', 'administrator']) ? 'approved' : 'proposed';
        }

        $program = SkProgram::create($validated);

        return redirect()->route('sk.programs.show', $program)->with('success', 'SK Program has been successfully created.');
    }

    public function show(SkProgram $program) {
        $program->load('coordinator', 'createdBy');
        return view('sk.programs.show', compact('program'));
    }

    public function edit(SkProgram $program) {
        $coordinators = User::whereIn('role', ['sk_chairman', 'sk_councilor', 'staff'])->where('status', 'active')->get();
        return view('sk.programs.edit', compact('program', 'coordinators'));
    }

    public function update(Request $request, SkProgram $program) {
        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'category'            => 'required|in:' . implode(',', array_keys(SkProgram::CATEGORIES)),
            'description'         => 'required|string',
            'location'            => 'nullable|string|max:255',
            'budget'              => 'nullable|numeric|min:0',
            'target_participants' => 'nullable|integer|min:0',
            'start_date'          => 'required|date',
            'end_date'            => 'nullable|date|after_or_equal:start_date',
            'coordinator_id'      => 'nullable|exists:users,id',
            'remarks'             => 'nullable|string',
        ]);

        $program->update($validated);

        return redirect()->route('sk.programs.show', $program)->with('success', 'Program details updated successfully.');
    }

    public function updateStatus(Request $request, SkProgram $program) {
        $action = $request->input('action');
        $user = auth()->user();

        // Privilege check for approval
        if ($action === 'approve' && !in_array($user->role, ['sk_chairman', 'captain', 'administrator'])) {
            return back()->with('error', 'Only the SK Chairman or Punong Barangay can approve proposed programs.');
        }

        switch ($action) {
            case 'approve':
                $program->update(['status' => 'approved']);
                return back()->with('success', 'Program has been officially approved for implementation.');

            case 'start':
                $program->update(['status' => 'ongoing']);
                return back()->with('success', 'Program is now marked as Ongoing.');

            case 'complete':
                $program->update(['status' => 'completed']);
                return back()->with('success', 'Program has been marked as Completed.');

            case 'cancel':
                $program->update(['status' => 'cancelled']);
                return back()->with('info', 'Program has been cancelled.');

            case 'reopen':
                $program->update(['status' => 'ongoing']);
                return back()->with('success', 'Program status reverted to Ongoing.');

            default:
                return back()->with('error', 'Invalid action requested.');
        }
    }

    public function destroy(SkProgram $program) {
        $program->delete();
        return redirect()->route('sk.programs.index')->with('success', 'Program record has been deleted.');
    }
}
