<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Throwable;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value', 'type'];

    private static ?array $runtimeValues = null;

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::allValues()[$key] ?? null;
        if (! $setting) return $default;

        return match ($setting['type']) {
            'boolean' => filter_var($setting['value'], FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($setting['value'], true),
            'encrypted' => static::decrypt($setting['value'], $default),
            default => $setting['value'],
        };
    }

    private static function decrypt(string $value, mixed $default): mixed
    {
        try {
            return Crypt::decryptString($value);
        } catch (Throwable) {
            return $default;
        }
    }

    public static function valuesForKeys(array $keys): array
    {
        return array_intersect_key(static::allValues(), array_flip($keys));
    }

    public static function forgetCache(): void
    {
        static::$runtimeValues = null;
        Cache::forget('site_settings.values.v1');
    }

    private static function allValues(): array
    {
        if (static::$runtimeValues !== null) return static::$runtimeValues;

        $load = fn (): array => static::query()
            ->get(['key', 'value', 'type'])
            ->mapWithKeys(fn (self $setting) => [$setting->key => [
                'value' => $setting->value,
                'type' => $setting->type,
            ]])
            ->all();

        static::$runtimeValues = app()->environment('testing')
            ? $load()
            : Cache::remember('site_settings.values.v1', now()->addMinutes(5), $load);

        return static::$runtimeValues;
    }
}
