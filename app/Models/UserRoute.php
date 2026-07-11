<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRoute extends Model
{
    protected $fillable = [
        'user_id', 'name', 'description', 'type',
        'distance', 'elevation_gain', 'elevation_loss',
        'waypoints', 'map_image',
        'start_lat', 'start_lng', 'end_lat', 'end_lng',
        'estimated_duration', 'estimated_calories',
        'is_public', 'times_used', 'difficulty',
    ];

    protected $casts = [
        'waypoints'          => 'array',
        'is_public'          => 'boolean',
        'distance'           => 'float',
        'elevation_gain'     => 'float',
        'elevation_loss'     => 'float',
        'start_lat'          => 'float',
        'start_lng'          => 'float',
        'end_lat'            => 'float',
        'end_lng'            => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get distance in km.
     */
    public function getDistanceKmAttribute(): ?float
    {
        return $this->distance ? round($this->distance / 1000, 2) : null;
    }
}
