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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('name');
            $table->string('avatar')->nullable()->after('email');
            $table->text('bio')->nullable()->after('avatar');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('bio');
            $table->date('birth_date')->nullable()->after('gender');
            $table->decimal('weight', 5, 2)->nullable()->after('birth_date'); // kg
            $table->decimal('height', 5, 2)->nullable()->after('weight');     // cm
            $table->string('location')->nullable()->after('height');
            $table->string('website')->nullable()->after('location');
            $table->enum('measurement_preference', ['metric', 'imperial'])->default('metric')->after('website');
            $table->boolean('is_private')->default(false)->after('measurement_preference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username', 'avatar', 'bio', 'gender', 'birth_date',
                'weight', 'height', 'location', 'website',
                'measurement_preference', 'is_private',
            ]);
        });
    }
};
