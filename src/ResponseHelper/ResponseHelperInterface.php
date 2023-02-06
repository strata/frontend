<?php

declare(strict_types=1);

namespace Strata\Frontend\ResponseHelper;

use FOS\HttpCache\ResponseTagger;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Query\QueryManager;
use Strata\Frontend\Exception\InvalidResponseHeaderValueException;

interface ResponseHelperInterface
{
    /**
     * Set a header
     * @param string $name
     * @param string $value
     * @param bool $replace If true, replace header, if false, append header
     * @return $this
     */
    public function setHeader(string $name, string $value, bool $replace = true): self;

    /**
     * Return headers array
     * @return array
     */
    public function getHeaders(): array;

    /**
     * Apply headers to response object and return response
     * @param $response
     * @return mixed
     */
    public function apply($response);

    /**
     * Apply cache tags to response tagger from query manager
     * @param ResponseTagger $responseTagger
     * @param QueryManager $manager
     * @return ResponseTagger
     */
    public function applyResponseTagsFromQuery(ResponseTagger $responseTagger, QueryManager $manager): ResponseTagger;

    /**
     * Set cache tag headers from response tagger
     * @param ResponseTagger $responseTagger
     * @return $this
     */
    public function setHeadersFromResponseTagger(ResponseTagger $responseTagger): self;

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
