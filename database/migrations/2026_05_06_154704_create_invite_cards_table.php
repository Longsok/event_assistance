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
        Schema::create('invite_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->string('template_style')->default('default');
            $table->boolean('show_agenda')->default(true);
            $table->boolean('show_venue')->default(true);
            $table->boolean('show_qr')->default(true);
            $table->text('custom_message')->nullable();
            $table->string('file_path')->nullable();
            // stored generated PDF/image path
            $table->timestamps();


            // one invite card config per event
            $table->unique('event_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invite_cards');
    }
};
