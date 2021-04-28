# Upgrading (pre v1.0)

Any upgrade notes for pre v1.0 releases that include backward-incompatible API changes.

## Upgrading from v0.6/0.7 to v0.8

### PHP 7.4
Strata currently requires a minimum of PHP 7.4, the current actively [supported version of PHP](https://www.php.net/supported-versions.php).

### Upgrade from Symfony 4.4 to Symfony 5

_Note: Symfony 5.3 is released in May_

Update all Symfony packages in your `composer.json` to `"5.2.*"` 

E.g.

```yaml
  symfony/framework-bundle": "5.2.*"
```

Update the extra.symfony.require setting at the bottom of your `composer.json` file: 

```yaml
 "extra": {
      "symfony": {
          "allow-contrib": false,
          "require": "5.2.*"
      }
  }
```

Run `composer update "symfony/*"` 

There may be other steps to follow for your application, see https://symfony.com/doc/current/setup/upgrade_major.html

### Use v0.8 branch

While in dev, update your `composer.json` file:

```
  "strata/frontend": "dev-release/0.8.0 as 0.8.0"
```

When released:

```
  "strata/frontend": "^0.8"
```

Run `composer update`

### Update content model config files

In 0.8.0 the `ContentModel` classes, which define what content types you have available to query in the API, have been 
changed to `Schema` classes. Update the naming conventions in your YAML content config files as follows:

v0.6 / 0.7:

```yaml
content_types:
  news:
    api_endpoint: posts
    source_content_type: posts
    content_fields: news.yaml
  projects:
    api_endpoint: projects
    content_fields: projects.yaml
```

0.8+

```yaml
content_types:
  news:
    endpoint: posts
    source_name: posts
    content_fields: news.yaml
  projects:
    endpoint: projects
    content_fields: projects.yaml
```


### Update controller code TODO

v0.6 format (example page controller action):

```php
public function __construct(CacheInterface $cache, LoggerInterface $logger)
{
    $this->api = new Wordpress(
        getenv('APP_API_BASE_URL'),
        new ContentModel(__DIR__ . '/../../config/content/content-model.yaml')
    );
    $this->api->setCache($cache);
    $this->api->setCacheLifetime(1800);
    $this->api->setLogger($logger);
}
    
public function page(string $slug, Request $request)
{
    $slug = filter_var($slug, FILTER_SANITIZE_STRING);
    $this->api->setCacheKey($request->getRequestUri());

    try {
        // Home content
        $this->api->setContentType('page');
        $page = $this->api->getPageBySlug($slug);

    } catch (NotFoundException $e) {
        throw new NotFoundHttpException('Page not found', $e);
    }

    return $this->render('pages/page.html.twig', [
        'page'  => $page,
    ]);
}
```

This has been updated and simplified in v0.8:

```php
public function __construct(CacheInterface $cache, LoggerInterface $logger)
{
    $this->api = new Wordpress(
        getenv('APP_API_BASE_URL'),
        new ContentModel(__DIR__ . '/../../config/content/content-model.yaml')
    );
    $this->api->setCache($cache);
    $this->api->setLogger($logger);
}

public function page(string $slug, Request $request)
{
    $slug = filter_var($slug, FILTER_SANITIZE_STRING);

    try {
        // Home content
        $this->api->setContentType('page');
        $page = $this->api->getPageBySlug($slug);

    } catch (NotFoundException $e) {
        throw new NotFoundHttpException('Page not found', $e);
    }

    return $this->render('pages/page.html.twig', [
        'page'  => $page,
    ]);
}
```

* Data caching is now automated based on URI and options (you may still want to add cache tags)