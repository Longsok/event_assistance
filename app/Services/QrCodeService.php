<?php

namespace App\Services;

use App\Models\Event;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    /**
     * Generate the shared event-day QR code.
     * This QR points to the public check-in page.
     * One QR per event — all guests use the same one.
     */
    public function generateEventQr(Event $event): string
    {
        $url = route('public.checkin', $event->attendance_token);

        // Generate as base64 SVG for inline display
        return base64_encode(
            QrCode::format('svg')
                ->size(300)
                ->errorCorrection('H')
                ->generate($url)
        );
    }

    /**
     * Generate the invite QR code.
     * Points to the guest self-registration page.
     * Used on the invite card.
     */
    public function generateInviteQr(Event $event): string
    {
        $url = route('public.register', $event->invite_token);

        return base64_encode(
            QrCode::format('svg')
                ->size(200)
                ->errorCorrection('M')
                ->generate($url)
        );
    }

    /**
     * Generate QR as PNG file and save to storage.
     * Used for downloadable/printable QR.
     */
    public function generateAndSave(Event $event): string
    {
        $url      = route('public.checkin', $event->attendance_token);
        $filename = "qrcodes/event-{$event->id}-checkin.png";

        $qrCode = QrCode::format('png')
            ->size(400)
            ->errorCorrection('H')
            ->generate($url);

        \Storage::disk('public')->put($filename, $qrCode);

        return \Storage::disk('public')->url($filename);
    }

    /**
     * Get the check-in URL for an event.
     */
    public function getCheckinUrl(Event $event): string
    {
        return route('public.checkin', $event->attendance_token);
    }

    /**
     * Get the invite URL for an event.
     */
    public function getInviteUrl(Event $event): string
    {
        return route('public.register', $event->invite_token);
    }
}
