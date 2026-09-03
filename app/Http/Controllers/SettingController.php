<?php
namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller {
    public function index() {
        $settings = Setting::all()->pluck('value', 'key');
        $permissionCatalog = Setting::PERMISSIONS;
        $permissionsMatrix = Setting::getPermissionsMatrix();
        $managedRoles = Setting::MANAGED_ROLES;

        return view('admin.settings.index', compact('settings', 'permissionCatalog', 'permissionsMatrix', 'managedRoles'));
    }

    public function update(Request $request) {
        $fields = [
            'barangay_name', 'barangay_address', 'contact_number', 'email',
            'captain_name', 'system_name', 'report_header', 'fiscal_year'
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                Setting::set($field, $request->input($field));
            }
        }

        // Maintenance mode toggle — ONLY System Administrator can toggle maintenance mode (Barangay Captain cannot)
        if (auth()->user()->isAdmin()) {
            Setting::set('maintenance_mode', $request->has('maintenance_mode') ? '1' : '0');
        }

        return back()->with('success', 'Barangay settings updated successfully.');
    }

    public function updatePermissions(Request $request) {
        $submitted = $request->input('permissions', []);
        $managedRoles = array_keys(Setting::MANAGED_ROLES);
        
        $newMatrix = [];
        foreach ($managedRoles as $role) {
            $newMatrix[$role] = [];
            foreach (Setting::PERMISSIONS as $category => $perms) {
                foreach ($perms as $permKey => $label) {
                    $newMatrix[$role][$permKey] = !empty($submitted[$role][$permKey]);
                }
            }
        }

        Setting::setPermissionsMatrix($newMatrix);

        return back()->with('success', 'Role permissions updated successfully.');
    }

    public function resetPermissions() {
        Setting::setPermissionsMatrix(Setting::DEFAULT_ROLE_PERMISSIONS);
        return back()->with('success', 'Role permissions reset to default configuration.');
    }

    public function uploadLogo(Request $request) {
        $request->validate(['logo' => 'required|image|max:2048']);
        $path = $request->file('logo')->store('settings', 'public');
        Setting::set('barangay_logo', $path);
        return back()->with('success', 'Logo updated successfully.');
    }
}
