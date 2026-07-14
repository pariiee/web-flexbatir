<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('value')->nullable();          // disimpan terenkripsi via Model cast
            $table->boolean('is_encrypted')->default(false); // flag apakah value dienkripsi
            $table->string('label')->nullable();             // label untuk ditampilkan di UI
            $table->string('group')->default('general');     // pengelompokan: general, ai, etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
