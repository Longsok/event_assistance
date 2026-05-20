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
        Schema::create('event_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->integer('total_invited')->default(0);
            $table->integer('total_attended')->default(0);
            $table->integer('tasks_completed')->default(0);
            $table->integer('tasks_total')->default(0);
            $table->decimal('total_budget', 12, 2)->default(0);
            $table->decimal('total_spent', 12, 2)->default(0);
            $table->decimal('total_contributions', 12, 2)->default(0);
            $table->text('organizer_notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();


            // one completion record per event
            $table->unique('event_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_completions');
    }
};
