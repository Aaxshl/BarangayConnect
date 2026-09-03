<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\{Resident, Announcement, User};

class SkController extends Controller {
    public function dashboard() {
        $user = Auth::user();
        $youthCount = Resident::whereRaw("strftime('%Y', 'now') - strftime('%Y', birthdate) BETWEEN 15 AND 30")->count();
        $skAnnouncementsCount = Announcement::where('announcement_type', 'community_event')->count();
        
        return view('sk.dashboard', compact('user', 'youthCount', 'skAnnouncementsCount'));
    }
}
