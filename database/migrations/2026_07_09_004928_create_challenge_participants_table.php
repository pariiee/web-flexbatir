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
        Schema::create('challenge_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenge_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Progress
            $table->decimal('current_value', 10, 2)->default(0);
            $table->decimal('target_value', 10, 2)->default(0);
            $table->unsignedTinyInteger('progress_percent')->default(0); // 0-100

            // Status & ranking
            $table->enum('status', ['active', 'completed', 'failed'])->default('active');
            $table->unsignedInteger('rank')->nullable();

            $table->timestamps();

            $table->unique(['challenge_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenge_participants');
    }
};
