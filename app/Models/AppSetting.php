<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AppSetting extends Model
{
    protected $fillable = [
        'setting_key',
        'setting_value',
        'description',
    ];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = Cache::remember("app_setting_{$key}", 300, function () use ($key) {
            return static::query()->where('setting_key', $key)->first();
        });

        if (! $setting) {
            return $default;
        }

        return $setting->setting_value ?? $default;
    }

    public static function setValue(string $key, mixed $value, ?string $description = null): self
    {
        $setting = static::query()->updateOrCreate(
            ['setting_key' => $key],
            [
                'setting_value' => is_array($value) ? json_encode($value) : (string) $value,
                'description' => $description,
            ]
        );

        Cache::forget("app_setting_{$key}");

        return $setting;
    }

    public static function getFloat(string $key, float $default = 0.0): float
    {
        return (float) static::getValue($key, $default);
    }
}
