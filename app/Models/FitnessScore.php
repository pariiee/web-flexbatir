<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FitnessScore extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'training_stress_score',
        'fatigue_level',
        'fitness_level',
        'form_score',
        'ai_recommendation',
        'ai_model_used',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
