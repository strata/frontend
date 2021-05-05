# HTTP full page caching

TODO

Strata supports caching HTTP responses via [Symfony's HTTPCache](https://symfony.com/doc/current/http_cache.html).
Functionality has been extended via [FOSHttpCache](https://foshttpcache.readthedocs.io/en/latest/index.html) and
[Toflar Psr6Store](https://github.com/Toflar/psr6-symfony-http-cache-store).

# Default behaviour

By default HTTP responses are cached based on `Cache-Control` response headers. For example this header will cache the
HTTP response for 5 minutes:

```
Cache-Control: public, max-age: 300
```

HTTP responses are cached based on their URL. So subsequent requests to the same URL will return a cached response, if
a valid cache response can be found.

For example these requests all return exactly the same data when caching is enabled. Only the first request is actually
sent to the server.


### Default TTL
If no cache-control headers exist in the response, a default time-to-live (TTL) cache lifetime of 1 hour is used.

You can set a different default TTL via the HttpCache option: `default_ttl`

```
$data->enableHttpCache('path/to/cache/folder', ['default_ttl' => 86400]);
```

## Tagging

By default, if a `Cache-Tags` response header is present this is used to tag the cached response. This can then be used
to invalidate the cache based on tags.

### Setting custom tags for a HTTP response

You can also set a custom tag against an HTTP response, to make it easier to group cached data responses by tag name.

#### CachingHttpClient::addTag(string $tag)

Behind the scenes Strata Data uses a custom `CachingHttpClient` which uses `FOSHttpCache`. To add tags before they are
stored in the cache you can do this via the `addTag()` method. This adds a tag to the list of custom tags to use when
storing HTTP responses in the cache.

```php
$data->getHttpClient()->addTag('custom-tag');
```

### CachingHttpClient::setTags(array $tags)

You can also set many tags at once via `setTags()`.

```php
$data->getHttpClient()->setTags(['tag1', 'tag2']);
```

It's important to note once you add tags, they are set to all subsequent requests. To stop this you can pass an
empty array to the `setTags()` method.

```php
$data->getHttpClient()->setTags([]);
```

## Invalidating the cache

TODO

Need a Data API method for this?

```php
$data->getCache()->invalidate($method, $uri, $options);
```

Or https://foshttpcache.readthedocs.io/en/latest/cache-invalidator.html#cache-invalidate

```php
$data->getCacheInvalidator()->invalidatePath('/users')->flush();
```

### Tags

TODO

```php
$cache = $data->getHttpClient()->getCache();
$cache->invalidateTags(['custom-tag']);
```
OR

```php
$data->getCache()->invalidateTags(['custom-tag']);
```


## Full page caching

In Production we use the [HTTP Cache](https://symfony.com/doc/current/http_cache.html) to cache the entire page response
in Symfony, making most page requests incredibly fast. This is achieved with a small edit in the `index.php` file, an
example appears below.

```php
// Wrap the default Kernel with the HTTP CacheKernel one in 'prod' environment
if ('prod' === $env) {
    $kernel = new CacheKernel($kernel);
}
```

The cache lifetime is controlled by standard HTTP headers which are set in the `App\EventListener\ResponseListener`, an
example appears below:

```php
public function onKernelResponse(ResponseEvent $event)
{
    // Add caching layer for Production (30 min cache on all pages)
    if (getenv('APP_ENV') === 'prod') {
        $response = $event->getResponse();
        $response->setSharedMaxAge(1800);
        $response->headers->addCacheControlDirective('must-revalidate', true);
    }
}
``` 

To test the HTTP Cache on local development, change your environment to prod temporarily (remember to change it back!)

```php
# .env.local
APP_ENV=prod
```
