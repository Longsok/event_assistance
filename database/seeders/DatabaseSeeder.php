<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Creates the two roles ('admin', 'user'), an admin account,
     * and a sample organizer account.
     *
     * Run with:  php artisan db:seed
     * Re-run safely (firstOrCreate is idempotent).
     */
   public function run(): void
{
    // ── 1. Roles ──
    $adminRole     = Role::firstOrCreate(['name' => 'admin',     'guard_name' => 'web']);
    $organizerRole = Role::firstOrCreate(['name' => 'organizer', 'guard_name' => 'web']);

    $legacyUserRole = Role::where('name', 'user')->where('guard_name', 'web')->first();
    if ($legacyUserRole) {
        foreach (User::role('user')->get() as $legacyUser) {
            $legacyUser->syncRoles([$organizerRole]);
        }
        $legacyUserRole->delete();
    }

    // ── 2. Admin user ──
    $admin = User::firstOrCreate(
        ['email' => 'soklongyoung03@gmail.com'],
        ['name' => 'Admin Long', 'password' => Hash::make('long123456')]
    );
    $admin->syncRoles([$adminRole]);

    // ── 3. Sample organizer ──
    $organizer = User::firstOrCreate(
        ['email' => 'soklong260@gmail.com'],
        ['name' => 'Long', 'password' => Hash::make('long123456')]
    );
    $organizer->syncRoles([$organizerRole]);

// ── 4. Task Groups ──
$taskGroups = [
    'AV & Technology',
    'Accreditation',
    'Activities',
    'Administration',
    'Artist Booking',
    'Artist Hospitality',
    'Auction',
    'Awards',
    'B2B Meetings',
    'Backstage',
    'Boat Race',
    'Brand Ambassadors',
    'Bridal Styling',
    'Broadcasting',
    'Catering',
    'Ceremonial Elements',
    'Ceremonial Items',
    'Ceremonies',
    'Ceremony',
    'Ceremony Planning',
    'Certificates',
    'Collections',
    'Communications',
    'Community Communication',
    'Competition',
    'Conference Programme',
    'Conservation',
    'Content Development',
    'Content Production',
    'Costumes',
    'Creative Team',
    'Crowd Management',
    'Cultural Activities',
    'Curation',
    'Decorations',
    'Demonstrations',
    'Designers',
    'Digital & Social',
    'Documentation',
    'Donations',
    'Donor Stewardship',
    'Entertainment',
    'Exhibition Design',
    'Exhibitor Management',
    'Exhibitor Services',
    'Facilitation',
    'Family Liaison',
    'Favours',
    'Film Programme',
    'Finance',
    'Fireworks',
    'Floats & Parade',
    'Food & Beverage',
    'Food Safety',
    'Food Village',
    'Fundraising Strategy',
    'Funeral Services',
    'Government & Permits',
    'Graduates',
    'Groom Styling',
    'Guest Management',
    'Hair & Make-Up',
    'Influencer Programme',
    'Infrastructure',
    'Installation',
    'Invitations',
    'Invitations & Guests',
    'Logistics',
    'M&E',
    'Marketing',
    'Materials',
    'Media',
    'Media & Broadcasting',
    'Media & Documentation',
    'Media & PR',
    'Medical & Safety',
    'Models',
    'Music',
    'Networking',
    'Offerings & Items',
    'Officials',
    'Operations',
    'Pagoda Ceremonies',
    'Pagoda Coordination',
    'Performance',
    'Performances',
    'Performers',
    'Permits & Compliance',
    'Photography',
    'Photography & Content',
    'Photography & Video',
    'Planning',
    'Post-Ceremony',
    'Post-Event',
    'Post-Retreat',
    'Post-Show',
    'Procession',
    'Product Experience',
    'Production',
    'Programme',
    'Programme Design',
    'Protocol & Planning',
    'Public Programme',
    'Publications',
    'Red Carpet',
    'Regalia',
    'Registration',
    'Regulations',
    'Rehearsals',
    'Safety & Security',
    'Security',
    'Speakers',
    'Sponsorship',
    'Staffing',
    'Stage & Production',
    'Stakeholder Management',
    'Strategy',
    'Styling',
    'Technical',
    'Technology',
    'Ticketing',
    'Transportation',
    'VIP & Diplomacy',
    'VIP Management',
    'Vendors',
    'Venue',
    'Venue & Accommodation',
    'Venue & Décor',
    'Venue & Logistics',
    'Venue & Site',
    'Venue Theming',
    'Visitor Management',
    'Volunteers',
];

foreach ($taskGroups as $i => $name) {
    $slug = \Illuminate\Support\Str::slug($name);
    \App\Models\TaskGroup::firstOrCreate(
        ['slug' => $slug],
        [
            'name'       => $name,
            'sort_order' => $i + 1,
            'color'      => '#534AB7',
        ]
    );
}

    $this->command->info('Seeding complete.');
    $this->command->table(
        ['Role', 'Email', 'Password'],
        [
            ['admin',     'soklongyoung03@gmail.com', 'long123456'],
            ['organizer', 'soklong260@gmail.com',     'long123456'],
        ]
    );
}
}
