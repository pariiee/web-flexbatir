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
        Schema::create('segment_efforts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('segment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('activity_id')->nullable()->constrained()->nullOnDelete();

            // Waktu
            $table->dateTime('started_at');
            $table->dateTime('ended_at');
            $table->unsignedInteger('elapsed_time');   // detik

            // Statistik
            $table->decimal('average_speed', 8, 2)->nullable();   // m/s
            $table->decimal('max_speed', 8, 2)->nullable();       // m/s
            $table->unsignedSmallInteger('average_heart_rate')->nullable();
            $table->unsignedSmallInteger('max_heart_rate')->nullable();

            // Ranking
            $table->unsignedInteger('rank')->nullable();          // ranking di leaderboard
            $table->boolean('is_kom')->default(false);            // King/Queen of Mountain

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('segment_efforts');
    }
};
