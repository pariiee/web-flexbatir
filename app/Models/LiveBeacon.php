<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveBeacon extends Model
{
    protected $fillable = [
        'user_id',
        'active_token',
        'is_active',
        'last_lat',
        'last_lng',
        'battery_level',
        'expires_at',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'last_lat'    => 'float',
        'last_lng'    => 'float',
        'expires_at'  => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
