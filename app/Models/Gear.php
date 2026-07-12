<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gear extends Model
{
    protected $fillable = [
        'user_id', 'name', 'brand', 'model', 'type',
        'description', 'distance_km', 'purchase_year', 'is_retired',
    ];

    protected $casts = [
        'distance_km' => 'float',
        'is_retired'  => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
