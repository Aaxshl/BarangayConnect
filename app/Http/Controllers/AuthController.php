<?php
namespace App\Http\Controllers;

use App\Models\{User, Resident};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller {
    /**
     * Display the unified login page for both Citizens and Staff/Admins.
     */
    public function showLogin() {
        if (Auth::check() || session('admin_user')) {
            $user = Auth::user() ?? session('admin_user');
            if ($user && $user->isSK()) {
                return redirect()->route('sk.dashboard');
            }
            return redirect()->route('admin.dashboard');
        }
        if (session('resident_id')) {
            return redirect()->route('portal.dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle unified login attempt.
     */
    public function login(Request $request) {
        $request->validate([
            'login_id' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginId = trim($request->login_id);
        $password = $request->password;

        // 1. Check Staff / Admin / SK User Account (by email)
        $user = User::where('email', $loginId)->first();
        if ($user && Auth::attempt(['email' => $user->email, 'password' => $password], $request->boolean('remember'))) {
            if ($user->status !== 'active') {
                Auth::logout();
                return back()->withErrors(['login_id' => 'Your staff/admin account is currently inactive.'])->withInput();
            }
            session(['admin_user' => $user]);

            // Route to SK Portal if SK role, else Admin dashboard
            $targetRoute = $user->isSK() ? route('sk.dashboard') : route('admin.dashboard');
            return redirect()->intended($targetRoute)->with('success', 'Welcome back, ' . $user->name . '!');
        }

        // 2. Check Resident / Citizen Account (by contact_number or email)
        $resident = Resident::where('contact_number', $loginId)
            ->orWhere('email', $loginId)
            ->first();

        if ($resident && Hash::check($password, $resident->password ?? '')) {
            if ($resident->status === 'inactive') {
                return back()->withErrors(['login_id' => 'Your resident account is inactive. Please contact the barangay hall.'])->withInput();
            }
            session(['resident_id' => $resident->id]);
            return redirect()->intended(route('portal.dashboard'))->with('success', 'Welcome back, ' . $resident->first_name . '!');
        }

        return back()->withErrors(['login_id' => 'Invalid email, mobile number, or password.'])->withInput();
    }

    /**
     * Unified Logout.
     */
    public function logout(Request $request) {
        Auth::logout();
        session()->forget(['admin_user', 'resident_id']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}
