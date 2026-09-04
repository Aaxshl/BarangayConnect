<?php
require 'c:/Users/Ashley Cuevas/Downloads/smartbarangay/smartbarangay/vendor/autoload.php';
$app = require_once 'c:/Users/Ashley Cuevas/Downloads/smartbarangay/smartbarangay/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Resident;
use App\Models\Announcement;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SkController;
use App\Http\Controllers\ResidentPortalController;
use Illuminate\Http\Request;

echo "=== 1. VERIFYING REMOVAL OF ADULT YOUTH (25-30) FROM SK ROSTER ===\n";
$skChair = User::where('role', 'sk_chairman')->first();
Auth::login($skChair);
\Illuminate\Support\Facades\View::share('errors', new \Illuminate\Support\ViewErrorBag());

$skController = new SkController();
$rosterReq = new Request();
$rosterView = $skController->youthResidents($rosterReq);
$youthList = $rosterView->getData()['youthResidents'];

echo "Checking ages of residents returned in the SK Youth list:\n";
foreach ($youthList as $y) {
    echo " - {$y->full_name} (Age: {$y->age})\n";
    assert($y->age >= 15 && $y->age <= 24, "Resident {$y->full_name} age {$y->age} must be between 15 and 24!");
}
echo "✓ All residents in SK Youth Directory are strictly between 15 and 24 years old!\n";

// Verify residents 25-30 are excluded
$adultYouthResident = Resident::whereBetween('birthdate', [now()->subYears(31)->addDay(), now()->subYears(25)->endOfDay()])->first();
if ($adultYouthResident) {
    $foundInList = $youthList->contains('id', $adultYouthResident->id);
    assert(!$foundInList, "Adult youth ({$adultYouthResident->full_name}, age {$adultYouthResident->age}) must NOT be in the SK youth list");
    echo "✓ Verified: {$adultYouthResident->full_name} (Age: {$adultYouthResident->age}) is excluded from the SK roster.\n";
}

// Check dashboard rendering with 15-24 age scope
$dashView = $skController->dashboard();
$dashHtml = $dashView->render();
assert(strpos($dashHtml, 'Residents aged 15–24') !== false, "Dashboard contains updated 15–24 label");
assert(strpos($dashHtml, '25–30 years old') === false, "Dashboard does NOT contain 25–30 cohort");
echo "✓ SK Dashboard successfully updated to 15–24 age range.\n";


echo "\n=== 2. VERIFYING SK ANNOUNCEMENTS IN RESIDENT PORTAL ===\n";
// Create a test announcement published by SK Chairman
$skAnn = Announcement::create([
    'title'             => 'Inter-Barangay SK Youth Basketball Tryouts',
    'announcement_type' => 'community_event',
    'body'              => 'Open tryouts for ages 15-24 at the covered court this Saturday morning. Bring valid ID.',
    'status'            => 'published',
    'published_at'      => now(),
    'created_by'        => $skChair->id,
]);

assert($skAnn->isSkAnnouncement() === true, "Announcement created by SK Chairman is recognized as an SK announcement");
assert($skAnn->publisher_label === 'Sangguniang Kabataan (SK)', "Publisher label is Sangguniang Kabataan (SK)");
echo "✓ Model correctly identifies SK announcement and returns publisher label.\n";

// Test 1: Resident Landing Page (home)
$portalController = new ResidentPortalController();
$homeView = $portalController->home();
$homeHtml = $homeView->render();
assert(strpos($homeHtml, '⚡ Sangguniang Kabataan (SK)') !== false, "Resident home displays SK badge");
assert(strpos($homeHtml, 'Inter-Barangay SK Youth Basketball Tryouts') !== false, "Resident home displays the announcement");
echo "✓ Resident Portal Landing Page (home) renders SK announcement with distinct SK badge!\n";

// Test 2: Resident Dashboard
$residentUser = Resident::first();
session(['resident_id' => $residentUser->id]);
$dashView = $portalController->dashboard();
$dashHtml = $dashView->render();
assert(strpos($dashHtml, '⚡ SK') !== false, "Resident dashboard displays SK badge");
echo "✓ Resident Dashboard renders SK announcement with distinct badge!\n";

// Test 3: Resident Announcements Page
$annView = view('resident.announcements', [
    'announcements' => Announcement::published()->latest('published_at')->paginate(9),
    'settings'      => Setting::all()->pluck('value','key')->toArray()
]);
$annHtml = $annView->render();
assert(strpos($annHtml, '⚡ Sangguniang Kabataan (SK)') !== false, "Announcements list displays SK badge");
echo "✓ Resident Announcements Page renders SK announcement with distinct badge!\n";

// Clean up test announcement
$skAnn->delete();

echo "\n=== ALL VERIFICATIONS COMPLETED WITH 100% SUCCESS ===\n";
