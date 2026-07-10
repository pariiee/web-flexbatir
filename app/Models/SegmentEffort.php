<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SegmentEffort extends Model
{
    protected $fillable = [
        'segment_id', 'user_id', 'activity_id',
        'started_at', 'ended_at', 'elapsed_time',
        'average_speed', 'max_speed',
        'average_heart_rate', 'max_heart_rate',
        'rank', 'is_kom',
    ];

    protected $casts = [
        'started_at'    => 'datetime',
        'ended_at'      => 'datetime',
        'is_kom'        => 'boolean',
        'average_speed' => 'float',
        'max_speed'     => 'float',
    ];

    public function segment(): BelongsTo
    {
        return $this->belongsTo(Segment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * Get elapsed time formatted as HH:MM:SS.
     */
    public function getElapsedTimeFormattedAttribute(): string
    {
        $hours   = floor($this->elapsed_time / 3600);
        $minutes = floor(($this->elapsed_time % 3600) / 60);
        $seconds = $this->elapsed_time % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
}
