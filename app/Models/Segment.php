<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Segment extends Model
{
    protected $fillable = [
        'user_id', 'name', 'description', 'type',
        'distance', 'elevation_gain', 'elevation_loss',
        'average_grade', 'maximum_grade',
        'start_lat', 'start_lng', 'end_lat', 'end_lng',
        'polyline', 'effort_count', 'athlete_count', 'is_public',
    ];

    protected $casts = [
        'polyline'       => 'array',
        'is_public'      => 'boolean',
        'distance'       => 'float',
        'elevation_gain' => 'float',
        'elevation_loss' => 'float',
        'average_grade'  => 'float',
        'maximum_grade'  => 'float',
        'start_lat'      => 'float',
        'start_lng'      => 'float',
        'end_lat'        => 'float',
        'end_lng'        => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function efforts(): HasMany
    {
        return $this->hasMany(SegmentEffort::class);
    }

    public function leaderboard(): HasMany
    {
        return $this->hasMany(SegmentEffort::class)
            ->orderBy('elapsed_time')
            ->with('user:id,name,username,avatar');
    }
}
