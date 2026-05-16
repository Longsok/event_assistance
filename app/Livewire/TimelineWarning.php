<?php

namespace App\Livewire;

use App\Services\TimelineEngine;
use App\Models\Event;
use Carbon\Carbon;
use Livewire\Component;

class TimelineWarning extends Component
{
    public int    $categoryId  = 0;
    public string $startDate   = '';
    public int    $capacity    = 0;
    public string $venueType   = 'indoor';
    public bool   $meal        = false;

    public array  $preview     = [];
    public bool   $showWarning = false;

    protected TimelineEngine $engine;

    public function boot(TimelineEngine $engine): void
    {
        $this->engine = $engine;
    }

    /**
     * Called every time any field updates on the create event form.
     * Gives live feedback before organizer submits.
     */
    public function updatedStartDate(): void
    {
        $this->runPreview();
    }

    public function updatedCategoryId(): void
    {
        $this->runPreview();
    }

    private function runPreview(): void
    {
        if (!$this->categoryId || !$this->startDate) {
            $this->preview     = [];
            $this->showWarning = false;
            return;
        }

        // Build a temporary event-like object for the engine
        $fakeEvent = new Event([
            'category_id'   => $this->categoryId,
            'start_date'    => $this->startDate,
            'end_date'      => $this->startDate,
            'capacity'      => $this->capacity ?: 100,
            'venue_type'    => $this->venueType,
            'meal_provided' => $this->meal,
        ]);
        $fakeEvent->start_date = Carbon::parse($this->startDate);
        $fakeEvent->end_date   = Carbon::parse($this->startDate);

        $this->preview = $this->engine->preview(
            $this->categoryId,
            Carbon::parse($this->startDate),
            $fakeEvent
        );

        $this->showWarning = $this->preview['overdue_count'] > 0;
    }

    public function render()
    {
        return view('livewire.timeline-warning');
    }
}
