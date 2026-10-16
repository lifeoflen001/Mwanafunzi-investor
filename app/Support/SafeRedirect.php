<?php

namespace App\Support;

class SafeRedirect
{
    public static function normalize(?string $destination, string $default = '/'): string
    {
        $destination = trim((string) $destination);

        if ($destination === '' || ! str_starts_with($destination, '/') || str_starts_with($destination, '//') || str_contains($destination, '\\')) {
            return $default;
        }

        return $destination;
    }
}
