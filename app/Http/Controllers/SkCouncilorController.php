<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\{User, SkProgram};

class SkCouncilorController extends Controller {

    public function __construct() {
        $this->middleware(function ($request, $next) {
            if (!auth()->user() || !auth()->user()->canManageSkCouncilors()) {
                abort(403, 'Unauthorized. Only the SK Chairman or Barangay Administrator can manage SK Councilors.');
            }
            return $next($request);
        });
    }

    /**
     * List all SK Councilors
     */
    public function index() {
        $councilors = User::where('role', 'sk_councilor')
            ->withCount(['serviceLogs as coordinated_programs_count' => function($q) {
                // Count coordinated SK programs via SkProgram
            }])
            ->latest()
            ->paginate(10);

        // Calculate count of coordinated programs for each councilor
        foreach ($councilors as $c) {
            $c->coordinated_count = SkProgram::where('coordinator_id', $c->id)->count();
        }

        return view('sk.councilors.index', compact('councilors'));
    }

    /**
     * Show form to register a new SK Councilor
     */
    public function create() {
        return view('sk.councilors.create');
    }

    /**
     * Store new SK Councilor account
     */
    public function store(Request $request) {
        $validated = $request->validate([
            'name'           => 'required|string|max:100',
            'email'          => 'required|email|max:255|unique:users,email',
            'contact_number' => 'nullable|string|max:30',
            'password'       => 'required|string|min:8|confirmed',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role']     = 'sk_councilor';
        $validated['status']   = 'active';

        User::create($validated);

        return redirect()->route('sk.councilors.index')->with('success', 'SK Councilor account has been registered successfully.');
    }

    /**
     * Show form to edit an SK Councilor
     */
    public function edit(User $councilor) {
        if ($councilor->role !== 'sk_councilor') {
            abort(404, 'User is not an SK Councilor.');
        }

        return view('sk.councilors.edit', compact('councilor'));
    }

    /**
     * Update SK Councilor details
     */
    public function update(Request $request, User $councilor) {
        if ($councilor->role !== 'sk_councilor') {
            abort(404, 'User is not an SK Councilor.');
        }

        $validated = $request->validate([
            'name'           => 'required|string|max:100',
            'email'          => 'required|email|max:255|unique:users,email,' . $councilor->id,
            'contact_number' => 'nullable|string|max:30',
            'status'         => 'required|in:active,inactive',
            'password'       => 'nullable|string|min:8|confirmed',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $councilor->update($validated);

        return redirect()->route('sk.councilors.index')->with('success', 'SK Councilor details updated successfully.');
    }

    /**
     * Toggle active/inactive status
     */
    public function toggleStatus(User $councilor) {
        if ($councilor->role !== 'sk_councilor') {
            abort(404, 'User is not an SK Councilor.');
        }

        $newStatus = $councilor->status === 'active' ? 'inactive' : 'active';
        $councilor->update(['status' => $newStatus]);

        $label = $newStatus === 'active' ? 'activated' : 'deactivated';
        return back()->with('success', "SK Councilor {$councilor->name} has been {$label}.");
    }

    /**
     * Delete an SK Councilor account
     */
    public function destroy(User $councilor) {
        if ($councilor->role !== 'sk_councilor') {
            abort(404, 'User is not an SK Councilor.');
        }

        $name = $councilor->name;
        $councilor->delete();

        return redirect()->route('sk.councilors.index')->with('success', "SK Councilor {$name} has been removed from the council.");
    }
}
