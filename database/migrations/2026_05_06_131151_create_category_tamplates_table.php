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
        Schema::create('category_tamplates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('event_categories')->cascadeOnDelete();
            $table->foreignId('group_id')->constrained('task_groups')->cascadeOnDelete();
            $table->string('task_name');
            $table->integer('days_before')->default(0);
            // negative value means after event e.g. -7 = 7 days after
            $table->enum('anchor', [
                'before_event',
                'first_day',
                'last_day',
                'after_event',
                'proportional'
            ])->default('before_event');
            $table->integer('offset_days')->default(0);
            $table->decimal('position_percent', 5, 2)->nullable();
            // used when anchor = proportional
            $table->enum('priority', ['high', 'medium', 'low'])->default('medium');
            $table->string('scale_trigger')->default('any');
            // e.g. any | capacity > 200 | venue = indoor | meal = yes
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
        Schema::dropIfExists('category_tamplates');
    }
};
