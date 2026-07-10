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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Info dasar
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', [
                'run', 'ride', 'swim', 'walk', 'hike',
                'workout', 'yoga', 'crossfit', 'other'
            ])->default('other');

            // Waktu
            $table->dateTime('started_at');
            $table->dateTime('ended_at')->nullable();
            $table->unsignedInteger('duration')->nullable(); // detik

            // Jarak & kecepatan
            $table->decimal('distance', 10, 2)->nullable();        // meter
            $table->decimal('average_speed', 8, 2)->nullable();    // m/s
            $table->decimal('max_speed', 8, 2)->nullable();        // m/s

            // Elevasi
            $table->decimal('elevation_gain', 8, 2)->nullable();   // meter
            $table->decimal('elevation_loss', 8, 2)->nullable();   // meter

            // Kalori & detak jantung
            $table->unsignedInteger('calories')->nullable();
            $table->unsignedSmallInteger('average_heart_rate')->nullable();
            $table->unsignedSmallInteger('max_heart_rate')->nullable();

            // GPS & file
            $table->json('gps_data')->nullable();                  // array koordinat
            $table->string('file_path')->nullable();               // file GPX/FIT

            // Pengaturan
            $table->boolean('is_public')->default(true);
            $table->enum('source', ['manual', 'upload', 'device'])->default('manual');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
