<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Challenge extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description', 'cover_image',
        'type', 'sport_type', 'target_value', 'unit',
        'start_date', 'end_date', 'is_public', 'participants_count',
    ];

    protected $casts = [
        'start_date'   => 'date',
        'end_date'     => 'date',
        'target_value' => 'float',
        'is_public'    => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ChallengeParticipant::class);
    }
}
