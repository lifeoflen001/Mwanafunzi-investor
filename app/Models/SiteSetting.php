<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value', 'type'];
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        if (! $setting) return $default;
        return match ($setting->type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }
}
