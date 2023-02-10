<?php

declare(strict_types=1);

namespace Strata\Frontend\Cache;

use FOS\HttpCache\SymfonyCache\CacheInvalidation;
use FOS\HttpCache\SymfonyCache\DebugListener;
use FOS\HttpCache\SymfonyCache\EventDispatchingHttpCache;
use FOS\HttpCache\SymfonyCache\PurgeTagsListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpCache\HttpCache as SymfonyHttpCache;
use Symfony\Component\HttpKernel\HttpCache\SurrogateInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Toflar\Psr6HttpCacheStore\Psr6StoreInterface;

/**
 * @todo copied from Strata Data, needs testing
 * @see https://foshttpcache.readthedocs.io/en/latest/installation.html
 */
class HttpCache extends SymfonyHttpCache implements CacheInvalidation
{
    use EventDispatchingHttpCache;

    const EVENT_PRE_CACHEABLE = 'strata_data.pre_cacheable';

    /**
     * Overwrite constructor to register event listeners for FOSHttpCache
     *
     * HttpCache constructor.
     * @param HttpKernelInterface $kernel
     * @param Psr6StoreInterface $store
     * @param SurrogateInterface|null $surrogate
     * @param array $options
     */
    public function __construct(HttpKernelInterface $kernel, Psr6StoreInterface $store, SurrogateInterface $surrogate = null, array $options = [])
    {
        parent::__construct($kernel, $store, $surrogate, $options);
    }

    /**
     * Made public to allow event listeners to do refresh operations.
     *
     * Override functionality to add event listener at point of fetching response, before it is checked to be cacheable
     *
     * {@inheritDoc}
     */
    public function fetch(Request $request, $catch = false)
    {
        $subRequest = clone $request;

        // send no head requests because we want content
        if ('HEAD' === $request->getMethod()) {
            $subRequest->setMethod('GET');
        }

        // avoid that the backend sends no content
        $subRequest->headers->remove('if_modified_since');
        $subRequest->headers->remove('if_none_match');

        $response = $this->forward($subRequest, $catch);
        $response = $this->dispatch(self::EVENT_PRE_CACHEABLE, $request, $response);

        if ($response->isCacheable()) {
            $this->store($request, $response);
        }

        return $response;
    }
}
