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
        // Extend status enum to include downpayment_paid
        \DB::statement("ALTER TABLE payments MODIFY COLUMN status ENUM('paid','pending','refunded','downpayment_paid') NOT NULL DEFAULT 'pending'");

        Schema::table('payments', function (Blueprint $table) {
            $table->enum('type', ['downpayment', 'balance', 'full'])->default('full')->after('status');
            $table->decimal('firm_cut', 10, 2)->default(0)->after('type');
            $table->decimal('lawyer_net', 10, 2)->default(0)->after('firm_cut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['type', 'firm_cut', 'lawyer_net']);
        });

        \DB::statement("ALTER TABLE payments MODIFY COLUMN status ENUM('paid','pending','refunded') NOT NULL DEFAULT 'pending'");
    }
};
