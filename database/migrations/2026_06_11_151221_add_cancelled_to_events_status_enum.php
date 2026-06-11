<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE events MODIFY COLUMN status ENUM('draft','published','ongoing','completed','cancelled') NOT NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE events MODIFY COLUMN status ENUM('draft','published','ongoing','completed') NOT NULL DEFAULT 'draft'");
    }
};
