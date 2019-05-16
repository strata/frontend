# Twig functions

## fix_url()

Fixes a URL by prepending with http:// or https:// so you can reliably use it in a hyperlink. This function only adds
the http scheme is required and otherwise should leave the URL untouched.

Usage:

```
{{ fix_url(website_url) }}
```

By default the http scheme is added if missing. You can also choose the https scheme by adding a second argument:

```
{{ fix_url(website_url, 'https') }}
```

## slugify()

Convert a string into a URL friendly slug. This replaces space with dashes and lower-cases the string. It also filters 
the string and removes any character that is not a unicode letter, number or dash -

For example this converts: `My name is Earl` into: `my-name-is-earl`

Usage:

```
{{ slugify(title) }}
```
