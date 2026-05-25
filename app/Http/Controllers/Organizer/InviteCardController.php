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
            'template_style' => 'required|in:classic,elegant,minimal',
            'show_agenda'    => 'nullable|boolean',
            'show_venue'     => 'nullable|boolean',
            'show_qr'        => 'nullable|boolean',
            'custom_message' => 'nullable|string|max:500',
        ]);
        $event->inviteCard()->updateOrCreate(
            ['event_id' => $event->id],
            [
                'template_style' => $request->template_style,
                'show_agenda'    => $request->boolean('show_agenda'),
                'show_venue'     => $request->boolean('show_venue'),
                'show_qr'        => $request->boolean('show_qr'),
                'custom_message' => $request->custom_message,
            ]
        );
        return back()->with('success', 'Invite card settings saved!');
    }

    // AJAX: live preview — uses stdClass to avoid $fillable restrictions
    public function preview(Request $request, Event $event)
    {
        $this->authorizeEvent($event);

        // stdClass has no $fillable guard — all values are always set
        $inviteCard                = new \stdClass();
        $inviteCard->template_style = $request->input('template_style', 'classic');
        $inviteCard->show_agenda   = (bool) $request->input('show_agenda', false);
        $inviteCard->show_venue    = (bool) $request->input('show_venue',  false);
        $inviteCard->show_qr       = (bool) $request->input('show_qr',     false);
        $inviteCard->custom_message = $request->input('custom_message', '');

        $event->loadMissing(['schedules', 'category']);

        return view('invite.partials.card-preview', [
            'event'      => $event,
            'inviteCard' => $inviteCard,
            'guestName'  => 'Guest Name',
        ]);
    }

    public function getInviteLink(Event $event)
    {
        $this->authorizeEvent($event);
        return response()->json(['url' => route('public.register', $event->invite_token)]);
    }

    public function guestList(Event $event)
    {
        $this->authorizeEvent($event);
        $eventGuests = $event->eventGuests()
            ->with('guest')
            ->orderBy('created_at')
            ->get();
        return view('invite.guest-qr-list', compact('event', 'eventGuests'));
    }

    private function authorizeEvent(Event $event): void
    {
        abort_if($event->user_id !== auth()->id(), 403);
    }
}
