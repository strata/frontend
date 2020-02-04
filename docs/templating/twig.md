# Twig filters & functions

The following filters and functions are available in Twig via the Frontend application.

**Filters**
* [build_version](#build_version)
* [excerpt](#excerpt)

**Functions**
* [fix_url](#fix_url) (to be changed to a filter in 0.7)
* [slugify](#slugify)

## build_version

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

## excerpt

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
     
## fix_url

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

## slugify

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
