<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('law_firm_profiles', function (Blueprint $table) {
            $table->string('dti_sec_registration_doc')->nullable()->after('logo');
            $table->string('business_permit_doc')->nullable()->after('dti_sec_registration_doc');
            $table->string('valid_id_doc')->nullable()->after('business_permit_doc');
            $table->text('ibp_details')->nullable()->after('valid_id_doc');
        });
    }

    public function down(): void
    {
        Schema::table('law_firm_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'dti_sec_registration_doc',
                'business_permit_doc',
                'valid_id_doc',
                'ibp_details',
            ]);
        });
    }
};
