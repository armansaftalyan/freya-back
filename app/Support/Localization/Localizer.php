<?php

declare(strict_types=1);

namespace App\Support\Localization;

class Localizer
{
    public static function string(mixed $translations, ?string $fallback = null): ?string
    {
        $resolved = self::pickValue(self::normalizeMap($translations));

        if (is_string($resolved) && $resolved !== '') {
            return $resolved;
        }

        return $fallback;
    }

    /** @return array<int|string, mixed> */
    public static function array(mixed $translations, array $fallback = []): array
    {
        $resolved = self::pickValue(self::normalizeMap($translations));

        if (is_array($resolved)) {
            return $resolved;
        }

        return $fallback;
    }

    /** @return array<string, mixed> */
    private static function normalizeMap(mixed $translations): array
    {
        if (is_array($translations)) {
            return $translations;
        }

        if (! is_string($translations) || $translations === '') {
            return [];
        }

        $decoded = json_decode($translations, true);

        return is_array($decoded) ? $decoded : [];
    }

    private static function pickValue(array $translations): mixed
    {
        if ($translations === []) {
            return null;
        }

        $locale = (string) app()->getLocale();
        $fallbackLocale = (string) config('localization.fallback_locale', config('app.fallback_locale', 'en'));

        foreach ([$locale, $fallbackLocale, 'en', 'ru', 'hy'] as $key) {
            if (array_key_exists($key, $translations)) {
                $value = $translations[$key];
                if ($value !== null && $value !== '') {
                    return $value;
                }
            }
        }

        foreach ($translations as $value) {
            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return null;
    }
}
