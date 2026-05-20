<?php

namespace App\Services;

use App\Models\Event;
use App\Models\InviteCard;
use App\Services\QrCodeService;

class InviteCardService
{
    public function __construct(private QrCodeService $qrCodeService) {}

    /**
     * Get all data needed to render the invite card.
     */
    public function getCardData(Event $event): array
    {
        $inviteCard = $event->inviteCard ?? new InviteCard([
            'template_style' => 'default',
            'show_agenda'    => true,
            'show_venue'     => true,
            'show_qr'        => true,
            'custom_message' => null,
        ]);

        $schedule = [];
        if ($inviteCard->show_agenda) {
            $schedule = $event->schedules()
                ->orderBy('day_number')
                ->orderBy('start_time')
                ->get()
                ->groupBy('day_number');
        }

        $qrCode = $inviteCard->show_qr
            ? $this->qrCodeService->generateInviteQr($event)
            : null;

        $inviteUrl = $this->qrCodeService->getInviteUrl($event);

        return [
            'event'       => $event,
            'invite_card' => $inviteCard,
            'schedule'    => $schedule,
            'qr_code'     => $qrCode,
            'invite_url'  => $inviteUrl,
        ];
    }

    /**
     * Create or update invite card settings for an event.
     */
    public function updateCard(Event $event, array $data): InviteCard
    {
        return $event->inviteCard()->updateOrCreate(
            ['event_id' => $event->id],
            [
                'template_style' => $data['template_style'] ?? 'default',
                'show_agenda'    => $data['show_agenda'] ?? true,
                'show_venue'     => $data['show_venue'] ?? true,
                'show_qr'        => $data['show_qr'] ?? true,
                'custom_message' => $data['custom_message'] ?? null,
            ]
        );
    }
}
