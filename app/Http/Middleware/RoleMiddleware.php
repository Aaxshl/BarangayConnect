<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware {
    /**
     * Handle incoming request and verify user role against allowed roles.
     * Usage in routes: ->middleware('role:captain,administrator')
     */
    public function handle(Request $request, Closure $next, ...$roles) {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $user = Auth::user();

        if ($user->status !== 'active') {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account has been deactivated.');
        }

        // Support both comma-separated and variadic arguments
        $allowedRoles = [];
        foreach ($roles as $role) {
            foreach (explode(',', $role) as $r) {
                $allowedRoles[] = trim($r);
            }
        }

        if (!in_array($user->role, $allowedRoles)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized action for your role.'], 403);
            }
            abort(403, 'Unauthorized. You do not have permission to access this feature.');
        }

        return $next($request);
    }
}
