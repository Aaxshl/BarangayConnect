<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller {
    public function index() {
        $users = User::latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }
    public function create() { return view('admin.users.create'); }
    public function store(Request $request) {
        $validated = $request->validate([
            'name'           => 'required|string|max:100',
            'email'          => 'required|email|unique:users',
            'password'       => 'required|min:8|confirmed',
            'role'           => 'required|in:'.implode(',',User::ROLES),
            'contact_number' => 'nullable|string',
        ]);
        $validated['password'] = Hash::make($validated['password']);
        $validated['status']   = 'active';
        User::create($validated);
        return redirect()->route('admin.users.index')->with('success','User created.');
    }
    public function show(User $user) { return view('admin.users.show', compact('user')); }
    public function edit(User $user) { return view('admin.users.edit', compact('user')); }
    public function update(Request $request, User $user) {
        $validated = $request->validate([
            'name'           => 'required|string|max:100',
            'email'          => 'required|email|unique:users,email,'.$user->id,
            'role'           => 'required|in:'.implode(',',User::ROLES),
            'contact_number' => 'nullable|string',
        ]);
        $user->update($validated);
        return redirect()->route('admin.users.index')->with('success','User updated.');
    }
    public function destroy(User $user) {
        $user->update(['status' => 'inactive']);
        return redirect()->route('admin.users.index')->with('success','User deactivated.');
    }
    public function deactivate(User $user) {
        $user->update(['status' => $user->status === 'active' ? 'inactive' : 'active']);
        return back()->with('success','User status toggled.');
    }
}
