# Twig filters & functions

The following filters and functions are available in Twig via the Frontend application.

**Filters**
* [build_version](#build_version)
* [excerpt](#excerpt)

**Functions**
* [all_not_empty](#all_not_empty)
* [fix_url](#fix_url)
* [is_prod](#is_prod)
* [not_empty](#not_empty)
* [slugify](#slugify)
* [staging_banner](#staging_banner)

## Filters

### build_version

Add a build version to src file in HTML, helping you bust the cache when making changes to CSS and JS files. 

Usage:

```
{{ '/path/to/file' | build_version }}
```

For example:

```
{{ '/assets/styles.css' | build_version }}
```

Returns: `/assets/styles.css?v=8b7973c7`

This filter attempts to read the file, if the path is not relative then it tries to load the file relative to the 
document root (defined by the `$_SERVER['DOCUMENT_ROOT']`). It creates a short 8-character hash of the file to help 
create a unique version identifier for the file.

### excerpt

Cut a string to a maximum length, but cut on the nearest word (so words are not split).

Usage:
``` 
{{ 'string' | excerpt(length, more_text) }}
``` 

Example:
``` 
{{ 'Mary had a little lamb, Its fleece was white as snow' | excerpt(30) }}
``` 

Returns: `Mary had a little lamb, Its…`

## Functions

### all_not_empty

Will return true if all of the items passed to it are defined and have non-empty values.

Usage:

```
{% if all_not_empty('item1', 3, null, 'a test string', '') %}
    <!-- do something -->
{% endif %}
```

Example that returns true:
```
{% if not_empty('item1', 3, 'testing) %}
```

Example that returns false:
```
{% if not_empty('item1, '', 'testing') %}
```
     
### fix_url

_Note: to be changed to a filter in 0.7_

Fixes a URL by prepending with http:// or https:// so you can safely use it in a hyperlink. 

This function only adds the http scheme if required and otherwise will leave the URL untouched.

Usage:

```
{{ fix_url(website_url, scheme) }}
```

Example:
```
{{ fix_url('example.com') }}
```

Returns: `http://example.com`

By default the http scheme is added if missing. You can also choose the https scheme by adding a second argument:

```
{{ fix_url('example.com', 'https') }}
```

Returns: `https://example.com`

### is_prod

Returns where this is the production (live) environment. 
 
```
{% if is_prod(app.environment) %}
```

Please note this defaults to expect `prod`  for the production environment. If you use a different production environment 
name you can pass this as the second argument. E.g. 

```
{% if is_prod(app.environment, 'live') %}
```

### not_empty

Will return true if one or more items passed to it are defined and have a non-empty value.

Usage:

```
{{ not_empty('item1', 3, null, 'a test string', '') }}
```

Example that returns true:
```
{{ not_empty('item1', 3, null) }}
```

Example that returns false:
```
{{ not_empty(0, '', null) }}
```

### slugify

_Note: to be changed to a filter in 0.7_

Generate a URL friendly slug from a string. This replaces space with dashes and lower-cases the string. It also filters 
the string and removes any character that is not a unicode letter, number or dash -

Usage:

```
{{ slugify(title) }}
```

Example:
```
{{ slugify('My name is Earl') }}
```

Returns: `my-name-is-earl`

### staging_banner

Outputs a staging banner detailing the current environment. 

```
{{ staging_banner(app.environment) }}
```

This has default styling using the CSS selector `.staging-banner`. You can easily 
override this using CSS with a higher [specificity](https://specifishity.com/). E.g.
  
```
div.staging-banner {
  background-color: red;
}
```

The staging banner also has a class name equal to the current environment.

You can alter the message outputted by passing a second argument. The string '%s' is replaced with the current environment 
name. E.g.

```
{{ staging_banner(app.environment, 'Environment: %s') }}
```

Please note this defaults to expect `prod`  for the production environment. If you use a different production environment 
name you can pass this as the third argument. E.g. 

```
{{ staging_banner(app.environment, 'You are on %s', live') }}
```

