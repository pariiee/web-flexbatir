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
        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // pembuat tantangan

            // Info dasar
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->enum('type', ['distance', 'duration', 'calories', 'activities', 'elevation'])->default('distance');
            $table->enum('sport_type', ['run', 'ride', 'swim', 'walk', 'hike', 'any'])->default('any');

            // Target
            $table->decimal('target_value', 10, 2);
            $table->string('unit')->nullable();

            // Periode
            $table->date('start_date');
            $table->date('end_date');

            // Pengaturan
            $table->boolean('is_public')->default(true);
            $table->unsignedInteger('participants_count')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenges');
    }
};
