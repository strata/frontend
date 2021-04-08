# Caching

* TOC
{:toc}

{% raw %}

## Data caching

Strata Data supports caching HTTP responses via [Symfony's HTTPCache](https://symfony.com/doc/current/http_cache.html).
Functionality has been extended via [FOSHttpCache](https://foshttpcache.readthedocs.io/en/latest/index.html) and
[Toflar Psr6Store](https://github.com/Toflar/psr6-symfony-http-cache-store).


## Enable the cache

Enable the cache via any HTTP data provider via `enableHttpCache($cacheDirectory, $defaultOptions)`. Once this is done all subsequent HTTP
calls are cached.

```
$data->enableHttpCache('path/to/cache/folder');
```

## Default behaviour

By default HTTP responses are cached based on `Cache-Control` response headers. For example this header will cache the
HTTP response for 5 minutes:

```
Cache-Control: public, max-age: 300
```

HTTP responses are cached based on their URL. So subsequent requests to the same URL will return a cached response, if
a valid cache response can be found.

For example these requests all return exactly the same data when caching is enabled. Only the first request is actually
sent to the server.

TODO DOES NOT WORK!

```php
$data = new RestApi('http://httpbin.org/');
$data->enableHttpCache($cacheDirectory);

// This returns a random UUID from httpbin.org
echo $data->get('uuid')->toArray();
echo $data->get('uuid')->toArray();  
```

### Default TTL
If no cache-control headers exist in the response, a default time-to-live (TTL) cache lifetime of 1 hour is used.

You can set a different default TTL via the HttpCache option: `default_ttl`

```
$data->enableHttpCache('path/to/cache/folder', ['default_ttl' => 86400]);
```

### Overriding the cache lifetime

You may want to force a specific cache lifetime for a HTTP request.

TODO

## Is a request cached?

How do you tell if a specific data HTTP request has been cached?

TODO

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





# -- OLD --

All WordPress API calls cache the return data to avoid having to make expensive API calls on every request. By default, 
these are cached for 30 minutes. The cache lifetime is currently managed in the controller constructor, an example 
appears below. 

```php
public function __construct(CacheInterface $cache, LoggerInterface $logger)
{
    $this->api = new Wordpress(
        getenv('APP_API_BASE_URL'),
        new ContentModel(__DIR__ . '/path/to/content-model.yaml')
    );
    $this->api->setContentType('page');
    $this->api->setCache($cache);
    $this->api->setCacheLifetime(1800);
    $this->api->setLogger($logger);
}
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

## Clearing the entire cache

Please note clearing the entire cache is not recommended to do often, but it can be useful in development.

The data cache is saved in the `var/cache/` folder. To clear the application cache, run:

```bash
php bin/console cache:pool:clear cache.app_clearer
```

This does not clear the HTTP Cache. If you have this enabled locally, you can clear the entire HTTP Cache by running:
 
```bash
rm -Rf var/cache/prod/http_cache`
```

Please note the default `cache:clear` command only clears the system cache which includes Twig templates, but not the 
application cache.

```bash
php bin/console cache:clear
```

{% endraw %}
