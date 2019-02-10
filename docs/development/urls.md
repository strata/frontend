# URLs

* TOC
{:toc}

{% raw %}

You can setup URLs in your PHP controller code via the `Url` object.

To create a new URL with the pattern `news/:slug`:

```
$url = new Url('news/:slug');
```

When combined with a content object with a URL slug of `hello-world`:

```
echo $url->getUrl($content);
```

Returns: `news/hello-world`

You can also output the URL via Twig, this outputs the URL for the current content object.

```
{{ page.url }}
```

## URL params

You can parse in content variables into the URL, using the options below. 

To set a parameter the format is `:param-name`

To set options associated with the param the format is: `:param-name(option=value,option2=value)` 

### id

Parse in the content ID. E.g.

```
$url = new Url('news/:id');
```

Example URL: `news/123`

### slug

Parse in the URL slug. E.g.

```
$url = new Url('news/:slug');
```

Example URL: `news/hello-world`

### date_published

Parse in the date published slug. 

By default the date is outputted in the format `Y/m/d`

```
$url = new Url('news/:date_published/:slug');
```

Example URL: `news/2019/02/12/hello-world`

You can also set the `format` option which controls how the date is outputted in the URL. See [date format](https://secure.php.net/date) options.

```
$url = new Url('news/:date_published(format=Y)/:slug');
```

Example URL: `news/2019/hello-world`

### date_modified

Parse in the date modified slug. This works in the same way as `date_published`

```
$url = new Url('news/:date_modified/:slug');
```

Example URL: `news/2019/02/13/hello-world`

{% endraw %}