<?php

namespace App\Traits;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

trait  TranslationHelper
{
    use Translatable;

    protected function getTranslationByLocaleKey(string $key, $city_id = 0): ?Model
    {

        if (
            $this->relationLoaded('translation')
            && $this->translation
            && $this->translation->getAttribute($this->getLocaleKey()) == $key
        ) {
            return $this->translation;
        }
        if ($city_id) {
            $translation = $this->translations->where($this->getLocaleKey(), $key)->where('city_id', $city_id)->first();
        } else {
            $translation = $this->translations->where($this->getLocaleKey(), $key)->where('city_id', request()->header('city-id') ?? 0)->first();
        }
        if (!$translation) {
            $translation = $this->translations->where($this->getLocaleKey(), $key)->first();
        }
        return $translation;
    }

    public function tr($locale, $city_id = 0, $withFallback = null)
    {

        return $this->getTranslation($locale, $city_id, $withFallback);
    }

    public function translatOrFallBack($city_id = 0)
    {
        return $this->getTranslation(null, $city_id, true);
    }

    public function getTranslation(?string $locale = null, $city_id = 0, bool $withFallback = null): ?Model
    {

        $configFallbackLocale = $this->getFallbackLocale();
        $locale = $locale ?: $this->locale();
        $withFallback = $withFallback === null ? $this->useFallback() : $withFallback;

        $fallbackLocale = $this->getFallbackLocale($locale);

        if ($translation = $this->getTranslationByLocaleKey($locale, $city_id)) {
            return $translation;
        }

        if ($withFallback && $fallbackLocale) {
            if ($translation = $this->getTranslationByLocaleKey($fallbackLocale, $city_id)) {
                return $translation;
            }

            if (
                is_string($configFallbackLocale)
                && $fallbackLocale !== $configFallbackLocale
                && $translation = $this->getTranslationByLocaleKey($configFallbackLocale, $city_id)
            ) {
                return $translation;
            }
        }

        if ($withFallback && $configFallbackLocale === null) {
            $configuredLocales = $this->getLocalesHelper()->all();

            foreach ($configuredLocales as $configuredLocale) {
                if (
                    $locale !== $configuredLocale
                    && $fallbackLocale !== $configuredLocale
                    && $translation = $this->getTranslationByLocaleKey($configuredLocale, $city_id)
                ) {
                    return $translation;
                }
            }
        }

        return null;
    }


}
