<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Backfill attendance_token for any existing event that does not have one,
     * so old events created before the auto-generation change can still resolve
     * their public check-in link.
     */
    public function up(): void
    {
        DB::table('events')
            ->whereNull('attendance_token')
            ->orderBy('id')
            ->each(function ($event) {
                DB::table('events')
                    ->where('id', $event->id)
                    ->update(['attendance_token' => (string) Str::uuid()]);
            });
    }

    public function down(): void
    {
        // No-op: we do not want to remove tokens on rollback.
    }
};