<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fitness_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->unsignedSmallInteger('training_stress_score')->default(0); // TSS 0-150+
            $table->unsignedTinyInteger('fatigue_level')->default(0);          // 0-10
            $table->unsignedTinyInteger('fitness_level')->default(0);          // CTL 0-100+
            $table->unsignedTinyInteger('form_score')->nullable();             // TSB = CTL - ATL
            $table->text('ai_recommendation')->nullable();                     // teks analisa AI
            $table->string('ai_model_used')->nullable();                       // model AI yang menghasilkan
            $table->timestamps();

            $table->unique(['user_id', 'date']); // satu record per user per hari
            $table->index(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fitness_scores');
    }
};
