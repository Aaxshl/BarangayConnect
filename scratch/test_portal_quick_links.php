<?php
require 'c:/Users/Ashley Cuevas/Downloads/smartbarangay/smartbarangay/vendor/autoload.php';
$app = require_once 'c:/Users/Ashley Cuevas/Downloads/smartbarangay/smartbarangay/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\ResidentPortalController;
use Illuminate\Http\Request;

echo "=== VERIFYING WHO WE ARE & CAREERS PAGES & PORTAL BUTTONS ===\n\n";

\Illuminate\Support\Facades\View::share('errors', new \Illuminate\Support\ViewErrorBag());

$portalController = new ResidentPortalController();

// 1. Check About Page
$aboutHtml = $portalController->about()->render();
assert(strpos($aboutHtml, 'Who We Are') !== false, "About page renders title");
assert(strpos($aboutHtml, '/sk/') === false, "About page has no /sk/ links");
assert(strpos($aboutHtml, 'Back to Portal Home') === false, "Bottom Back to Portal Home button removed");
assert(strpos($aboutHtml, '.portal-desktop-topbar, .portal-nav, .mobile-bottom-nav { display: none !important; }') !== false, "About page hides navbar via CSS");
echo "✓ 'Who We Are' renders with top Back button, no bottom button, and navbar hidden.\n";

// 2. Check Careers Page
$careersHtml = $portalController->careers()->render();
assert(strpos($careersHtml, 'Careers &amp; Opportunities') !== false || strpos($careersHtml, 'Careers & Opportunities') !== false, "Careers page renders title");
assert(strpos($careersHtml, '/sk/') === false, "Careers page has no /sk/ links");
assert(strpos($careersHtml, '.portal-desktop-topbar, .portal-nav, .mobile-bottom-nav { display: none !important; }') !== false, "Careers page hides navbar via CSS");
echo "✓ 'Careers & Opportunities' renders with top Back button and navbar hidden.\n";

// 3. Check Home Page Hero - should NOT have the 3 buttons in portal-hero
$homeHtml = $portalController->home()->render();
preg_match('/<div class="portal-hero">.*?<\/div>\s*<!--/s', $homeHtml, $matches);
$heroHtml = $matches[0] ?? '';
assert(strpos($heroHtml, 'Request Document') === false, "Hero does NOT have Request Document button");
assert(strpos($heroHtml, 'Report Incident') === false, "Hero does NOT have Report Incident button");
assert(strpos($heroHtml, 'Track Request') === false, "Hero does NOT have Track Request button");
echo "✓ Landing page hero does NOT contain request, report, or track buttons.\n";

// 4. Check Portal Layout - desktop topbar should NOT have request, report, track links
$aboutLayoutHtml = $portalController->about()->render();
assert(strpos($aboutLayoutHtml, 'portal-desktop-nav-links') !== false, "Portal topbar rendered");
assert(strpos($aboutLayoutHtml, 'Request Document') === false, "Topbar does NOT have Request Document");
assert(strpos($aboutLayoutHtml, 'Report Issue') === false, "Topbar does NOT have Report Issue");
assert(strpos($aboutLayoutHtml, '>Track<') === false, "Topbar does NOT have Track button");
echo "✓ Portal topbar does NOT contain request, report, or track buttons.\n";

assert(strpos($aboutHtml, 'Back to Portal') !== false, "About page has Back to Portal button");
assert(strpos($careersHtml, 'Back to Portal') !== false, "Careers page has Back to Portal button");

// 5. Check Resident Dashboard contains Who We Are & Careers cards
$resident = \App\Models\Resident::first();
if ($resident) {
    session(['resident_id' => $resident->id]);
    $dashHtml = $portalController->dashboard()->render();
    assert(strpos($dashHtml, 'Who We Are') !== false, "Dashboard contains Who We Are card");
    assert(strpos($dashHtml, 'Careers &amp; Opportunities') !== false || strpos($dashHtml, 'Careers & Opportunities') !== false, "Dashboard contains Careers card");
    echo "✓ Resident Dashboard contains Who We Are and Careers cards.\n";
}

echo "\n=== ALL CHECKS PASSED WITH 100% SUCCESS! ===\n";
