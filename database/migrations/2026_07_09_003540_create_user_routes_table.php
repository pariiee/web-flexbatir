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
        Schema::create('user_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Info dasar
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', [
                'run', 'ride', 'swim', 'walk', 'hike', 'other'
            ])->default('other');

            // Data rute
            $table->decimal('distance', 10, 2)->nullable();       // meter
            $table->decimal('elevation_gain', 8, 2)->nullable();  // meter
            $table->decimal('elevation_loss', 8, 2)->nullable();  // meter
            $table->json('waypoints')->nullable();                 // array koordinat [{lat, lng, ele}]
            $table->string('map_image')->nullable();              // thumbnail peta

            // Titik awal & akhir
            $table->decimal('start_lat', 10, 7)->nullable();
            $table->decimal('start_lng', 10, 7)->nullable();
            $table->decimal('end_lat', 10, 7)->nullable();
            $table->decimal('end_lng', 10, 7)->nullable();

            // Estimasi
            $table->unsignedInteger('estimated_duration')->nullable(); // detik
            $table->unsignedInteger('estimated_calories')->nullable();

            // Pengaturan
            $table->boolean('is_public')->default(true);
            $table->unsignedInteger('times_used')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_routes');
    }
};
