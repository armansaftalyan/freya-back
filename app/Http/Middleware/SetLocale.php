<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = (array) config('localization.supported_locales', ['ru', 'en', 'hy']);
        $fallbackLocale = (string) config('localization.fallback_locale', config('app.fallback_locale', 'en'));

        $locale = $this->resolveLocale($request, $supportedLocales, $fallbackLocale);

        app()->setLocale($locale);

        if ($request->hasSession()) {
            $request->session()->put('locale', $locale);
        }

        return $next($request);
    }

    /** @param array<int, string> $supportedLocales */
    private function resolveLocale(Request $request, array $supportedLocales, string $fallbackLocale): string
    {
        $sessionLocale = $request->hasSession() ? $request->session()->get('locale') : null;

        $candidates = [
            $request->query('lang'),
            $request->header('X-Locale'),
            $request->cookie('app_locale'),
            $sessionLocale,
            $request->getPreferredLanguage($supportedLocales),
        ];

        foreach ($candidates as $candidate) {
            $locale = $this->normalizeLocale($candidate);

            if ($locale !== null && in_array($locale, $supportedLocales, true)) {
                return $locale;
            }
        }

        return $fallbackLocale;
    }

    private function normalizeLocale(mixed $locale): ?string
    {
        if (! is_string($locale) || $locale === '') {
            return null;
        }

        $normalized = strtolower(str_replace('_', '-', trim($locale)));

        if (str_contains($normalized, '-')) {
            $normalized = explode('-', $normalized)[0];
        }

        return $normalized;
    }
}
