<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    // ── Cache key ────────────────────────────────────────────────────

    private const CACHE_KEY = 'app_settings';

    private const CACHE_TTL = 3600; // 1 hour

    // ── Static helpers ────────────────────────────────────────────────

    /**
     * Get a setting value, falling back to $default if not set.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = static::allCached();

        return $settings[$key] ?? $default;
    }

    /**
     * Set (upsert) a single setting value and clear cache.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Bulk-upsert an array of key=>value settings and clear cache.
     *
     * @param  array<string, mixed>  $settings
     */
    public static function setMany(array $settings): void
    {
        foreach ($settings as $key => $value) {
            static::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Return all settings as a flat key=>value array, cached for 1 hour.
     *
     * @return array<string, mixed>
     */
    public static function allCached(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, fn () => static::pluck('value', 'key')->all());
    }
}
