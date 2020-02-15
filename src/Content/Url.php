<?php

declare(strict_types=1);

namespace Studio24\Frontend\Content;

use Studio24\Frontend\Exception\UrlException;
use Studio24\Frontend\Content\Field\DateTime;

/**
 * Class to manage URL generation
 *
 * @package Studio24\Frontend\Content
 */
class Url
{
    /**
     * Array of available URL params
     *
     * @var array
     */
    protected $availableParams = [
        'id',
        'slug',
        'date_published',
        'date_modified',
    ];

    /**
     * Array of params this URL pattern has
     *
     * array['param name'] =>
     *   'replace' => 'string to replace in URL pattern'
     *   'options' => [ option => value ]
     *
     * @var array
     */
    protected $params = [];

    /**
     * URL pattern
     *
     * @var string
     */
    protected $pattern;

    /**
     * Constructor
     *
     * @param string $pattern URL pattern to set
     * @throws UrlException
     */
    public function __construct(string $pattern = null)
    {
        if ($pattern !== null) {
            $this->setPattern($pattern);
        }
    }

    /**
     * Set URL pattern
     *
     * E.g.
     * $this->setPattern('news/:slug');
     * $this->setPattern('news/:date_published(format=Y-m-d)/:slug');
     *
     * @param string $pattern
     * @throws UrlException
     */
    public function setPattern(string $pattern)
    {
        $this->clearParams();
        $this->pattern = $pattern;

        foreach ($this->availableParams as $param) {
            if (preg_match('/:(' . preg_quote($param, '/') . ')(\([^)]+\))?/', $pattern, $m)) {
                $replace = $m[0];
                $param = $m[1];

                if (isset($m[2]) & !empty($m[2])) {
                    $this->setParam($param, $replace, $this->parseParamOptions($m[2]));
                } else {
                    $this->setParam($param, $replace);
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * Reset and clear all current params
     */
    public function clearParams()
    {
        $this->params = [];
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param string $param
     * @return bool
     */
    public function hasParam(string $param): bool
    {
        return array_key_exists($param, $this->getParams());
    }

    /**
     * Is the specified URL param name valid?
     *
     * @param string $param
     * @return bool
     */
    public function validParam(string $param): bool
    {
        return in_array($param, $this->availableParams);
    }

    /**
     * Set a URL param
     *
     * @param string $param
     * @param string $replace
     * @param array $options
     * @throws UrlException
     */
    public function setParam(string $param, string $replace, array $options = [])
    {
        if (!$this->validParam($param)) {
            throw new UrlException(sprintf('Param name %s not recognised!', $param));
        }

        $this->params[$param] = [
            'replace' => $replace,
            'options' => $options
        ];
    }

    /**
     * Return replace string for the URL param
     *
     * @param string $param
     * @return mixed
     * @throws UrlException
     */
    public function getReplace(string $param)
    {
        if (!$this->validParam($param)) {
            throw new UrlException(sprintf('Param name %s not recognised!', $param));
        }

        if ($this->hasParam($param)) {
            return $this->params[$param]['replace'];
        }

        return null;
    }

    /**
     * Return options for the URL param
     *
     * @param string $param
     * @return array
     * @throws UrlException
     */
    public function getOptions(string $param): array
    {
        if (!$this->validParam($param)) {
            throw new UrlException(sprintf('Param name %s not recognised!', $param));
        }

        if ($this->hasParam($param)) {
            return $this->params[$param]['options'];
        }

        return [];
    }

    /**
     * Get a named option for a URL param
     *
     * @param string $param
     * @param string $option
     * @return null
     * @throws UrlException
     */
    public function getOption(string $param, string $option)
    {
        if (!$this->validParam($param)) {
            throw new UrlException(sprintf('Param name %s not recognised!', $param));
        }

        if ($this->hasParam($param) && isset($this->params[$param]['options'][$option])) {
            return $this->params[$param]['options'][$option];
        } else {
            return null;
        }
    }

    /**
     * Parse param options string into an array
     *
     * These are expected in key=value format
     * Multiple params can be set with comma separated values, e.g. key1=value,key2=value
     *
     * @param string $value
     * @return array Array of param options
     */
    public function parseParamOptions(string $value): array
    {
        $options = [];

        $value = trim($value, "() \t\n\r\0\x0B");
        $values = explode(',', $value);
        foreach ($values as $value) {
            $pairs = explode('=', $value);
            if (count($pairs) === 2) {
                $options[$pairs[0]] = $pairs[1];
            }
        }

        return $options;
    }

    /**
     * Parse a param value for content into a URL
     *
     * @param string $url
     * @param BaseContent $content
     * @param string $param
     * @return string URL
     * @throws UrlException
     */
    public function parseParamValue(string $url, BaseContent $content, string $param): string
    {
        $formatDate = function (DateTime $date, string $param): string {
            $format = $this->getOption($param, 'format');

            // Default format: 2012/12/30
            if (empty($format)) {
                $format = 'Y/m/d';
            }
            return $date->format($format);
        };

        switch ($param) {
            case 'id':
                $value = $content->getId();
                break;

            case 'slug':
                $value = $content->getUrlSlug();
                break;

            case 'date_published':
                $value = $formatDate($content->getDatePublished(), $param);
                break;

            case 'date_modified':
                $value = $formatDate($content->getDateModified(), $param);
                break;

            default:
                throw new UrlException(sprintf('Param name %s not recognised!', $param));
        }

        return str_ireplace($this->getReplace($param), $value, $url);
    }

    /**
     * Return URL for the a page
     *
     * @param BaseContent $content
     * @return string
     * @throws UrlException
     */
    public function getUrl(BaseContent $content): string
    {
        $url = $this->getPattern();

        foreach ($this->params as $param => $values) {
            $url = $this->parseParamValue($url, $content, $param);
        }

        return $url;
    }
}
