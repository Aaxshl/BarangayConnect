<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class ResidentAuth {
    public function handle(Request $request, Closure $next) {
        if (!session('resident_id')) {
            return redirect()->route('login')->with('error', 'Please sign in to access your resident portal.');
        }
        return $next($request);
    }
}
