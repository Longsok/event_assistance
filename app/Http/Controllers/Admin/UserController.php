<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')
            ->withCount('events')
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|in:admin,organizer',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->name} created as {$request->role}.");
    }

    public function show(User $user)
    {
        $user->load(['roles', 'events.category']);
        return view('admin.users.show', compact('user'));
    }

    public function promoteToAdmin(User $user)
    {
        $user->syncRoles(['admin']);
        return back()->with('success', "{$user->name} promoted to admin.");
    }

    public function demoteToOrganizer(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot demote yourself.');
        }
        $user->syncRoles(['organizer']);
        return back()->with('success', "{$user->name} set to organizer.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }

    public function setRole(Request $request, User $user)
    {
        $request->validate(['role' => 'required|in:organizer,admin']);

        if ($user->id === auth()->id() && $request->role !== 'admin') {
            return back()->with('error', 'You cannot change your own admin role.');
        }

        $user->syncRoles([$request->role]);
        return back()->with('success', "{$user->name} role updated to {$request->role}.");
    }
}
