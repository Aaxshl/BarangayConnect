<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller {
    public function index() {
        $user = auth()->user();
        return view('admin.profile.index', compact('user'));
    }
    public function update(Request $request) {
        $user = auth()->user();
        $validated = $request->validate([
            'name'           => 'required|string|max:100',
            'email'          => 'required|email|unique:users,email,'.$user->id,
            'contact_number' => 'nullable|string',
        ]);
        $user->update($validated);
        return back()->with('success','Profile updated.');
    }
    public function changePassword(Request $request) {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);
        $user = auth()->user();
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }
        $user->update(['password' => Hash::make($request->password)]);
        return back()->with('success','Password updated.');
    }
    public function uploadAvatar(Request $request) {
        $request->validate(['avatar' => 'required|image|max:2048']);
        $path = $request->file('avatar')->store('avatars','public');
        auth()->user()->update(['avatar' => $path]);
        return back()->with('success','Avatar updated.');
    }
}
