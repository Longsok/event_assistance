<?php

namespace App\Livewire;

use App\Models\Guest;
use App\Models\Event;
use Livewire\Component;
use Livewire\WithPagination;

class GuestSearch extends Component
{
    use WithPagination;

    public Event  $event;
    public string $search     = '';
    public string $rsvpFilter = '';

    public function mount(Event $event): void
    {
        $this->event = $event;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedRsvpFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $guests = $this->event->eventGuests()
            ->with('guest')
            ->when($this->search, fn($q) =>
                $q->whereHas('guest', fn($gq) =>
                    $gq->where('name', 'like', '%' . $this->search . '%')
                       ->orWhere('email', 'like', '%' . $this->search . '%')
                )
            )
            ->when($this->rsvpFilter, fn($q) =>
                $q->where('rsvp_status', $this->rsvpFilter)
            )
            ->paginate(15);

        return view('livewire.guest-search', compact('guests'));
    }
}
