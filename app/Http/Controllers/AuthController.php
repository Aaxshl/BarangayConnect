<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller {
    public function showLogin() {
        if (session('admin_user')) return redirect()->route('admin.dashboard');
        return view('auth.login');
    }
    public function login(Request $request) {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);
        if (Auth::attempt($request->only('email','password'))) {
            $user = Auth::user();
            if ($user->status !== 'active') {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account is inactive.']);
            }
            session(['admin_user' => $user]);
            return redirect()->route('admin.dashboard');
        }
        return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
    }
    public function logout(Request $request) {
        Auth::logout();
        session()->forget('admin_user');
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
