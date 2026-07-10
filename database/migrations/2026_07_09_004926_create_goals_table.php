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
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Info dasar
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['distance', 'duration', 'calories', 'activities', 'elevation'])->default('distance');
            $table->enum('sport_type', ['run', 'ride', 'swim', 'walk', 'hike', 'any'])->default('any');
            $table->enum('period', ['weekly', 'monthly', 'yearly', 'custom'])->default('monthly');

            // Target & progress
            $table->decimal('target_value', 10, 2);       // nilai target
            $table->decimal('current_value', 10, 2)->default(0); // progress saat ini
            $table->string('unit')->nullable();            // km, menit, kkal, dll

            // Periode
            $table->date('start_date');
            $table->date('end_date');

            // Status
            $table->enum('status', ['active', 'completed', 'failed', 'cancelled'])->default('active');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goals');
    }
};
