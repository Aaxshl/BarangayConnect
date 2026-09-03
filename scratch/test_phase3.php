<?php
require 'c:/Users/Ashley Cuevas/Downloads/smartbarangay/smartbarangay/vendor/autoload.php';
$app = require_once 'c:/Users/Ashley Cuevas/Downloads/smartbarangay/smartbarangay/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingController;

echo "=== 1. TESTING DEMOGRAPHIC AGE BRACKETS IN SETTING ===\n";
$brackets = Setting::getAgeBrackets();
assert(is_array($brackets), "Brackets should be an array");
assert(count($brackets) === 3, "There should be 3 main demographic categories (Childhood & Youth, Adult Demographics, Seniors)");
echo "✓ Default age brackets retrieved successfully (" . count($brackets) . " categories):\n";
foreach ($brackets as $c) {
    echo "   - Category: " . $c['category'] . " (" . count($c['brackets']) . " brackets)\n";
}

// Test setting and resetting
$customBrackets = $brackets;
$customBrackets[0]['category'] = 'Young Filipinos';
Setting::setAgeBrackets($customBrackets);
assert(Setting::getAgeBrackets()[0]['category'] === 'Young Filipinos', "Custom bracket category saved");
Setting::setAgeBrackets(Setting::DEFAULT_AGE_BRACKETS);
assert(Setting::getAgeBrackets()[0]['category'] === 'Childhood & Youth', "Reset to default worked");
echo "✓ Age bracket customization and reset verified.\n";

echo "\n=== 2. TESTING DASHBOARD CONTROLLER PER ROLE ===\n";
$rolesToTest = [
    'captain'       => 'Hon. Ricardo Santos',
    'administrator' => 'Joven Reyes',
    'secretary'     => 'Leny Aguilar',
    'councilor'     => 'Hon. Teresa Mendoza',
    'staff'         => 'Marco Villanueva',
];

$controller = new DashboardController();

foreach ($rolesToTest as $role => $expectedName) {
    $user = User::where('role', $role)->first();
    assert($user !== null, "User for role {$role} should exist in database");
    Auth::login($user);

    $viewResponse = $controller->index();
    $data = $viewResponse->getData();

    assert(isset($data['banner']), "Banner should exist for {$role}");
    $lastName = last(explode(' ', $expectedName));
    assert(str_contains($data['banner']['greeting'], $lastName), "Greeting should contain user name ({$lastName})");
    assert($data['currentRole'] === $role, "Current role should match");

    // Check role-specific keys
    if ($role === 'captain') {
        assert(isset($data['total_residents']), "Captain data must have total_residents");
        assert(isset($data['docs_this_month']), "Captain data must have docs_this_month");
        assert(isset($data['active_services']), "Captain data must have active_services");
    } elseif ($role === 'administrator') {
        assert(isset($data['total_users']), "Admin data must have total_users");
        assert(isset($data['user_counts']), "Admin data must have user_counts");
        assert(isset($data['php_version']), "Admin data must have php_version");
    } elseif ($role === 'secretary') {
        assert(isset($data['pending_docs']), "Secretary data must have pending_docs");
        assert(isset($data['actionable_docs']), "Secretary data must have actionable_docs");
    } elseif ($role === 'councilor') {
        assert(isset($data['demographics']), "Councilor data must have demographics");
        assert(isset($data['male_pct']), "Councilor data must have male_pct");
        assert(count($data['demographics']) === 3, "Councilor demographics must have 3 categories");
    } elseif ($role === 'staff') {
        assert(isset($data['my_active_logs']), "Staff data must have my_active_logs");
        assert(isset($data['assigned_logs']), "Staff data must have assigned_logs");
    }

    // Render view to ensure no Blade syntax / compilation error
    $renderedHtml = $viewResponse->render();
    echo sprintf("✓ Role [%s] dashboard rendered successfully (%d bytes, banner: '%s')\n", $role, strlen($renderedHtml), $data['banner']['greeting']);
}

echo "\n=== 3. TESTING SETTINGS VIEW WITH AGE BRACKETS TAB ===\n";
$adminUser = User::where('role', 'administrator')->first();
Auth::login($adminUser);
$settingController = new SettingController();
$settingsView = $settingController->index();
$renderedSettings = $settingsView->render();
assert(str_contains($renderedSettings, 'Demographics &amp; Age Brackets') || str_contains($renderedSettings, 'Demographic Age Brackets'), "Settings view contains Age Brackets tab");
echo sprintf("✓ Settings view rendered successfully with Demographics tab (%d bytes)\n", strlen($renderedSettings));

echo "\n=== ALL PHASE 3 TESTS COMPLETED WITH 100% SUCCESS ===\n";
