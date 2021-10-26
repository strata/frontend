<?php

namespace Strata\Frontend;

use Strata\Frontend\Exception\InvalidLocaleException;

/**
 * Class to manage site-wide settings including locale
 */
class Site
{
    const DIRECTION_LTR = 'ltr';
    const DIRECTION_RTL = 'rtl';
    const TEXT_DIRECTION = 'textDirection';
    const DATA = 'data';

    private ?string $locale = null;
    private array $locales = [];
    private ?string $defaultLocale = null;

    /**
     * Add a locale for this site (defaults to LTR)
     * @param string $locale
     * @param array $data Array of key => value data for this locale (e.g. site_id)
     * @param string $direction
     * @throws InvalidLocaleException
     */
    public function addLocale(string $locale, array $data = [], string $direction = self::DIRECTION_LTR)
    {
        if (!in_array($direction, [self::DIRECTION_LTR, self::DIRECTION_RTL])) {
            throw new \InvalidArgumentException(sprintf('Text direction %s is invalid', $direction));
        }
        $this->locales[$locale] = [
            self::TEXT_DIRECTION => $direction,
            self::DATA => $data,
        ];
    }

    /**
     * Add locale and set it as the default locale for this site
     * @param string $locale
     * @param array $data
     * @param string $direction
     * @throws InvalidLocaleException
     */
    public function addDefaultLocale(string $locale, array $data = [], string $direction = self::DIRECTION_LTR)
    {
        $this->addLocale($locale, $data, $direction);
        $this->defaultLocale = $locale;
    }

    /**
     * Add RTF locale for this site
     * @param string $locale
     * @param array $data
     * @throws InvalidLocaleException
     */
    public function addLocaleRtl(string $locale, array $data = [])
    {
        $this->addLocale($locale, $data, self::DIRECTION_RTL);
    }

    /**
     * Return whether a locale is setup for this site
     * @param string $locale
     * @return bool
     */
    public function isValidLocale(string $locale): bool
    {
        return array_key_exists($locale, $this->locales);
    }

    /**
     * Set locale this site currently uses
     * @param string $locale
     */
    public function setLocale(string $locale)
    {
        if (!$this->isValidLocale($locale)) {
            throw new InvalidLocaleException(sprintf('Locale %s is not setup for this site, please add via Site::addLocale()', $locale));
        }
        $this->locale = $locale;
    }

    /**
     * Return current site locale
     * @return string
     * @throw InvalidLocaleException
     */
    public function getLocale(): string
    {
        if ($this->locale === null && $this->defaultLocale !== null) {
            $this->setLocale($this->defaultLocale);
        }
        if ($this->locale === null) {
            throw new InvalidLocaleException('Cannot return current locale, set via Site::setLocale() or add a default locale via Site::addDefaultLocale()');
        }
        return $this->locale;
    }

    /**
     * Set key => value data for this locale
     *
     * You must add the locale first before using this method
     *
     * @param string $locale
     * @param string $name
     * @param $value
     * @throws InvalidLocaleException
     */
    public function setLocaleData(string $locale, string $name, $value)
    {
        if (!isset($this->locales[$locale])) {
            throw new InvalidLocaleException(sprintf('You must first add locale "%s" via Site::addLocale()', $locale));
        }
        $this->locales[$locale][self::DATA][$name] = $value;
    }

    /**
     * Return current locale data
     * @param string|null $name Named data value, or all data values if null
     * @return mixed|null Array of all locale data, requested locale attribute, or null if not set
     * @throws InvalidLocaleException
     */
    public function getLocaleData(?string $name = null)
    {
        $currentLocale = $this->getLocale();
        if ($currentLocale === null) {
            throw new InvalidLocaleException('Current locale not set, please ensure you set this via Site::setLocale()');
        }
        if ($name === null) {
            return $this->locales[$currentLocale][self::DATA];
        }
        $data = $this->locales[$currentLocale][self::DATA];
        if (array_key_exists($name, $data)) {
            return $data[$name];
        }
        return null;
    }

    /**
     * Return data values for all locales, optionally excluding data for the current locale
     *
     * Returns data in an array format locale => data value, e.g.
     * [
     *   'en' => 'value',
     *   'fr' => 'value'
     * ]
     *
     * @param string $name
     * @param bool $excludeCurrentLocale
     * @return array
     */
    public function getData(string $name, bool $excludeCurrentLocale = false): array
    {
        $currentLocale = $this->getLocale();
        $data = [];
        foreach ($this->locales as $locale => $item) {
            if ($excludeCurrentLocale && $locale === $currentLocale) {
                continue;
            }
            if (isset($item[self::DATA][$name])) {
                $data[$locale] = $item[self::DATA][$name];
            }
        }
        return $data;
    }

    /**
     * Magic method to allow property access for current locale data
     * @param string $name
     * @return mixed|null
     * @throws InvalidLocaleException
     */
    public function __get(string $name)
    {
        return $this->getLocaleData($name);
    }

    /**
     * Return text direction for current locale
     * @param ?string $locale Optionally pass the locale you want to return text direction for, or uses current locale
     * @return string
     * @throws InvalidLocaleException
     */
    public function getTextDirection(?string $locale = null): string
    {
        if ($locale === null) {
            $locale = $this->getLocale();
        }
        if ($locale === null) {
            throw new InvalidLocaleException('Current locale not set, please ensure you set this via Site::setLocale()');
        }
        if (!$this->isValidLocale($locale)) {
            throw new InvalidLocaleException(sprintf('Locale "%s" not set, please ensure you add this via Site::addLocale() or Site::addLocaleRtl()', $locale));
        }
        return $this->locales[$locale][self::TEXT_DIRECTION];
    }

    /**
     * Return text direction HTML attribute for current locale
     * @param ?string $locale Optionally pass the locale you want to return text direction for, or uses current locale
     * @return string|null
     * @throws InvalidLocaleException
     */
    public function getTextDirectionHtml(?string $locale = null): ?string
    {
        $direction = $this->getTextDirection($locale);
        if ($direction === self::DIRECTION_LTR) {
            return '';
        }
        return sprintf('dir="%s"', $direction);
    }

}
