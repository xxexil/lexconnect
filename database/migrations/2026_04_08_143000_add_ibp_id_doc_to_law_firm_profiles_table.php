<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('law_firm_profiles', function (Blueprint $table) {
            $table->string('ibp_id_doc')->nullable()->after('valid_id_doc');
        });
    }

    public function down(): void
    {
        Schema::table('law_firm_profiles', function (Blueprint $table) {
            $table->dropColumn('ibp_id_doc');
        });
    }
};
