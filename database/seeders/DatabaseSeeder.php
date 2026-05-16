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
        // ── 1. Create roles ───────────────────────────────────────────────
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $userRole  = Role::firstOrCreate(['name' => 'user',  'guard_name' => 'web']);

        // ── 2. Create / update admin user ─────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'soklongyoung03@gmail.com'],
            [
                'name'     => 'Admin Long',
                'password' => Hash::make('long123456'),
            ]
        );
        // Sync so running multiple times won't stack roles
        $admin->syncRoles([$adminRole]);

        // ── 3. Create a sample organizer (regular user) ───────────────────
        $organizer = User::firstOrCreate(
            ['email' => 'soklong260@gmail.com'],
            [
                'name'     => 'Long',
                'password' => Hash::make('long123456'),
            ]
        );
        $organizer->syncRoles([$userRole]);

        $this->command->info('Seeding complete.');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['admin', 'soklongyoung03@gmail.com',     'long123456'],
                ['user',  'soklong260@gmail.com', 'long123456'],
            ]
        );
    }
}
