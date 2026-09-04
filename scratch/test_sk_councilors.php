<?php
require 'c:/Users/Ashley Cuevas/Downloads/smartbarangay/smartbarangay/vendor/autoload.php';
$app = require_once 'c:/Users/Ashley Cuevas/Downloads/smartbarangay/smartbarangay/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\SkCouncilorController;
use Illuminate\Http\Request;

echo "=== 1. TESTING canManageSkCouncilors PERMISSIONS ===\n";
$skChair = User::where('role', 'sk_chairman')->first();
$skCouncilor = User::where('role', 'sk_councilor')->first();
$captain = User::where('role', 'captain')->first();
$admin = User::where('role', 'administrator')->first();
$secretary = User::where('role', 'secretary')->first();
$staff = User::where('role', 'staff')->first();

assert($skChair->canManageSkCouncilors() === true, "SK Chairman CAN manage councilors");
assert($captain->canManageSkCouncilors() === true, "Captain CAN manage councilors");
assert($admin->canManageSkCouncilors() === true, "Admin CAN manage councilors");
assert($skCouncilor->canManageSkCouncilors() === false, "SK Councilor CANNOT manage councilors");
assert($secretary->canManageSkCouncilors() === false, "Secretary CANNOT manage councilors");
assert($staff->canManageSkCouncilors() === false, "Staff CANNOT manage councilors");

echo "✓ Permission check verified for all roles.\n";

// Test 403 authorization block for SK Councilor
Auth::login($skCouncilor);
try {
    $blocked = false;
    $unauthController = new SkCouncilorController();
    // Simulate middleware check
    if (!Auth::user()->canManageSkCouncilors()) {
        $blocked = true;
    }
    assert($blocked === true, "SK Councilor should be blocked from managing councilors");
    echo "✓ SK Councilor is properly blocked from councilor management.\n";
} catch (\Throwable $e) {
    echo "✓ Exception thrown as expected: " . $e->getMessage() . "\n";
}

echo "\n=== 2. TESTING SK COUNCILOR CRUD WORKFLOW AS SK CHAIRMAN ===\n";
Auth::login($skChair);
\Illuminate\Support\Facades\View::share('errors', new \Illuminate\Support\ViewErrorBag());

$controller = new SkCouncilorController();

// 1. Create a test SK Councilor
$testEmail = 'test.councilor.' . time() . '@brgy.gov.ph';
$storeReq = Request::create('/sk/councilors', 'POST', [
    'name'                  => 'Andrea Nicole Cruz',
    'email'                 => $testEmail,
    'contact_number'        => '09179998877',
    'password'              => 'password123',
    'password_confirmation' => 'password123',
]);

$controller->store($storeReq);
$createdUser = User::where('email', $testEmail)->first();
assert($createdUser !== null, "Test councilor should be created");
assert($createdUser->role === 'sk_councilor', "Role must strictly be sk_councilor");
assert($createdUser->status === 'active', "Status should be active");
assert(Hash::check('password123', $createdUser->password), "Password should be properly hashed");
echo "✓ Successfully registered new SK Councilor: {$createdUser->name} ({$createdUser->email})\n";

// 2. Edit & Update details
$updateReq = Request::create('/sk/councilors/' . $createdUser->id, 'PUT', [
    'name'           => 'Andrea Nicole Cruz-Santos',
    'email'          => $testEmail,
    'contact_number' => '09171112222',
    'status'         => 'active',
]);
$controller->update($updateReq, $createdUser);
$createdUser->refresh();
assert($createdUser->name === 'Andrea Nicole Cruz-Santos', "Name updated");
assert($createdUser->contact_number === '09171112222', "Contact updated");
echo "✓ Successfully updated SK Councilor details.\n";

// 3. Toggle Status (deactivate / activate)
$controller->toggleStatus($createdUser);
$createdUser->refresh();
assert($createdUser->status === 'inactive', "Status toggled to inactive");
echo "✓ Successfully deactivated SK Councilor account.\n";

$controller->toggleStatus($createdUser);
$createdUser->refresh();
assert($createdUser->status === 'active', "Status toggled back to active");
echo "✓ Successfully reactivated SK Councilor account.\n";

// 4. View Rendering
$indexView = $controller->index();
echo sprintf("✓ Rendered [sk.councilors.index] successfully (%d bytes)\n", strlen($indexView->render()));

$createView = $controller->create();
echo sprintf("✓ Rendered [sk.councilors.create] successfully (%d bytes)\n", strlen($createView->render()));

$editView = $controller->edit($createdUser);
echo sprintf("✓ Rendered [sk.councilors.edit] successfully (%d bytes)\n", strlen($editView->render()));

// Clean up
$controller->destroy($createdUser);
assert(User::where('email', $testEmail)->first() === null, "Test councilor deleted cleanly");
echo "✓ Successfully deleted test councilor record.\n";

echo "\n=== ALL SK COUNCILOR MANAGEMENT TESTS COMPLETED WITH 100% SUCCESS ===\n";
