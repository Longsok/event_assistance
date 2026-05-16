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
        Schema::create('budget_tamplates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('event_categories')->cascadeOnDelete();
            $table->string('line_item');
            $table->decimal('suggested_percentage', 5, 2)->default(0);
            $table->string('scale_trigger')->default('any');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_tamplates');
    }
};
