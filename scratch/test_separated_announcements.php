<?php
require 'c:/Users/Ashley Cuevas/Downloads/smartbarangay/smartbarangay/vendor/autoload.php';
$app = require_once 'c:/Users/Ashley Cuevas/Downloads/smartbarangay/smartbarangay/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Announcement;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\SkAnnouncementController;
use App\Http\Controllers\ResidentPortalController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

echo "=== 1. CREATING TEST ANNOUNCEMENTS FOR BARANGAY AND SK ===\n";
$captain = User::where('role', 'captain')->first();
$skChair = User::where('role', 'sk_chairman')->first();

\Illuminate\Support\Facades\View::share('errors', new \Illuminate\Support\ViewErrorBag());

// Barangay Announcement
$brgyAnn = Announcement::create([
    'title'             => 'Barangay Clean-Up Drive 2026',
    'announcement_type' => 'community_event',
    'body'              => 'All puroks are invited to participate in the community clean-up drive.',
    'status'            => 'published',
    'published_at'      => now(),
    'created_by'        => $captain->id,
]);

// SK Announcement
$skAnn = Announcement::create([
    'title'             => 'SK Inter-Purok Youth Volleyball League',
    'announcement_type' => 'community_event',
    'body'              => 'Open to youth aged 15-24. Registration open at the SK Office.',
    'status'            => 'published',
    'published_at'      => now(),
    'created_by'        => $skChair->id,
]);

echo "Created Barangay Announcement: ID {$brgyAnn->id} ('{$brgyAnn->title}')\n";
echo "Created SK Announcement: ID {$skAnn->id} ('{$skAnn->title}')\n";

assert($brgyAnn->isBarangayAnnouncement() === true, "brgyAnn is recognized as Barangay announcement");
assert($brgyAnn->isSkAnnouncement() === false, "brgyAnn is NOT recognized as SK announcement");

assert($skAnn->isSkAnnouncement() === true, "skAnn is recognized as SK announcement");
assert($skAnn->isBarangayAnnouncement() === false, "skAnn is NOT recognized as Barangay announcement");
echo "✓ Announcement Model helpers correctly identify Barangay vs SK origin.\n";


echo "\n=== 2. VERIFYING QUERY SCOPES (scopeBarangay vs scopeSk) ===\n";
$brgyList = Announcement::barangay()->pluck('id')->toArray();
$skList   = Announcement::sk()->pluck('id')->toArray();

assert(in_array($brgyAnn->id, $brgyList), "Barangay scope includes Barangay announcement");
assert(!in_array($skAnn->id, $brgyList), "Barangay scope EXCLUDES SK announcement");

assert(in_array($skAnn->id, $skList), "SK scope includes SK announcement");
assert(!in_array($brgyAnn->id, $skList), "SK scope EXCLUDES Barangay announcement");
echo "✓ Query scopes strictly separate Barangay and SK announcements.\n";


echo "\n=== 3. VERIFYING BARANGAY ADMIN ANNOUNCEMENT CONTROLLER ISOLATION ===\n";
Auth::login($captain);
$adminController = new AnnouncementController();

// 1. Index should ONLY contain Barangay announcements
$adminIndexView = $adminController->index(new Request());
$adminItems = $adminIndexView->getData()['announcements'];
foreach ($adminItems as $item) {
    assert(!$item->isSkAnnouncement(), "Admin announcement index must NOT contain any SK announcements");
}
echo "✓ Admin Announcement Index strictly lists Barangay announcements only.\n";

// 2. Attempting to view, edit, update, delete, or publish an SK announcement via Admin Controller MUST throw 403
$blockedActions = [
    'show'    => fn() => $adminController->show($skAnn),
    'edit'    => fn() => $adminController->edit($skAnn),
    'update'  => fn() => $adminController->update(new Request(['title'=>'Hack','body'=>'Hack','announcement_type'=>'general']), $skAnn),
    'destroy' => fn() => $adminController->destroy($skAnn),
    'publish' => fn() => $adminController->publish($skAnn),
    'archive' => fn() => $adminController->archive($skAnn),
];

foreach ($blockedActions as $action => $closure) {
    $blocked = false;
    try {
        $closure();
    } catch (HttpException $e) {
        if ($e->getStatusCode() === 403) {
            $blocked = true;
        }
    }
    assert($blocked, "Barangay Admin Controller {$action}() on SK announcement must be BLOCKED with 403 Forbidden");
    echo "✓ Barangay Admin Controller {$action}() on SK announcement blocked with 403.\n";
}


echo "\n=== 4. VERIFYING SK ANNOUNCEMENT CONTROLLER ISOLATION ===\n";
Auth::login($skChair);
$skController = new SkAnnouncementController();

// 1. Index should ONLY contain SK announcements
$skIndexView = $skController->index(new Request());
$skItems = $skIndexView->getData()['announcements'];
foreach ($skItems as $item) {
    assert($item->isSkAnnouncement(), "SK announcement index must ONLY contain SK announcements");
}
echo "✓ SK Announcement Index strictly lists SK announcements only.\n";

// 2. Attempting to view, edit, update, or delete a Barangay announcement via SK Controller MUST throw 403
$skBlockedActions = [
    'show'    => fn() => $skController->show($brgyAnn),
    'edit'    => fn() => $skController->edit($brgyAnn),
    'update'  => fn() => $skController->update(new Request(['title'=>'Hack','body'=>'Hack','announcement_type'=>'general','status'=>'published']), $brgyAnn),
    'destroy' => fn() => $skController->destroy($brgyAnn),
];

foreach ($skBlockedActions as $action => $closure) {
    $blocked = false;
    try {
        $closure();
    } catch (HttpException $e) {
        if ($e->getStatusCode() === 403) {
            $blocked = true;
        }
    }
    assert($blocked, "SK Controller {$action}() on Barangay announcement must be BLOCKED with 403 Forbidden");
    echo "✓ SK Controller {$action}() on Barangay announcement blocked with 403.\n";
}


echo "\n=== 5. VERIFYING RESIDENT PORTAL SOURCE FILTERING ===\n";
$portalController = new ResidentPortalController();

// All announcements
$allView = $portalController->announcements(new Request());
$allIds = $allView->getData()['announcements']->pluck('id')->toArray();
assert(in_array($brgyAnn->id, $allIds) && in_array($skAnn->id, $allIds), "All filter contains both announcements");

// Barangay Only filter
$brgyPortalView = $portalController->announcements(new Request(['source' => 'barangay']));
$brgyPortalIds = $brgyPortalView->getData()['announcements']->pluck('id')->toArray();
assert(in_array($brgyAnn->id, $brgyPortalIds), "Barangay filter contains Barangay announcement");
assert(!in_array($skAnn->id, $brgyPortalIds), "Barangay filter does NOT contain SK announcement");

// SK Only filter
$skPortalView = $portalController->announcements(new Request(['source' => 'sk']));
$skPortalIds = $skPortalView->getData()['announcements']->pluck('id')->toArray();
assert(in_array($skAnn->id, $skPortalIds), "SK filter contains SK announcement");
assert(!in_array($brgyAnn->id, $skPortalIds), "SK filter does NOT contain Barangay announcement");

echo "✓ Resident portal source tabs (All, Barangay Office, Sangguniang Kabataan) operate cleanly!\n";

// Clean up test data
$brgyAnn->delete();
$skAnn->delete();

echo "\n=== ALL VERIFICATIONS PASSED WITH 100% SUCCESS ===\n";
