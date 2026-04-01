<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lawyer_profiles', function (Blueprint $table) {
            $table->foreignId('law_firm_id')
                ->nullable()
                ->after('reviews_count')
                ->constrained('law_firm_profiles')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('lawyer_profiles', function (Blueprint $table) {
            $table->dropForeign(['law_firm_id']);
            $table->dropColumn('law_firm_id');
        });
    }
};
