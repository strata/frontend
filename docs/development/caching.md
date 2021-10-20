# Caching

## Performance issues with headless CMS sites

Building a frontend website which dynamically reads data from multiple external APIs can cause performance issues. Reading data from multiple data sources results in a new HTTP request for each API call, with an external web application generating content for each API call.

While some external data calls may cache repeatedly requested data, it's good practice to cache this on the frontend website side.

## Caching strategies

It is generally considered good performance to cache commonly requested content, to speed up future requests for users. However, caching adds a layer of complexity, so it's important to do this with care. 

It's recommended to add caching in small steps, measuring the difference in page load speed as you add a cache layer. 

[Full-page caching](#full-page-caching) is commonly used to cache the entire page content. This is really useful if your page content is the same for all users (which is very common for most websites). There are techniques that can be used to serve personalised content alongside shared, cached content.

[Data caching](#data-caching) can be used to cache individual data responses. This is useful if you have any data requests that are shared across multiple pages. If the data request is unique for the current page, then full-page caching is a more effective way to cache the current page content.

## Full-page caching

Full-page caching (also known as HTTP caching) caches all page content (and usually request headers) for the current page. Most page content is common to all users, so this approach tends to work very well. 

It's important to remember the entire page content and headers are cached with this approach. This means any data sent over the response headers is also shared, so be careful about using things like PHP sessions or CSRF tokens. 

### Caching services
Common full-page caching tools include [Varnish](https://varnish-cache.org/), [Cloudflare](https://developers.cloudflare.com/cache/) or other CDN caching services. It is recommended to use Varnish or other professional CDN services for full-page caching on production.

These services work based on standard [caching HTTP response headers](https://tomayko.com/blog/2008/things-caches-do). 

### Caching response headers

To enable caching the right caching response headers need to be passed.

_TODO - add example code_

### Dealing with personalised content

Any page content that is personalised to the user should either be not cached, or can be populated via a JavaScript request. 

For example, if you just want to populate a user menu with the current user's name and links to their account, then defaulting to a not-logged in view in HTML is a sensible approach. You can then use an asynchronous JavaScript request (also known as XMLHttpRequest or Ajax) to check the user's logged in status and populate the user menu. This approach only requires the asynchronous JavaScript request to not be cached. All other page content can be cached and shared for all users, speeding up requests considerably.

It is also possible to use Edge Side Includes (ESI) ([Varnish](https://varnish-cache.org/docs/7.0/users-guide/esi.html), [Symfony](https://symfony.com/doc/current/http_cache/esi.html)).

## Data caching

Strata Data can also cache data returned from external APIs, speeding up page generation. A summary of how this works appears below. You can also read full details about [data caching](https://docs.strata.dev/data/usage/caching).

### Enable the cache

_TODO - check the code examples_

First pass a PSR-6 compatible cache adapter to the setCache() method to enable caching. It's recommended to use a cache that supports tagging.

You can do this either on a query manager object (`Strata/Data/Query/QueryManager`) or 
directly via the data provider object (implements `Strata\Data\DataProviderInterface`).

```php
use Symfony\Component\Cache\Adapter\FilesystemTagAwareAdapter;

$manager->setCache(new FilesystemTagAwareAdapter());
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

_TODO use cache tags to help clear cache on URL or data request type_

You can add tags to all data cache requests via:

```php
$api->setCacheTags(['my-tag', 'second-tag']);
```

## Clearing the cache

### Full-page caching

Varnish supports [PURGE requests](https://varnish-cache.org/docs/7.0/users-guide/purging.html) to remove the cache for the current page URL. 

Cloudflare supports [cache purging](https://developers.cloudflare.com/cache/how-to/purge-cache) via their control panel and via their [API](https://api.cloudflare.com/#zone-purge-files-by-url).

### Clearing the cache on changes in your CMS

_TODO Craft CMS_

### Symfony cache

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
