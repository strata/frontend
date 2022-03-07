<?php

namespace Strata\Frontend\ResponseHelper;

use FOS\HttpCache\ResponseTagger;
use FOS\HttpCache\TagHeaderFormatter\TagHeaderFormatter;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Query\QueryManager;
use Strata\Frontend\Exception\InvalidResponseHeaderValueException;

interface ResponseHelperInterface
{
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
    public function getResponseTagger(string $headerName = TagHeaderFormatter::DEFAULT_HEADER_NAME, string $glue = ','): ResponseTagger;

    /**
     * Set the response tagger used for adding cache tags to the response
     * @param ResponseTagger $responseTagger
     * @return $this
     */
    public function setResponseTagger(ResponseTagger $responseTagger): self;

    /**
     * Set cache tag headers from response tagger
     * @return $this
     */
    public function setHeadersFromResponseTagger(): self;

    /**
     * Set a header to the response object
     * @param string $name
     * @param string $value
     * @param bool $replace If true, replace header, if false, append header
     * @return $this
     */
    public function setHeader(string $name, string $value, bool $replace = true): self;

    /**
     * Add cache tags to response tagger from query manager
     * @param QueryManager $manager
     * @return $this
     */
    public function addTagsFromQueryManager(QueryManager $manager): self;

    /**
     * Set Cache-Control headers to enable full page caching
     * @param int $maxAge Max age in seconds, defaults to 1 day
     * @return $this
     */
    public function cacheControl(int $maxAge = CacheLifetime::DAY): self;

    /**
     * Mark this response as do not cache
     * @return $this
     */
    public function doNotCache(): self;

    /**
     * Set X-Frame-Options security response header
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Frame-Options
     * @see https://owasp.org/www-project-secure-headers/#x-frame-options
     * @return $this
     * @throws InvalidResponseHeaderValueException
     */
    public function setFrameOptions(string $value = 'deny'): self;

    /**
     * Set content type options security response header
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Content-Type-Options
     * @see https://owasp.org/www-project-secure-headers/#x-frame-options
     * @return $this
     */
    public function setContentTypeOptions(): self;

    /**
     * Set referrer policy security response header
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Referrer-Policy
     * @see https://owasp.org/www-project-secure-headers/#referrer-policy
     * @return $this
     */
    public function setReferrerPolicy(string $value = 'same-origin'): self;
}