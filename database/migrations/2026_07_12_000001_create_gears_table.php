<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gears', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->enum('type', ['shoes', 'bike', 'helmet', 'watch', 'vest', 'other'])->default('other');
            $table->text('description')->nullable();
            $table->decimal('distance_km', 10, 2)->default(0);
            $table->unsignedSmallInteger('purchase_year')->nullable();
            $table->boolean('is_retired')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gears');
    }
};
