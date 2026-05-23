<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function create()
    {
        return view('admin.users.create');
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|in:admin,organizer',
        ]);

        $user = \App\Models\User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->name} created successfully.");
    }

    public function index()
    {
        $users = User::with('roles')
            ->withCount('events')
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
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

    public function demoteToUser(User $user)
    {
        // Prevent demoting yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot demote yourself.');
        }

        $user->syncRoles(['user']);
        return back()->with('success', "{$user->name} demoted to user.");
    }

    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')
                         ->with('success', 'User deleted.');
    }

    public function setRole(\Illuminate\Http\Request $request, User $user)
    {
        $request->validate(['role' => 'required|in:user,organizer,admin']);

        if ($request->role === 'user') {
            $user->syncRoles([]);
        } else {
            $user->syncRoles([$request->role]);
        }

        return back()->with('success', "{$user->name} role updated to {$request->role}.");
    }
}
