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
        Schema::create('event_tasks', function (Blueprint $table) {
            $table->id();
                $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('group_id')->constrained('task_groups')->cascadeOnDelete();
            $table->string('task_name');
            $table->date('original_due_date')->nullable();
            // what the template originally intended
            $table->date('due_date');
            // actual scheduled date — rescheduled to today if overdue at creation
            $table->enum('priority', ['high', 'medium', 'low'])->default('medium');
            $table->enum('status', [
                'pending',
                'in_progress',
                'done',
                'overdue',
                'skipped'
            ])->default('pending');
            $table->boolean('is_custom')->default(false);
            // false = generated from template | true = added by organizer
            $table->boolean('is_late')->default(false);
            // flagged at creation time if due_date was already in the past
            $table->text('late_note')->nullable();
            // explains why task is late
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_tasks');
    }
};
