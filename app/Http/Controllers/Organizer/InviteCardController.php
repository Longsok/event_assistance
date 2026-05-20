<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\InviteCardService;
use Illuminate\Http\Request;

class InviteCardController extends Controller
{
    public function __construct(private InviteCardService $inviteCardService) {}

    public function show(Event $event)
    {
        $this->authorizeEvent($event);

        $inviteCard = $event->inviteCard;
        $inviteUrl  = route('public.register', $event->invite_token);

        return view('invite.show', compact('event', 'inviteCard', 'inviteUrl'));
    }

    public function update(Request $request, Event $event)
    {
        $this->authorizeEvent($event);

        $request->validate([
            'template_style' => 'required|in:default,elegant,minimal',
            'show_agenda'    => 'boolean',
            'show_venue'     => 'boolean',
            'show_qr'        => 'boolean',
            'custom_message' => 'nullable|string|max:500',
        ]);

        $event->inviteCard()->updateOrCreate(
            ['event_id' => $event->id],
            $request->only([
                'template_style',
                'show_agenda',
                'show_venue',
                'show_qr',
                'custom_message',
            ])
        );

        return back()->with('success', 'Invite card updated.');
    }

    // Copy invite link to clipboard (just returns the URL)
    public function getInviteLink(Event $event)
    {
        $this->authorizeEvent($event);

        return response()->json([
            'url' => route('public.register', $event->invite_token),
        ]);
    }

    private function authorizeEvent(Event $event): void
    {
        abort_if($event->user_id !== auth()->id(), 403);
    }
}
