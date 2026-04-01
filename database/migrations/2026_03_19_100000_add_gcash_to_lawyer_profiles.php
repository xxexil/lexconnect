<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('lawyer_profiles', function (Blueprint $table) {
            $table->string('gcash_number')->nullable()->after('location');
            $table->string('gcash_qr')->nullable()->after('gcash_number');
        });
    }
    public function down(): void {
        Schema::table('lawyer_profiles', function (Blueprint $table) {
            $table->dropColumn(['gcash_number','gcash_qr']);
        });
    }
};
