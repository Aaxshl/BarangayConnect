<?php
namespace App\Http\Controllers;
use App\Models\{Household, Resident};
use Illuminate\Http\Request;

class HouseholdController extends Controller {
    public function index() {
        $households = Household::with('head')->paginate(15);
        return view('admin.households.index', compact('households'));
    }
    public function create() { return view('admin.households.create'); }
    public function store(Request $request) {
        $validated = $request->validate([
            'household_id'   => 'required|string|unique:households,household_id',
            'address'        => 'required|string',
            'purok'          => 'nullable|string',
            'zone'           => 'nullable|string',
            'contact_number' => 'nullable|string',
        ]);
        Household::create($validated);
        return redirect()->route('admin.households.index')->with('success','Household registered.');
    }
    public function show(Household $household) {
        $household->load('head','members');
        $unassignedResidents = Resident::active()->whereNull('household_id')->orderBy('last_name')->get();
        return view('admin.households.show', compact('household','unassignedResidents'));
    }
    public function edit(Household $household) {
        $residents = Resident::active()->orderBy('last_name')->get();
        return view('admin.households.edit', compact('household','residents'));
    }
    public function update(Request $request, Household $household) {
        $validated = $request->validate([
            'address'        => 'required|string',
            'purok'          => 'nullable|string',
            'zone'           => 'nullable|string',
            'contact_number' => 'nullable|string',
            'head_resident_id' => 'nullable|exists:residents,id',
        ]);
        $household->update($validated);
        return redirect()->route('admin.households.index')->with('success','Household updated.');
    }
    public function destroy(Household $household) {
        $household->update(['status' => 'inactive']);
        return redirect()->route('admin.households.index')->with('success','Household deactivated.');
    }
    public function assignResident(Request $request, Household $household) {
        $request->validate(['resident_id' => 'required|exists:residents,id']);
        Resident::find($request->resident_id)->update(['household_id' => $household->id]);
        $household->increment('number_of_members');
        return back()->with('success','Resident assigned to household.');
    }
    public function removeResident(Household $household, Resident $resident) {
        $resident->update(['household_id' => null]);
        $household->decrement('number_of_members');
        return back()->with('success','Resident removed from household.');
    }
}
