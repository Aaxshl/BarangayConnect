<?php
require 'c:/Users/Ashley Cuevas/Downloads/smartbarangay/smartbarangay/vendor/autoload.php';
$app = require_once 'c:/Users/Ashley Cuevas/Downloads/smartbarangay/smartbarangay/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Resident;
use App\Models\SkProgram;
use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SkController;
use App\Http\Controllers\SkProgramController;
use App\Http\Controllers\SkAnnouncementController;
use Illuminate\Http\Request;

echo "=== 1. TESTING SK USER ROLES & PERMISSIONS ===\n";
$skChair = User::where('role', 'sk_chairman')->first();
$skCouncilor = User::where('role', 'sk_councilor')->first();
$captain = User::where('role', 'captain')->first();

assert($skChair !== null, "SK Chairman should exist");
assert($skCouncilor !== null, "SK Councilor should exist");
assert($skChair->canAccessSkPortal() === true, "SK Chairman can access SK portal");
assert($skCouncilor->canAccessSkPortal() === true, "SK Councilor can access SK portal");
assert($captain->canAccessSkPortal() === true, "Captain can access SK portal");

echo "✓ SK Role authentication verified for SK Chairman, SK Councilor, and Captain.\n";

echo "\n=== 2. TESTING YOUTH POPULATION & DEMOGRAPHICS ===\n";
$youthQuery = Resident::active()->where(function($q) {
    $minDate = now()->subYears(31)->addDay()->startOfDay();
    $maxDate = now()->subYears(15)->endOfDay();
    $q->whereBetween('birthdate', [$minDate, $maxDate])
      ->orWhere(function($sq) { $sq->whereNull('birthdate')->whereBetween('age', [15, 30]); });
});
$totalYouth = $youthQuery->count();
assert($totalYouth >= 3, "There should be at least 3 youth residents in the database");
echo "✓ Total Youth Residents (15–30 yrs) found: {$totalYouth}\n";

echo "\n=== 3. TESTING SK PROGRAMS & LIFECYCLE ===\n";
$program = SkProgram::first();
assert($program !== null, "At least one SK program should exist from seeder");
assert($program->coordinator !== null || $program->createdBy !== null, "Program relationships work");
echo "✓ Found SK program: '{$program->title}' (Category: {$program->category_label}, Status: {$program->status_label}, Budget: ₱{$program->budget})\n";

// Test Status transition controller
Auth::login($skChair);
$progController = new SkProgramController();

// Test proposing a new program
$testProg = SkProgram::create([
    'title'               => 'Test Youth Esports Tournament',
    'category'            => 'sports_and_wellness',
    'description'         => 'Local Mobile Legends and Valorant barangay youth tournament.',
    'budget'              => 10000.00,
    'start_date'          => today(),
    'status'              => 'proposed',
    'created_by'          => $skCouncilor->id,
]);
assert($testProg->status === 'proposed', "Program initial status should be proposed");

// Approve as SK Chairman
$req = new Request(['action' => 'approve']);
$progController->updateStatus($req, $testProg);
$testProg->refresh();
assert($testProg->status === 'approved', "Program should now be approved");

// Start program
$req = new Request(['action' => 'start']);
$progController->updateStatus($req, $testProg);
$testProg->refresh();
assert($testProg->status === 'ongoing', "Program should now be ongoing");

// Complete program
$req = new Request(['action' => 'complete']);
$progController->updateStatus($req, $testProg);
$testProg->refresh();
assert($testProg->status === 'completed', "Program should now be completed");

// Clean up test program
$testProg->delete();
echo "✓ SK Program full lifecycle (proposed -> approved -> ongoing -> completed) tested successfully.\n";

echo "\n=== 4. TESTING VIEW RENDERING ACROSS ALL SK PAGES ===\n";
Auth::login($skChair);
\Illuminate\Support\Facades\View::share('errors', new \Illuminate\Support\ViewErrorBag());

// 1. Dashboard
$skController = new SkController();
$dashView = $skController->dashboard();
$dashHtml = $dashView->render();
echo sprintf("✓ Rendered [sk.dashboard] successfully (%d bytes)\n", strlen($dashHtml));

// 2. Youth Residents Directory
$rosterReq = new Request();
$rosterView = $skController->youthResidents($rosterReq);
$rosterHtml = $rosterView->render();
echo sprintf("✓ Rendered [sk.youth-residents.index] successfully (%d bytes)\n", strlen($rosterHtml));

// 3. Youth Resident Profile
$anyYouth = Resident::active()->whereBetween('birthdate', [now()->subYears(31)->addDay(), now()->subYears(15)->endOfDay()])->first();
if ($anyYouth) {
    $youthProfileView = $skController->youthResidentShow($anyYouth);
    $youthProfileHtml = $youthProfileView->render();
    echo sprintf("✓ Rendered [sk.youth-residents.show] successfully (%d bytes)\n", strlen($youthProfileHtml));
}

// 4. Programs Index & Show & Edit & Create
$progIndex = $progController->index(new Request());
echo sprintf("✓ Rendered [sk.programs.index] successfully (%d bytes)\n", strlen($progIndex->render()));

$progCreate = $progController->create();
echo sprintf("✓ Rendered [sk.programs.create] successfully (%d bytes)\n", strlen($progCreate->render()));

$progShow = $progController->show($program);
echo sprintf("✓ Rendered [sk.programs.show] successfully (%d bytes)\n", strlen($progShow->render()));

$progEdit = $progController->edit($program);
echo sprintf("✓ Rendered [sk.programs.edit] successfully (%d bytes)\n", strlen($progEdit->render()));

// 5. Announcements Index, Create, Show, Edit
$annController = new SkAnnouncementController();
$annIndex = $annController->index(new Request());
echo sprintf("✓ Rendered [sk.announcements.index] successfully (%d bytes)\n", strlen($annIndex->render()));

$annCreate = $annController->create();
echo sprintf("✓ Rendered [sk.announcements.create] successfully (%d bytes)\n", strlen($annCreate->render()));

$ann = Announcement::first();
if ($ann) {
    $annShow = $annController->show($ann);
    echo sprintf("✓ Rendered [sk.announcements.show] successfully (%d bytes)\n", strlen($annShow->render()));

    $annEdit = $annController->edit($ann);
    echo sprintf("✓ Rendered [sk.announcements.edit] successfully (%d bytes)\n", strlen($annEdit->render()));
}

echo "\n=== ALL PHASE 4 TESTS COMPLETED WITH 100% SUCCESS ===\n";
