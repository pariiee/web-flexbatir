<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RouteBookmark extends Model
{
    protected $fillable = ['user_route_id', 'user_id'];

    public function route(): BelongsTo
    {
        return $this->belongsTo(UserRoute::class, 'user_route_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
