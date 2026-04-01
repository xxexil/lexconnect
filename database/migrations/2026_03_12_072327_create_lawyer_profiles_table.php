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
        Schema::create('lawyer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('specialty');
            $table->string('firm')->nullable();
            $table->decimal('hourly_rate', 8, 2)->default(0);
            $table->integer('experience_years')->default(0);
            $table->string('location')->nullable();
            $table->text('bio')->nullable();
            $table->boolean('is_certified')->default(false);
            $table->enum('availability_status', ['available','busy','offline'])->default('available');
            $table->decimal('rating', 3, 1)->default(0);
            $table->integer('reviews_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lawyer_profiles');
    }
};
