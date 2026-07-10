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
        Schema::create('segments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // pembuat segmen

            // Info dasar
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['run', 'ride', 'swim', 'walk', 'hike', 'other'])->default('other');

            // Data geografis
            $table->decimal('distance', 10, 2)->nullable();          // meter
            $table->decimal('elevation_gain', 8, 2)->nullable();     // meter
            $table->decimal('elevation_loss', 8, 2)->nullable();     // meter
            $table->decimal('average_grade', 5, 2)->nullable();      // persen
            $table->decimal('maximum_grade', 5, 2)->nullable();      // persen

            // Titik awal & akhir
            $table->decimal('start_lat', 10, 7)->nullable();
            $table->decimal('start_lng', 10, 7)->nullable();
            $table->decimal('end_lat', 10, 7)->nullable();
            $table->decimal('end_lng', 10, 7)->nullable();
            $table->json('polyline')->nullable();                     // array koordinat rute

            // Statistik
            $table->unsignedInteger('effort_count')->default(0);
            $table->unsignedInteger('athlete_count')->default(0);

            $table->boolean('is_public')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('segments');
    }
};
