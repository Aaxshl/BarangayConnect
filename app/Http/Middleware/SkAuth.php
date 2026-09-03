<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkAuth {
    public function handle(Request $request, Closure $next) {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $user = Auth::user();

        if ($user->status !== 'active') {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account has been deactivated.');
        }

        if (!$user->canAccessSkPortal()) {
            return redirect()->route('admin.dashboard')->with('error', 'Access restricted to SK Officials.');
        }

        return $next($request);
    }
}
