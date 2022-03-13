<?php

declare(strict_types=1);

namespace Strata\Frontend\ResponseHelper;

use FOS\HttpCache\ResponseTagger;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Query\QueryManager;
use Strata\Frontend\Exception\InvalidResponseHeaderValueException;

/**
 * Helper to apply headers to an HTTP response from your frontend application
 *
 * Includes built-in support for common security and caching response headers
 */
abstract class ResponseHelperAbstract implements ResponseHelperInterface
{
    private array $headers = [];

    /**
     * Set a header
     * @param string $name
     * @param string $value
     * @param bool $replace If true, replace header, if false, append header
     * @return $this
     */
    public function setHeader(string $name, string $value, bool $replace = true): self
    {
        if (!isset($this->headers[$name]) || $replace) {
            $this->headers[$name] = [];
        }
        $this->headers[$name][] = new HeaderValue($value, $replace);
        return $this;
    }

    /**
     * Return headers array
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @inheritDoc
     */
    abstract public function apply($response);

    /**
     * Add cache tags to response tagger from query manager
     * @param ResponseTagger $responseTagger
     * @param QueryManager $manager
     * @param bool $setHeaders Whether to automatically set headers once tags are retrieved from query manager
     * @return ResponseTagger
     */
    public function applyResponseTagsFromQuery(ResponseTagger $responseTagger, QueryManager $manager, bool $setHeaders = false): ResponseTagger
    {
        foreach ($manager->getQueries() as $query) {
            if ($query->hasResponseRun() && $query->hasCacheTags()) {
                $responseTagger->addTags($query->getCacheTags());
            }
        }

        if ($setHeaders) {
            $this->setHeadersFromResponseTagger($responseTagger);
        }

        return $responseTagger;
    }

    /**
     * Set cache tag headers from response tagger
     * @param ResponseTagger $responseTagger
     * @param bool $replace If true, replace header, if false, append header
     * @return $this
     */
    public function setHeadersFromResponseTagger(ResponseTagger $responseTagger, bool $replace = true): self
    {
        $this->setHeader($responseTagger->getTagsHeaderName(), $responseTagger->getTagsHeaderValue(), $replace);
        $responseTagger->clear();
        return $this;
    }

    /**
     * Set Cache-Control headers to enable full page caching
     * @param int $maxAge Max age in seconds, defaults to 1 day
     * @return $this
     */
    public function cacheControl(int $maxAge = CacheLifetime::DAY): self
    {
        $this->setHeader('Cache-Control', sprintf('public, must-revalidate, max-age=%d', $maxAge));
        return $this;
    }

    /**
     * Mark this response as do not cache
     * @return $this
     */
    public function doNotCache(): self
    {
        $this->setHeader('Cache-Control', 'private, no-store, no-cache');
        return $this;
    }

    /**
     * Set X-Frame-Options security response header
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Frame-Options
     * @see https://owasp.org/www-project-secure-headers/#x-frame-options
     * @return $this
     * @throws InvalidResponseHeaderValueException
     */
    public function setFrameOptions(string $value = 'deny'): self
    {
        $valid = ['deny', 'sameorigin'];
        if (!in_array($value, $valid)) {
            throw new InvalidResponseHeaderValueException(sprintf('Invalid response header value "%s" for frameOptions', $value));
        }

        $this->setHeader('X-Frame-Options', $value);
        return $this;
    }

    /**
     * Set content type options security response header
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Content-Type-Options
     * @see https://owasp.org/www-project-secure-headers/#x-frame-options
     * @return $this
     */
    public function setContentTypeOptions(): self
    {
        $this->setHeader('X-Content-Type-Options', 'nosniff');
        return $this;
    }

    /**
     * Set referrer policy security response header
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Referrer-Policy
     * @see https://owasp.org/www-project-secure-headers/#referrer-policy
     * @return $this
     */
    public function setReferrerPolicy(string $value = 'same-origin'): self
    {
        $valid = [
            'no-referrer',
            'no-referrer-when-downgrade',
            'origin',
            'origin-when-cross-origin',
            'same-origin',
            'strict-origin',
            'strict-origin-when-cross-origin',
            'unsafe-url',
        ];
        if (!in_array($value, $valid)) {
            throw new InvalidResponseHeaderValueException(sprintf('Invalid response header value "%s" for setReferrerPolicy', $value));
        }

        $this->setHeader('Referrer-Policy', $value);
        return $this;
    }
}
