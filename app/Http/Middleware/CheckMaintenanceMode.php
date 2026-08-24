<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;

class CheckMaintenanceMode {
    public function handle(Request $request, Closure $next) {
        // Allow admin routes always
        if ($request->is('admin/*') || $request->is('login') || $request->is('logout')) {
            return $next($request);
        }
        $maintenanceMode = Setting::get('maintenance_mode', '0');
        if ($maintenanceMode === '1') {
            return response()->view('errors.maintenance', [], 503);
        }
        return $next($request);
    }
}
