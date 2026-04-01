<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE consultations MODIFY COLUMN status ENUM('pending','upcoming','completed','cancelled') DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE consultations MODIFY COLUMN status ENUM('upcoming','completed','cancelled') DEFAULT 'upcoming'");
    }
};
