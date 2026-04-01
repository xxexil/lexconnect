<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('law_firm_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('firm_name');
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->year('founded_year')->nullable();
            $table->enum('firm_size', ['solo', 'small', 'medium', 'large'])->default('small');
            $table->json('specialties')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->string('logo')->nullable();
            $table->decimal('rating', 3, 1)->default(0);
            $table->integer('reviews_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('law_firm_profiles');
    }
};
