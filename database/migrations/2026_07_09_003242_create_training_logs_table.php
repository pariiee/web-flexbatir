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
        Schema::create('training_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('activity_id')->nullable()->constrained()->nullOnDelete();

            // Info dasar
            $table->date('date');
            $table->string('title');
            $table->text('notes')->nullable();
            $table->enum('status', ['planned', 'completed', 'skipped'])->default('planned');

            // Tipe latihan
            $table->enum('type', [
                'run', 'ride', 'swim', 'walk', 'hike',
                'workout', 'yoga', 'crossfit', 'other'
            ])->default('other');

            // Target latihan
            $table->unsignedInteger('planned_duration')->nullable();  // detik
            $table->decimal('planned_distance', 10, 2)->nullable();   // meter

            // Hasil aktual
            $table->unsignedInteger('actual_duration')->nullable();   // detik
            $table->decimal('actual_distance', 10, 2)->nullable();    // meter
            $table->unsignedInteger('actual_calories')->nullable();

            // Perasaan & intensitas
            $table->unsignedTinyInteger('perceived_effort')->nullable(); // 1-10
            $table->unsignedTinyInteger('mood')->nullable();             // 1-5

            $table->timestamps();

            // Satu user hanya bisa punya satu log per tanggal per tipe
            $table->unique(['user_id', 'date', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_logs');
    }
};
