<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class FinancialSetting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'description'];

    protected static string $cachePrefix = 'fin_setting_';

    /**
     * Get a setting value by key, with optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember(static::$cachePrefix . $key, 3600, function () use ($key, $default) {
            $record = static::where('key', $key)->first();
            return $record ? $record->value : $default;
        });
    }

    /**
     * Set (upsert) a setting value and bust cache.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget(static::$cachePrefix . $key);
    }

    /**
     * Save multiple settings at once.
     */
    public static function setMany(array $pairs): void
    {
        foreach ($pairs as $key => $value) {
            static::set($key, $value);
        }
    }

    /**
     * Return the configured default GST/WHT rate as float.
     */
    public static function defaultGstRate(): float
    {
        return (float) static::get('default_gst_rate', 15);
    }

    /**
     * Check if FBR advanced mode is enabled.
     */
    public static function fbrModeEnabled(): bool
    {
        return (bool) static::get('fbr_mode_enabled', false);
    }

    /**
     * Fiscal year start month (7 = July for Pakistan).
     */
    public static function fyStartMonth(): int
    {
        return (int) static::get('fiscal_year_start', 7);
    }
}
