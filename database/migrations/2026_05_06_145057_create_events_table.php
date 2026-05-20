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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('event_categories')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('venue')->nullable();
            $table->string('address')->nullable();
            $table->date('event_date');
            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('capacity')->default(0);
            $table->enum('venue_type', ['indoor', 'outdoor', 'hybrid'])->default('indoor');
            $table->boolean('meal_provided')->default(false);
            $table->enum('status', [
                'draft',
                'published',
                'ongoing',
                'completed',
                'archived'
            ])->default('draft');
            $table->boolean('is_public')->default(true);
            $table->string('cover_image')->nullable();
            $table->string('invite_token')->unique()->nullable();
            // used for public invite link /join/{invite_token}
            $table->string('attendance_token')->unique()->nullable();
            // generated on event day for shared QR /attend/{attendance_token}
            $table->boolean('allow_self_registration')->default(true);
            $table->integer('max_registrations')->nullable();
            // null = use capacity
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
