<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("system_setting_{$key}", function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            if (! $setting) {
                return $default;
            }

            return match ($setting->type) {
                'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
                'integer' => (int) $setting->value,
                'json' => json_decode($setting->value, true) ?: [],
                default => $setting->value,
            };
        });
    }

    public static function set(string $key, mixed $value, string $type = 'string', string $group = 'academic', ?string $description = null): self
    {
        $serializedValue = is_array($value) ? json_encode($value) : (is_bool($value) ? ($value ? '1' : '0') : (string) $value);

        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $serializedValue,
                'type' => $type,
                'group' => $group,
                'description' => $description,
            ]
        );

        Cache::forget("system_setting_{$key}");
        Cache::forget('system_settings_all');

        return $setting;
    }

    public static function getAllGrouped(): array
    {
        return Cache::rememberForever('system_settings_all', function () {
            return static::all()->groupBy('group')->toArray();
        });
    }
}
