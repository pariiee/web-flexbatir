<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'is_encrypted', 'label', 'group'];

    protected $casts = [
        'is_encrypted' => 'boolean',
    ];

    // ── Static Helpers ────────────────────────────────────────────────────────

    /**
     * Ambil value setting berdasarkan key.
     * Jika is_encrypted = true, otomatis dekripsi.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        if ($setting->is_encrypted && $setting->value) {
            try {
                return Crypt::decryptString($setting->value);
            } catch (\Exception) {
                return $default;
            }
        }

        return $setting->value;
    }

    /**
     * Simpan atau update setting.
     * Jika $encrypt = true, value akan dienkripsi sebelum disimpan.
     */
    public static function set(string $key, mixed $value, bool $encrypt = false): static
    {
        $storedValue = $encrypt
            ? Crypt::encryptString((string) $value)
            : $value;

        return static::updateOrCreate(
            ['key' => $key],
            [
                'value'        => $storedValue,
                'is_encrypted' => $encrypt,
            ]
        );
    }

    /**
     * Ambil semua settings dalam satu group, sudah didekripsi.
     * Return array ['key' => 'value'].
     */
    public static function getGroup(string $group): array
    {
        return static::where('group', $group)
            ->get()
            ->mapWithKeys(function (self $s) {
                $val = $s->is_encrypted && $s->value
                    ? self::safeDecrypt($s->value)
                    : $s->value;

                return [$s->key => $val];
            })
            ->toArray();
    }

    // ── Private ───────────────────────────────────────────────────────────────

    private static function safeDecrypt(string $value): ?string
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception) {
            return null;
        }
    }
}
