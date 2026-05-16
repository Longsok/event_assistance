<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    // Global guest list (reusable across events)
    public function index(Request $request)
    {
        $guests = Guest::where('user_id', auth()->id())
            ->when($request->search, fn($q) =>
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
            )
            ->withCount('eventGuests')
            ->latest()
            ->paginate(20);

        return view('guests.index', compact('guests'));
    }

    public function create()
    {
        return view('guests.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        Guest::create([
            'user_id'       => auth()->id(),
            'name'          => $request->name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'custom_fields' => $request->custom_fields ?? [],
        ]);

        return redirect()->route('guests.index')
                         ->with('success', 'Guest added.');
    }

    public function edit(Guest $guest)
    {
        abort_if($guest->user_id !== auth()->id(), 403);
        return view('guests.edit', compact('guest'));
    }

    public function update(Request $request, Guest $guest)
    {
        abort_if($guest->user_id !== auth()->id(), 403);

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $guest->update($request->only(['name', 'email', 'phone', 'custom_fields']));

        return redirect()->route('guests.index')
                         ->with('success', 'Guest updated.');
    }

    public function destroy(Guest $guest)
    {
        abort_if($guest->user_id !== auth()->id(), 403);
        $guest->delete();
        return back()->with('success', 'Guest deleted.');
    }
}
