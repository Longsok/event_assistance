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
        Schema::create('schedule_tamplates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('event_categories')->cascadeOnDelete();
            $table->string('session_name');
            $table->enum('anchor', [
                'start',
                'end',
                'middle',
                'proportional'
            ])->default('proportional');
            $table->integer('offset_minutes')->default(0);
            // offset from anchor point in minutes
            $table->integer('duration_minutes')->default(60);
            $table->boolean('is_break')->default(false);
            $table->string('scale_trigger')->default('any');
            // e.g. any | capacity > 200 | meal = yes
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_tamplates');
    }
};
