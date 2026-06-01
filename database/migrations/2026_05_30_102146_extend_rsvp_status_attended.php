<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The check-in flow sets event_guests.rsvp_status to 'attended', but the
     * original column only allowed pending/confirmed/declined, so writing
     * 'attended' threw an integrity-constraint error and turned check-in/scan
     * into a 500. This widens the allowed set. Driver aware (MySQL + SQLite).
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement(
                "ALTER TABLE `event_guests`
                 MODIFY `rsvp_status`
                 ENUM('pending','confirmed','declined','attended')
                 NOT NULL DEFAULT 'pending'"
            );
            return;
        }

        if ($driver === 'sqlite') {
            // SQLite cannot ALTER a CHECK constraint in place; rebuild the column
            // as a plain string (valid values enforced in app logic).
            Schema::table('event_guests', function ($table) {
                $table->string('rsvp_status_tmp')->default('pending');
            });
            DB::table('event_guests')->update(['rsvp_status_tmp' => DB::raw('rsvp_status')]);
            Schema::table('event_guests', function ($table) {
                $table->dropColumn('rsvp_status');
            });
            Schema::table('event_guests', function ($table) {
                $table->renameColumn('rsvp_status_tmp', 'rsvp_status');
            });
            return;
        }

        DB::statement("ALTER TABLE event_guests ALTER COLUMN rsvp_status TYPE VARCHAR(20)");
    }

    public function down(): void
    {
        // No-op: narrowing again could fail for rows already set to 'attended'.
    }
};