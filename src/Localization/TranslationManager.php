<?php

namespace Rhaima\VoltPanel\Localization;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class TranslationManager
{
    protected string $defaultLocale;
    protected array $supportedLocales;
    protected string $translationsPath;

    public function __construct()
    {
        $this->defaultLocale = config('voltpanel.localization.default_locale', 'en');
        $this->supportedLocales = config('voltpanel.localization.supported_locales', ['en']);
        $this->translationsPath = config('voltpanel.localization.path', resource_path('lang/vendor/voltpanel'));
    }

    public function translate(string $key, ?string $locale = null, array $replace = []): string
    {
        $locale = $locale ?? app()->getLocale();

        $translation = $this->getTranslation($key, $locale);

        if ($translation === null) {
            $translation = $this->getTranslation($key, $this->defaultLocale) ?? $key;
        }

        foreach ($replace as $search => $value) {
            $translation = str_replace(':' . $search, $value, $translation);
        }

        return $translation;
    }

    protected function getTranslation(string $key, string $locale): ?string
    {
        $translations = $this->loadTranslations($locale);

        return data_get($translations, $key);
    }

    protected function loadTranslations(string $locale): array
    {
        return Cache::remember("voltpanel.translations.{$locale}", 3600, function () use ($locale) {
            $file = $this->translationsPath . "/{$locale}.json";

            if (!File::exists($file)) {
                return [];
            }

            return json_decode(File::get($file), true) ?? [];
        });
    }

    public function getSupportedLocales(): array
    {
        return $this->supportedLocales;
    }

    public function setLocale(string $locale): void
    {
        if (in_array($locale, $this->supportedLocales)) {
            app()->setLocale($locale);
            session()->put('locale', $locale);
        }
    }

    public function getCurrentLocale(): string
    {
        return session()->get('locale', $this->defaultLocale);
    }
}
