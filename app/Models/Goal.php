<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Goal extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description', 'type', 'sport_type',
        'period', 'target_value', 'current_value', 'unit',
        'start_date', 'end_date', 'status',
    ];

    protected $casts = [
        'start_date'    => 'date',
        'end_date'      => 'date',
        'target_value'  => 'float',
        'current_value' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get progress percentage.
     */
    public function getProgressPercentAttribute(): float
    {
        if ($this->target_value <= 0) return 0;
        return min(100, round(($this->current_value / $this->target_value) * 100, 1));
    }
}
