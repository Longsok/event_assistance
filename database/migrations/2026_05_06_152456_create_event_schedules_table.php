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
        Schema::create('event_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->integer('day_number');
            $table->date('schedule_date');
            $table->string('session_name');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_minutes');
            $table->string('location')->nullable();
            // e.g. Room A, Main Hall, Outdoor Stage
            $table->boolean('is_break')->default(false);
            // coffee break, lunch break etc
            $table->boolean('is_custom')->default(false);
            // false = generated from template | true = added by organizer
            $table->text('notes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_schedules');
    }
};
