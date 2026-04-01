<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paymongo_child_merchants', function (Blueprint $table) {
            $table->id();
            $table->morphs('owner');
            $table->string('provider')->default('paymongo');
            $table->string('merchant_type');
            $table->string('status')->default('not_started');
            $table->string('onboarding_mode')->default('hosted');
            $table->string('paymongo_child_account_id')->nullable();
            $table->string('hosted_onboarding_url')->nullable();
            $table->json('requirements_payload')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();

            $table->unique(['owner_type', 'owner_id', 'provider'], 'paymongo_child_merchants_owner_provider_unique');
            $table->index(['status', 'merchant_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paymongo_child_merchants');
    }
};
