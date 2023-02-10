# WordPress

## Setup

Setup your repository connection to the WordPress API via:

```php
use Strata\Frontend\Repository\WordPress\WordPress;

$api = new WordPress('https://example.com/wp-json/');
```

You can test you have a valid API connection via:

```php
$success = $api->ping();
```

### Content schema

TODO

### Authentication

You shouldn't have to authenticate for access to the public REST API. If you need to send any authenticated API requests 
you will need to pass your authentication details (generated from the CMS).

```php
$api->setAuthorization($username, $password);
```

This requires [Application Passwords WordPress plugin](https://wordpress.org/plugins/application-passwords/).

## Usage

TODO


