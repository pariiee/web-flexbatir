<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');           // training_load | recovery | pace_trend | weekly_summary | streak
            $table->string('title');
            $table->text('body');
            $table->string('severity')->default('info'); // info | tip | warning | achievement
            $table->string('action_label')->nullable();
            $table->string('action_route')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insights');
    }
};
