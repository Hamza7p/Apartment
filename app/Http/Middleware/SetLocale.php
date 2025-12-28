<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = $this->extractLocale($request);

        if (! in_array($locale, ['en', 'ar'])) {
            $locale = config('app.locale', 'en');
        }

        app()->setLocale($locale);

        return $next($request);
    }

    /**
     * Extract locale from Accept-Language header.
     * Handles formats like: "ar", "ar,en", "ar-EG,ar;q=0.9,en;q=0.8"
     */
    private function extractLocale(Request $request): ?string
    {
        $acceptLanguage = $request->header('Accept-Language');

        if (empty($acceptLanguage)) {
            return null;
        }

        // Handle case-insensitive header
        $acceptLanguage = strtolower($acceptLanguage);

        // Split by comma and get the first language code
        $parts = explode(',', $acceptLanguage);
        $firstPart = trim($parts[0]);

        // Extract language code (e.g., "ar-EG" -> "ar", "ar;q=0.9" -> "ar")
        $locale = explode('-', $firstPart)[0];
        $locale = explode(';', $locale)[0];
        $locale = trim($locale);

        // Normalize to lowercase
        return $locale ? strtolower($locale) : null;
    }
}
