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
        $ageBrackets = Setting::getAgeBrackets();

        return view('admin.settings.index', compact('settings', 'permissionCatalog', 'permissionsMatrix', 'managedRoles', 'ageBrackets'));
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

    public function updateAgeBrackets(Request $request) {
        $submitted = $request->input('categories', []);
        $brackets = [];

        foreach ($submitted as $cat) {
            $catName = trim($cat['category'] ?? '');
            if (!$catName) continue;

            $items = [];
            foreach ($cat['brackets'] ?? [] as $b) {
                $min = isset($b['min']) && $b['min'] !== '' ? (int)$b['min'] : 0;
                $max = isset($b['max']) && $b['max'] !== '' && $b['max'] !== null ? (int)$b['max'] : null;
                $label = trim($b['label'] ?? '');
                if (!$label) continue;

                $items[] = [
                    'min' => $min,
                    'max' => $max,
                    'label' => $label,
                ];
            }

            if (!empty($items)) {
                $brackets[] = [
                    'category' => $catName,
                    'brackets' => $items,
                ];
            }
        }

        if (!empty($brackets)) {
            Setting::setAgeBrackets($brackets);
            return back()->with('success', 'Demographic age brackets updated successfully.');
        }

        return back()->with('error', 'Please provide at least one valid age category and bracket.');
    }

    public function resetAgeBrackets() {
        Setting::setAgeBrackets(Setting::DEFAULT_AGE_BRACKETS);
        return back()->with('success', 'Demographic age brackets reset to standard defaults.');
    }

    public function uploadLogo(Request $request) {
        $request->validate(['logo' => 'required|image|max:2048']);
        $path = $request->file('logo')->store('settings', 'public');
        Setting::set('barangay_logo', $path);
        return back()->with('success', 'Logo updated successfully.');
    }
}
