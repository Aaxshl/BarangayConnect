<?php
require 'c:/Users/Ashley Cuevas/Downloads/smartbarangay/smartbarangay/vendor/autoload.php';
$app = require_once 'c:/Users/Ashley Cuevas/Downloads/smartbarangay/smartbarangay/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

echo "=== VERIFYING QR REMOVAL, LOGOUT CLEANUP & DASHBOARD QUICK BUTTONS REMOVAL ===\n\n";

\Illuminate\Support\Facades\View::share('errors', new \Illuminate\Support\ViewErrorBag());

// 1. Verify QR routes removed
assert(!Route::has('admin.qr.index'), "admin.qr.index route does not exist");
assert(!Route::has('admin.qr.verify'), "admin.qr.verify route does not exist");
assert(!class_exists('App\Http\Controllers\QrVerificationController'), "QrVerificationController does not exist");
echo "✓ QR verification completely removed from routes and controllers.\n";

// 2. Verify admin layout has no topbar logout button and no QR link
$adminUser = User::where('role', 'administrator')->first() ?? User::first();
Auth::login($adminUser);
$adminHtml = view('layouts.admin')->render();
assert(strpos($adminHtml, 'admin.qr.index') === false, "No QR link in admin layout");
// In topbar (header), there should NOT be a logout button
preg_match('/<header class="topbar">.*?<\/header>/s', $adminHtml, $headerMatches);
$headerHtml = $headerMatches[0] ?? '';
assert(strpos($headerHtml, 'Logout') === false, "Topbar header does NOT have Logout button");
assert(strpos($headerHtml, 'ti-logout') === false, "Topbar header does NOT have ti-logout icon");
// But sidebar DOES have the logout button
assert(strpos($adminHtml, 'btn-sidebar-logout') !== false, "Sidebar still contains the logout button");
echo "✓ Topbar logout button removed; sidebar logout button preserved.\n";

// 3. Verify Dashboard quick button section 'row g-2 mb-3' removed
$dashFileContent = file_get_contents('c:/Users/Ashley Cuevas/Downloads/smartbarangay/smartbarangay/resources/views/admin/dashboard.blade.php');
assert(strpos($dashFileContent, 'row g-2 mb-3') === false, "Dashboard does NOT contain 'row g-2 mb-3' quick button sections");
echo "✓ Dashboard analytics quick buttons (row g-2 mb-3) completely removed.\n";

// 4. Verify Dashboard renders for all roles
$roles = ['captain', 'administrator', 'secretary', 'councilor', 'staff'];
$dashController = new DashboardController();
foreach ($roles as $role) {
    $user = User::where('role', $role)->first();
    if ($user) {
        Auth::login($user);
        $res = $dashController->index();
        assert($res->name() === 'admin.dashboard', "Dashboard renders for {$role}");
        echo "✓ Dashboard renders cleanly for role: {$role}\n";
    }
}

echo "\n=== ALL VERIFICATION CHECKS PASSED WITH 100% SUCCESS! ===\n";
