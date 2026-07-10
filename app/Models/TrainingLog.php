<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingLog extends Model
{
    protected $fillable = [
        'user_id', 'activity_id', 'date', 'title', 'notes',
        'status', 'type', 'planned_duration', 'planned_distance',
        'actual_duration', 'actual_distance', 'actual_calories',
        'perceived_effort', 'mood',
    ];

    protected $casts = [
        'date'             => 'date',
        'planned_distance' => 'float',
        'actual_distance'  => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }
}
