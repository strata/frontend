<?php

namespace Strata\Frontend;

use Symfony\Component\Intl\Locales;
use Strata\Frontend\Exception\InvalidLocaleException;

/**
 * Class to manage site-wide settings including locale
 *
 * @see https://symfony.com/doc/current/components/intl.html
 */
class Site
{
    const DIRECTION_LTR = 'ltr';
    const DIRECTION_RTL = 'rtl';
    private ?string $locale = null;
    private array $locales = [];

    /**
     * Add a LTR locale for this site
     * @param string $locale
     * @param array $attributes Array of key => value attributes for this locale (e.g. site_id)
     * @param string $direction
     * @throws InvalidLocaleException
     */
    public function addLocale(string $locale, array $attributes = [], string $direction = self::DIRECTION_LTR)
    {
        if (!Locales::exists($locale)) {
            throw new InvalidLocaleException(sprintf('Locale %s is not recognised', $locale));
        }
        if (!in_array($direction, [self::DIRECTION_LTR, self::DIRECTION_RTL])) {
            throw new \InvalidArgumentException(sprintf('Text direction %s is invalid', $direction));
        }
        $this->locales[$locale] = [
            'textDirection' => $direction,
            'attributes' => $attributes,
        ];
    }

    /**
     * Add RTF locale for this site
     * @param string $locale
     * @param array $attributes
     * @throws InvalidLocaleException
     */
    public function addRtfLocale(string $locale, array $attributes = [])
    {
        $this->addLocale($locale, $attributes, self::DIRECTION_RTL);
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
        \Locale::setDefault($locale);
    }

    /**
     * Return site locale
     * @return string
     * @throws InvalidLocaleException
     */
    public function getLocale(): string
    {
        if ($this->locale === null) {
            throw new InvalidLocaleException('Locale not set for this site');
        }
        return $this->locale;
    }

    /**
     * Return locale data
     * @param string|null $attribute
     * @return mixed|null Array of locale data, or requested locale attribute, or null if not set
     * @throws InvalidLocaleException
     */
    public function getLocaleData(?string $attribute = null)
    {
        $locale = $this->getLocale();
        if ($attribute === null) {
            return $this->locales[$locale];
        }
        $attributes = $this->locales[$locale]['attributes'];
        if (array_key_exists($attribute, $attributes)) {
            return $attributes[$attribute];
        }
        return null;
    }

    /**
     * Magic method to allow property access on locale attributes
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
     * @return string
     * @throws InvalidLocaleException
     */
    public function getTextDirection(): string
    {
        $data = $this->getLocaleData();
        return $data['textDirection'];
    }

    /**
     * Return text direction HTML attribute for current locale
     * @return string|null
     * @throws InvalidLocaleException
     */
    public function getTextDirectionHtml(): ?string
    {
        $direction = $this->getTextDirection();
        if ($direction === self::DIRECTION_LTR) {
            return '';
        }
        return sprintf('dir="%s"', $direction);
    }
}