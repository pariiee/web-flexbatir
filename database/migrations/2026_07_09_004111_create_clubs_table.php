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
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();

            // Info dasar
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('location')->nullable();
            $table->string('website')->nullable();

            // Tipe olahraga
            $table->enum('sport_type', [
                'run', 'ride', 'swim', 'walk', 'hike', 'multisport', 'other'
            ])->default('other');

            // Pengaturan
            $table->enum('privacy', ['public', 'private'])->default('public');
            $table->unsignedInteger('members_count')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clubs');
    }
};
