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
        Schema::create('event_budget_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_budget_id')->constrained('event_budgets')->cascadeOnDelete();
            $table->string('line_item');
            // copied directly from budget_template.line_item at generation time
            $table->decimal('suggested_amount', 12, 2)->nullable();
            // calculated once: suggested_percentage / 100 * total_budget
            // never changes after creation — reference point for organizer
            $table->decimal('allocated_amount', 12, 2)->default(0);
            // pre-filled with suggested_amount — organizer can freely edit
            $table->decimal('actual_amount', 12, 2)->default(0);
            // organizer updates as they actually spend money
            $table->boolean('is_custom')->default(false);
            // false = generated from template | true = added by organizer
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_budget_items');
    }
};
