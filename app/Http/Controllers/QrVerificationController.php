<?php
namespace App\Http\Controllers;
use App\Models\Resident;
use Illuminate\Http\Request;

class QrVerificationController extends Controller {
    public function index() { return view('admin.qr.index'); }
    public function verify(Request $request) {
        $request->validate(['query' => 'required|string']);
        $query = $request->input('query');
        $resident = Resident::where('id',$query)
            ->orWhere('contact_number',$query)
            ->with(['household','documents' => fn($q) => $q->latest()->limit(5)])
            ->first();
        if (!$resident) return back()->with('error','Resident not found.');
        return view('admin.qr.result', compact('resident'));
    }
}
