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
        Schema::create('payment_risk_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('lawyer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('consultation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->string('context')->default('consultation_booking');
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('currency', 3)->default('PHP');
            $table->unsignedInteger('risk_score')->default(0);
            $table->enum('risk_level', ['low', 'medium', 'high'])->default('low');
            $table->enum('recommendation', ['allow', 'review', 'block'])->default('allow');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('email')->nullable();
            $table->json('flags')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_risk_events');
    }
};
