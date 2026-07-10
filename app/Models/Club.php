<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Club extends Model
{
    protected $fillable = [
        'owner_id', 'name', 'slug', 'description', 'logo',
        'cover_image', 'location', 'website', 'sport_type',
        'privacy', 'members_count',
    ];

    protected $casts = [
        'members_count' => 'integer',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(ClubMember::class);
    }

    public function approvedMembers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'club_members')
            ->wherePivot('status', 'approved')
            ->withPivot('role', 'status')
            ->withTimestamps();
    }
}
