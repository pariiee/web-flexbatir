<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'username', 'email', 'password', 'avatar', 'bio', 'gender', 'birth_date', 'weight', 'height', 'location', 'website', 'measurement_preference', 'is_private', 'is_admin', 'is_banned', 'ban_reason', 'banned_at', 'is_verified'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'banned_at'         => 'datetime',
            'password'          => 'hashed',
            'is_admin'          => 'boolean',
            'is_banned'         => 'boolean',
        ];
    }

    /// User yang mengikuti user ini
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'follows',
            'following_id',
            'follower_id'
        )->withTimestamps();
    }

    /// User yang diikuti oleh user ini
    public function following(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'follows',
            'follower_id',
            'following_id'
        )->withTimestamps();
    }

    public function getFollowersCountAttribute(): int
    {
        return $this->followers()->count();
    }

    public function getFollowingCountAttribute(): int
    {
        return $this->following()->count();
    }

    public function gears(): HasMany
    {
        return $this->hasMany(Gear::class);
    }

    /**
     * Normalize avatar ke URL yang bisa langsung dipakai di <img src>.
     * Handle dua format:
     *   - path relatif: "avatars/filename.jpg"  (dari Flutter API)
     *   - URL lengkap:  "/storage/avatars/..."  (dari web upload)
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->avatar) return null;

        // Sudah URL lengkap (http/https atau /storage/...)
        if (str_starts_with($this->avatar, 'http') || str_starts_with($this->avatar, '/')) {
            return $this->avatar;
        }

        // Path relatif → convert ke Storage URL
        return Storage::url($this->avatar);
    }
}
