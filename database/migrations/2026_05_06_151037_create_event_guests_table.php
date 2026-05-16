<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('event_guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('guest_id')->constrained('guests')->cascadeOnDelete();
            $table->string('guest_code')->unique();
            // e.g. CONF-2025-001 — used for event day check-in
            $table->enum('rsvp_status', ['pending','confirmed','declined'])->default('pending');
            $table->enum('registered_via', ['organizer','invite_link','csv_import'])->default('organizer');
            $table->string('seat_number')->nullable();
            $table->string('meal_preference')->nullable();
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('registered_at')->nullable();
            // when guest self-registered via invite link
            $table->boolean('confirmation_sent')->default(false);
            $table->timestamps();

            // a guest can only be registered once per event
            $table->unique(['event_id', 'guest_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_guests');
    }
};
