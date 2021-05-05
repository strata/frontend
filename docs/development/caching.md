# Caching

## Data caching

Strata Data can cache data returned from external APIs, speeding up page generation. A summary of how this works appears 
below. You can also read full details about [data caching](https://docs.strata.dev/data/usage/caching) in Strata Data.

### Enable the cache

First pass a PSR-6 compatible cache adapter to the setCache() method to enable caching. It's recommended to use a cache that supports tagging.

You can do this either on a data repository object (implements `Strata/Frontend/Repository/RepositoryInterface`) or 
directly via the data provider object (implements `Strata\Data\DataProviderInterface`).

```php
use Symfony\Component\Cache\Adapter\FilesystemTagAwareAdapter;

$api->setCache(new FilesystemTagAwareAdapter());
```

This sets and enables the cache for all subsequent data requests.

### Using the cache

To cache data simply set the cache and then make your data request:

```php
$response = $api->get('my-data');
```

If the response is stored in the cache, then the cached version is returned. Cache items are stored based on the URI and 
query options (defined in `DataProviderInterface::getRequestIdentifier()`). 

You can confirm if a response is from the cache via:

```php
$cacheHit = $response->isHit();
```

You can also disable the cache for future data requests:

```php
$api->disableCache();
```

And re-enable it again when you want to use it:

```php
$api->enableCache();
```

### Cache lifetime

By default, all GET or HEAD requests are cached for one hour. You can specify custom cache lifetimes when enabling the cache:

```php
$api->enableCache(300);
```

### Cache tags

You can add tags to all data cache requests via:

```php
$api->setCacheTags(['my-tag', 'second-tag']);
```

## Clearing the cache

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
