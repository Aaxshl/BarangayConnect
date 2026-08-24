<?php
namespace App\Http\Controllers;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller {
    public function index() {
        $settings = Setting::all()->pluck('value','key');
        return view('admin.settings.index', compact('settings'));
    }
    public function update(Request $request) {
        $fields = [
            'barangay_name','barangay_address','contact_number','email',
            'captain_name','system_name','report_header','fiscal_year'
        ];
        foreach ($fields as $field) {
            if ($request->has($field)) Setting::set($field, $request->input($field));
        }
        // Maintenance mode toggle
        Setting::set('maintenance_mode', $request->has('maintenance_mode') ? '1' : '0');
        return back()->with('success','Settings saved.');
    }
    public function uploadLogo(Request $request) {
        $request->validate(['logo' => 'required|image|max:2048']);
        $path = $request->file('logo')->store('settings','public');
        Setting::set('barangay_logo', $path);
        return back()->with('success','Logo updated.');
    }
}
