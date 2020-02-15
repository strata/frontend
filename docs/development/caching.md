# Caching

* TOC
{:toc}

{% raw %}

## Data caching

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
