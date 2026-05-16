<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\Guest;
use App\Models\EventGuest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GuestRegistrationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Event      $event,
        public Guest      $guest,
        public EventGuest $eventGuest,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "You're registered for {$this->event->title}!",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.guest-confirmation',
            with: [
                'event'      => $this->event,
                'guest'      => $this->guest,
                'eventGuest' => $this->eventGuest,
                'guestCode'  => $this->eventGuest->guest_code,
                'schedule'   => $this->event->schedules()
                                    ->orderBy('day_number')
                                    ->orderBy('start_time')
                                    ->get(),
            ]
        );
    }
}
