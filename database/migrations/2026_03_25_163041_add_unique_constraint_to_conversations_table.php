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
        Schema::table('conversations', function (Blueprint $table) {
            // Add unique constraint to prevent duplicate conversations between same client and lawyer
            $table->unique(['client_id', 'lawyer_id'], 'unique_client_lawyer_conversation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            // Remove the unique constraint
            $table->dropUnique('unique_client_lawyer_conversation');
        });
    }
};
