<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EventCategory;
use App\Models\TaskGroup;

class EventTemplateSeeder extends Seeder
{
    /**
     * Seeds realistic task, budget, and schedule templates
     * for a "Wedding" category with proper lead times.
     *
     * Run with: php artisan db:seed --class=EventTemplateSeeder
     */
    public function run(): void
    {
        $this->seedWedding();
        $this->seedConference();
    }

    protected function seedWedding(): void
    {
        $cat = EventCategory::firstOrCreate(
            ['slug' => 'wedding'],
            [
                'name'        => 'Wedding',
                'color'       => '#ec4899',
                'description' => 'Wedding ceremony and reception',
                'is_active'   => true,
            ]
        );

        // ── Task templates with VARIED lead times ──────────────────────
        $groups = [
            'Venue'     => TaskGroup::firstOrCreate(['slug'=>'venue'],     ['name'=>'Venue',     'color'=>'#6366f1']),
            'Catering'  => TaskGroup::firstOrCreate(['slug'=>'catering'],  ['name'=>'Catering',  'color'=>'#f59e0b']),
            'Decor'     => TaskGroup::firstOrCreate(['slug'=>'decor'],     ['name'=>'Decor',     'color'=>'#ec4899']),
            'Logistics' => TaskGroup::firstOrCreate(['slug'=>'logistics'], ['name'=>'Logistics', 'color'=>'#10b981']),
        ];

        $taskTemplates = [
            // 90 days before
            ['task_name'=>'Book wedding venue',           'group'=>'Venue',     'days_before'=>90,  'anchor'=>'before_event', 'priority'=>'high'],
            ['task_name'=>'Book caterer',                 'group'=>'Catering',  'days_before'=>90,  'anchor'=>'before_event', 'priority'=>'high'],
            ['task_name'=>'Book photographer',            'group'=>'Logistics', 'days_before'=>90,  'anchor'=>'before_event', 'priority'=>'high'],
            // 60 days before
            ['task_name'=>'Send save-the-dates',          'group'=>'Logistics', 'days_before'=>60,  'anchor'=>'before_event', 'priority'=>'high'],
            ['task_name'=>'Book florist',                 'group'=>'Decor',     'days_before'=>60,  'anchor'=>'before_event', 'priority'=>'high'],
            ['task_name'=>'Book band or DJ',              'group'=>'Logistics', 'days_before'=>60,  'anchor'=>'before_event', 'priority'=>'medium'],
            // 30 days before
            ['task_name'=>'Send invitations',             'group'=>'Logistics', 'days_before'=>30,  'anchor'=>'before_event', 'priority'=>'high'],
            ['task_name'=>'Confirm guest count with caterer','group'=>'Catering','days_before'=>30, 'anchor'=>'before_event', 'priority'=>'high'],
            ['task_name'=>'Finalise seating plan',        'group'=>'Logistics', 'days_before'=>30,  'anchor'=>'before_event', 'priority'=>'medium'],
            ['task_name'=>'Order wedding cake',           'group'=>'Catering',  'days_before'=>30,  'anchor'=>'before_event', 'priority'=>'medium'],
            // 14 days before
            ['task_name'=>'Final venue walkthrough',      'group'=>'Venue',     'days_before'=>14,  'anchor'=>'before_event', 'priority'=>'high'],
            ['task_name'=>'Confirm transport arrangements','group'=>'Logistics','days_before'=>14,  'anchor'=>'before_event', 'priority'=>'medium'],
            ['task_name'=>'Collect RSVPs',                'group'=>'Logistics', 'days_before'=>14,  'anchor'=>'before_event', 'priority'=>'high'],
            // 7 days before
            ['task_name'=>'Prepare welcome packs for guests','group'=>'Logistics','days_before'=>7, 'anchor'=>'before_event', 'priority'=>'medium'],
            ['task_name'=>'Final menu confirmation',      'group'=>'Catering',  'days_before'=>7,   'anchor'=>'before_event', 'priority'=>'high'],
            ['task_name'=>'Decor delivery & setup plan',  'group'=>'Decor',     'days_before'=>7,   'anchor'=>'before_event', 'priority'=>'medium'],
            // 1 day before
            ['task_name'=>'Rehearsal dinner',             'group'=>'Venue',     'days_before'=>1,   'anchor'=>'before_event', 'priority'=>'high'],
            ['task_name'=>'Decor setup at venue',         'group'=>'Decor',     'days_before'=>1,   'anchor'=>'before_event', 'priority'=>'high'],
            ['task_name'=>'Confirm day-of schedule with all vendors','group'=>'Logistics','days_before'=>1,'anchor'=>'before_event','priority'=>'high'],
            // Day 1 tasks
            ['task_name'=>'Venue final check',            'group'=>'Venue',     'days_before'=>0,   'anchor'=>'first_day', 'priority'=>'high'],
            ['task_name'=>'Guest check-in management',    'group'=>'Logistics', 'days_before'=>0,   'anchor'=>'first_day', 'priority'=>'high'],
            // After event
            ['task_name'=>'Thank you notes to vendors',   'group'=>'Logistics', 'days_before'=>3,   'anchor'=>'after_event', 'priority'=>'low'],
            ['task_name'=>'Return hired equipment',       'group'=>'Venue',     'days_before'=>2,   'anchor'=>'after_event', 'priority'=>'medium'],
        ];

        foreach ($taskTemplates as $t) {
            $cat->categoryTemplates()->updateOrCreate(
                ['task_name' => $t['task_name']],
                [
                    'group_id'    => $groups[$t['group']]->id,
                    'days_before' => $t['days_before'],
                    'anchor'      => $t['anchor'],
                    'priority'    => $t['priority'],
                    'scale_trigger' => 'any',
                    'sort_order'  => 0,
                ]
            );
        }

        // ── Budget templates ──────────────────────────────────────────
        $budgetTemplates = [
            ['line_item'=>'Venue Hire',          'suggested_percentage'=>25, 'scale_trigger'=>'any'],
            ['line_item'=>'Catering',            'suggested_percentage'=>30, 'scale_trigger'=>'any'],
            ['line_item'=>'Photography',         'suggested_percentage'=>10, 'scale_trigger'=>'any'],
            ['line_item'=>'Flowers & Decor',     'suggested_percentage'=>8,  'scale_trigger'=>'any'],
            ['line_item'=>'Entertainment',       'suggested_percentage'=>7,  'scale_trigger'=>'any'],
            ['line_item'=>'Wedding Cake',        'suggested_percentage'=>3,  'scale_trigger'=>'any'],
            ['line_item'=>'Transport',           'suggested_percentage'=>3,  'scale_trigger'=>'any'],
            ['line_item'=>'Invitations & Stationery', 'suggested_percentage'=>2, 'scale_trigger'=>'any'],
            ['line_item'=>'Hair & Makeup',       'suggested_percentage'=>4,  'scale_trigger'=>'any'],
            ['line_item'=>'Miscellaneous',       'suggested_percentage'=>8,  'scale_trigger'=>'any'],
        ];

        foreach ($budgetTemplates as $i => $b) {
            $cat->budgetTemplates()->updateOrCreate(
                ['line_item' => $b['line_item']],
                [
                    'suggested_percentage' => $b['suggested_percentage'],
                    'scale_trigger'        => $b['scale_trigger'],
                    'sort_order'           => $i,
                ]
            );
        }

        // ── Schedule templates ────────────────────────────────────────
        $scheduleTemplates = [
            ['session_name'=>'Bride/Groom preparation',  'anchor'=>'start',       'offset_minutes'=>0,  'duration_minutes'=>120],
            ['session_name'=>'Guest arrival & seating',  'anchor'=>'start',       'offset_minutes'=>120,'duration_minutes'=>45],
            ['session_name'=>'Wedding ceremony',         'anchor'=>'start',       'offset_minutes'=>180,'duration_minutes'=>60],
            ['session_name'=>'Wedding photos',           'anchor'=>'start',       'offset_minutes'=>240,'duration_minutes'=>90],
            ['session_name'=>'Cocktail reception',       'anchor'=>'start',       'offset_minutes'=>330,'duration_minutes'=>60],
            ['session_name'=>'Wedding dinner',           'anchor'=>'start',       'offset_minutes'=>390,'duration_minutes'=>120],
            ['session_name'=>'Speeches & toasts',        'anchor'=>'start',       'offset_minutes'=>510,'duration_minutes'=>45],
            ['session_name'=>'First dance & dancing',    'anchor'=>'start',       'offset_minutes'=>555,'duration_minutes'=>180],
        ];

        foreach ($scheduleTemplates as $i => $s) {
            $cat->scheduleTemplates()->updateOrCreate(
                ['session_name' => $s['session_name']],
                [
                    'anchor'           => $s['anchor'],
                    'offset_minutes'   => $s['offset_minutes'],
                    'duration_minutes' => $s['duration_minutes'],
                    'sort_order'       => $i,
                ]
            );
        }

        $this->command->info("Wedding templates seeded.");
    }

    protected function seedConference(): void
    {
        $cat = EventCategory::firstOrCreate(
            ['slug' => 'conference'],
            [
                'name'        => 'Conference',
                'color'       => '#3b82f6',
                'description' => 'Professional conference or seminar',
                'is_active'   => true,
            ]
        );

        $logisticsGroup = TaskGroup::firstOrCreate(
            ['slug' => 'logistics'],
            ['name' => 'Logistics', 'color' => '#10b981']
        );
        $venueGroup = TaskGroup::firstOrCreate(
            ['slug' => 'venue'],
            ['name' => 'Venue', 'color' => '#6366f1']
        );

        $taskTemplates = [
            ['task_name'=>'Confirm speakers',              'days_before'=>60, 'anchor'=>'before_event', 'priority'=>'high'],
            ['task_name'=>'Send registration invites',     'days_before'=>45, 'anchor'=>'before_event', 'priority'=>'high'],
            ['task_name'=>'Confirm AV equipment',          'days_before'=>21, 'anchor'=>'before_event', 'priority'=>'high'],
            ['task_name'=>'Prepare attendee packs',        'days_before'=>7,  'anchor'=>'before_event', 'priority'=>'medium'],
            ['task_name'=>'Final headcount to caterer',    'days_before'=>3,  'anchor'=>'before_event', 'priority'=>'high'],
            ['task_name'=>'Room & stage setup',            'days_before'=>0,  'anchor'=>'first_day',    'priority'=>'high'],
            ['task_name'=>'AV test & soundcheck',          'days_before'=>0,  'anchor'=>'first_day',    'priority'=>'high'],
            ['task_name'=>'Post-event survey distribution','days_before'=>1,  'anchor'=>'after_event',  'priority'=>'medium'],
        ];

        foreach ($taskTemplates as $t) {
            $cat->categoryTemplates()->updateOrCreate(
                ['task_name' => $t['task_name']],
                [
                    'group_id'    => ($t['anchor'] === 'first_day' ? $venueGroup->id : $logisticsGroup->id),
                    'days_before' => $t['days_before'],
                    'anchor'      => $t['anchor'],
                    'priority'    => $t['priority'],
                    'scale_trigger' => 'any',
                ]
            );
        }

        $budgetTemplates = [
            ['line_item'=>'Venue Hire',     'suggested_percentage'=>30],
            ['line_item'=>'Catering',       'suggested_percentage'=>25],
            ['line_item'=>'AV Equipment',   'suggested_percentage'=>15],
            ['line_item'=>'Marketing',      'suggested_percentage'=>10],
            ['line_item'=>'Speaker Fees',   'suggested_percentage'=>12],
            ['line_item'=>'Miscellaneous',  'suggested_percentage'=>8],
        ];

        foreach ($budgetTemplates as $i => $b) {
            $cat->budgetTemplates()->updateOrCreate(
                ['line_item' => $b['line_item']],
                ['suggested_percentage' => $b['suggested_percentage'], 'sort_order' => $i]
            );
        }

        $this->command->info("Conference templates seeded.");
    }
}
