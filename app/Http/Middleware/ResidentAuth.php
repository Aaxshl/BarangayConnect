<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class ResidentAuth {
    public function handle(Request $request, Closure $next) {
        if (!session('resident_id')) {
            return redirect()->route('portal.login')->with('error', 'Please login to continue.');
        }
        return $next($request);
    }
}
