<?php
require 'c:/Users/Ashley Cuevas/Downloads/smartbarangay/smartbarangay/vendor/autoload.php';
$app = require_once 'c:/Users/Ashley Cuevas/Downloads/smartbarangay/smartbarangay/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SkAnnouncementController;
use Illuminate\Http\Request;
use Carbon\Carbon;

echo "=== 1. VERIFYING SK ANNOUNCEMENT SCHEDULING ===\n";
$skChair = User::where('role', 'sk_chairman')->first();
Auth::login($skChair);
\Illuminate\Support\Facades\View::share('errors', new \Illuminate\Support\ViewErrorBag());

$skAnnController = new SkAnnouncementController();

// 1. Create a scheduled announcement for tomorrow
$futureDate = now()->addDays(2)->format('Y-m-d H:i:s');
$createReq = new Request([
    'title'             => 'Upcoming Youth Assembly 2026',
    'announcement_type' => 'community_event',
    'body'              => 'Join us for the annual Katipunan ng Kabataan general assembly.',
    'status'            => 'scheduled',
    'published_at'      => $futureDate,
]);

$response = $skAnnController->store($createReq);
assert($response->isRedirect(route('sk.announcements.index')), "Store redirects to announcements index");

$createdAnn = Announcement::where('title', 'Upcoming Youth Assembly 2026')->first();
assert($createdAnn !== null, "Announcement was created in DB");
assert($createdAnn->status === 'scheduled', "Announcement status is scheduled");
assert($createdAnn->published_at !== null, "Announcement published_at is set");
assert(!$createdAnn->isLive(), "Future scheduled announcement is NOT live");
echo "✓ Scheduled announcement created with future publication date.\n";

// 2. Verify scopePublished behavior
$publishedList = Announcement::published()->pluck('id')->toArray();
assert(!in_array($createdAnn->id, $publishedList), "Future scheduled announcement is NOT included in published scope");
echo "✓ Verified scopePublished() hides future scheduled announcements from resident portal.\n";

// 3. Test index view rendering with photo placeholder and scheduled badge
$indexReq = new Request();
$indexView = $skAnnController->index($indexReq);
$indexHtml = $indexView->render();
assert(strpos($indexHtml, 'Scheduled') !== false, "Index view displays Scheduled badge");
assert(strpos($indexHtml, 'Upcoming Youth Assembly 2026') !== false, "Index view lists the scheduled announcement");
echo "✓ Index view correctly renders scheduled status badge and announcement row.\n";

// 4. Test show view rendering with scheduled notice
$showView = $skAnnController->show($createdAnn);
$showHtml = $showView->render();
assert(strpos($showHtml, 'Goes live:') !== false, "Show view displays 'Goes live:' notice");
echo "✓ Show view correctly displays scheduled publication details.\n";

// 5. Test create and edit view rendering
$createView = $skAnnController->create();
$createHtml = $createView->render();
assert(strpos($createHtml, 'Schedule for Later') !== false, "Create view contains Schedule for Later option");
assert(strpos($createHtml, 'id="scheduleDateContainer"') !== false, "Create view contains datetime input container");
echo "✓ Create view form supports scheduling.\n";

$editView = $skAnnController->edit($createdAnn);
$editHtml = $editView->render();
assert(strpos($editHtml, 'Schedule for Later') !== false, "Edit view contains Schedule for Later option");
echo "✓ Edit view form supports scheduling.\n";

// 6. Test with photo attached (simulate image in DB)
$createdAnn->update(['image' => 'announcements/test_banner.jpg']);
$showWithImg = $skAnnController->show($createdAnn)->render();
assert(strpos($showWithImg, 'storage/announcements/test_banner.jpg') !== false, "Show view renders attached photo");
$indexWithImg = $skAnnController->index($indexReq)->render();
assert(strpos($indexWithImg, 'storage/announcements/test_banner.jpg') !== false, "Index view renders attached photo thumbnail");
echo "✓ Verified attached photo is displayed in both index table and show view.\n";

// Clean up test announcement
$createdAnn->delete();

echo "\n=== 2. VERIFYING TEAL COLOR PALETTE ===\n";
$layoutPath = 'c:/Users/Ashley Cuevas/Downloads/smartbarangay/smartbarangay/resources/views/layouts/sk.blade.php';
$layoutContent = file_get_contents($layoutPath);
assert(strpos($layoutContent, '--sk-primary: #0d9488;') !== false, "Layout uses Teal primary color (#0d9488)");
assert(strpos($layoutContent, '--sk-primary-dark: #0f766e;') !== false, "Layout uses Teal dark color (#0f766e)");
assert(strpos($layoutContent, '.btn-primary {') !== false, "Layout has Teal button overrides");
echo "✓ SK Portal Layout verified with Teal palette (#0d9488 / #0f766e).\n";

echo "\n=== ALL TESTS PASSED SUCCESSFULLY! ===\n";
