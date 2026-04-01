<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('lawyer_blocked_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lawyer_id')->constrained('users')->onDelete('cascade');
            $table->date('blocked_date');
            $table->string('reason')->nullable();
            $table->timestamps();
            $table->unique(['lawyer_id', 'blocked_date']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('lawyer_blocked_dates');
    }
};
