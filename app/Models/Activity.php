<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description', 'type',
        'started_at', 'ended_at', 'duration',
        'distance', 'average_speed', 'max_speed',
        'elevation_gain', 'elevation_loss',
        'calories', 'average_heart_rate', 'max_heart_rate',
        'gps_data', 'file_path', 'is_public', 'source',
    ];

    protected $casts = [
        'started_at'          => 'datetime',
        'ended_at'            => 'datetime',
        'gps_data'            => 'array',
        'is_public'           => 'boolean',
        'distance'            => 'float',
        'average_speed'       => 'float',
        'max_speed'           => 'float',
        'elevation_gain'      => 'float',
        'elevation_loss'      => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get duration formatted as HH:MM:SS.
     */
    public function getDurationFormattedAttribute(): ?string
    {
        if (!$this->duration) return null;

        $hours   = floor($this->duration / 3600);
        $minutes = floor(($this->duration % 3600) / 60);
        $seconds = $this->duration % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    /**
     * Get distance in km.
     */
    public function getDistanceKmAttribute(): ?float
    {
        return $this->distance ? round($this->distance / 1000, 2) : null;
    }
}
