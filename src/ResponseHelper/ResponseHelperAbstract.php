<?php

declare(strict_types=1);

namespace Strata\Frontend\ResponseHelper;

use FOS\HttpCache\ResponseTagger;
use FOS\HttpCache\TagHeaderFormatter\CommaSeparatedTagHeaderFormatter;
use FOS\HttpCache\TagHeaderFormatter\TagHeaderFormatter;
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
    private ?ResponseTagger $responseTagger = null;

    /**
     * @inheritDoc
     */
    abstract public function setHeader(string $name, string $value, bool $replace = true): self;

    /**
     * Return response tagger for adding cache tags to the response
     *
     * Usage:
     * $responseTagger = $responseHelper->getResponseTagger();
     * $responseTagger->addTags(['tag-one', 'tag-two']);
     *
     * // Apply cache tag headers to response
     * $responseHelper->apply($response);
     *
     * @param string $headerName Response header for cache tags, defaults to X-Cache-Tags
     * @param string $glue Combine multiple tags with this string
     * @return ResponseTagger
     */
    public function getResponseTagger(string $headerName = TagHeaderFormatter::DEFAULT_HEADER_NAME, string $glue = ','): ResponseTagger
    {
        if ($this->responseTagger instanceof ResponseTagger) {
            return $this->responseTagger;
        }

        if ($headerName !== TagHeaderFormatter::DEFAULT_HEADER_NAME || $glue !== ',') {
            $formatter = new CommaSeparatedTagHeaderFormatter($headerName, $glue);
            $this->responseTagger = new ResponseTagger(['header_formatter' => $formatter]);
        } else {
            $this->responseTagger = new ResponseTagger();
        }

        return $this->responseTagger;
    }

    /**
     * Set the response tagger used for adding cache tags to the response
     * @param ResponseTagger $responseTagger
     * @return $this
     */
    public function setResponseTagger(ResponseTagger $responseTagger): self
    {
        $this->responseTagger = $responseTagger;
        return $this;
    }

    /**
     * Set cache tag headers from response tagger
     * @return $this
     */
    public function setHeadersFromResponseTagger(): self
    {
        $responseTagger = $this->getResponseTagger();
        $this->setHeader($responseTagger->getTagsHeaderName(), $responseTagger->getTagsHeaderValue());
        $this->getResponseTagger()->clear();
        return $this;
    }

    /**
     * Add cache tags to response tagger from query manager
     * @param QueryManager $manager
     * @return $this
     */
    public function addTagsFromQueryManager(QueryManager $manager): self
    {
        $responseTagger = $this->getResponseTagger();

        foreach ($manager->getQueries() as $query) {
            if ($query->hasResponseRun() && $query->hasCacheTags()) {
                $responseTagger->addTags($query->getCacheTags());
            }
        }

        $this->setHeadersFromResponseTagger();
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
