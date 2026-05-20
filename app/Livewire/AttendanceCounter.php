<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\AttendanceLog;
use App\Services\AttendanceService;
use Livewire\Attributes\Polling;
use Livewire\Component;

class AttendanceCounter extends Component
{
    public Event $event;
    public array $stats = [];

    public function mount(Event $event): void
    {
        $this->event = $event;
        $this->loadStats();
    }

    /**
     * Poll every 3 seconds to update the live counter.
     */
    #[Polling('3s')]
    public function loadStats(): void
    {
        $service     = app(AttendanceService::class);
        $this->stats = $service->getStats($this->event);
    }

    public function render()
    {
        return view('livewire.attendance-counter');
    }
}
