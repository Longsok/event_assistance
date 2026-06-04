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
    'Planning & Administration',
    'Catering & Food',
    'Decoration & Setup',
    'Music & Entertainment',
    'Photography & Video',
    'Guest Management',
    'Transportation',
    'Ceremony & Rituals',
    'Outfits & Costumes',
    'Hair & Makeup',
    'Budget & Finance',
    'Venue & Logistics',
    'Technical & AV',
    'Security & Safety',
    'Marketing & Promotion',
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
