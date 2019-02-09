# Content fields

* TOC
{:toc toc_levels=2..3}

{% raw %}
  
## The content object

We build a standardised content object to collect standard fields and custom content fields from the CMS. These are detailed 
below.

### Common fields

#### id

The page ID (from the CMS).

``
{{ page.id }}
``

#### title

Page title.

```
{{ page.title }}
```

#### Page URL

TODO:  Return the current page URL. E.g. /news/my-page-title

```
{{ page.url }}
```

#### urlSlug

URL slug as defined in the CMS. This isn't the whole page URL, for that, see `page.url`

```
{{ page.urlSlug }}
```

#### status

TODO:  Page status as defined by the CMS.

```
{{ page.status }}
```

#### contentType

The content type from the CMS (string). E.g. `post`

```
{{ page.contentType }}
```

#### excerpt

A summary of the content. This either returns the field set in the CMS as the excerpt. 

```
{{ page.excerpt }}
```
 
TODO:  If no excerpt is set this is automatically generated from the first 200 characters of the page content, 
with any HTML stripped out. 

You can change the number of characters the automatically generated excerpt is based on by passing the `limit` 
parameter. The following example sets the excerpt to 100.

```
{{ page.excerpt(100) }}
```

#### datePublished

Date the page was published, see `DateTime`.
 
```
{{ page.datePublished }}
```

#### dateModified

Date the page was last modified, see `DateTime`.

```
{{ page.dateModified }}
```

### Custom content fields

Custom content fields are collected within the `content` property. `content` is a collection of many content fields and
can be looped over like an array or accessed directly by referencing the content field name of the child item.

Custom fields all have their own object type, which can be seen in the Symfony debugger. All content types share the 
following common methods. In the example code below we use the word `field` to represent the field name. In your own code
make sure you reference tha actual content field name used in your content object.

In the example below we use the word `field` to refer to the content field name. E.g. to output the field:

```
{{ page.content.field }}
```

In real world usage, replace `field` with the actual field name. E.g. Output the post_type field:

```
{{ page.content.post_type }}
```

#### Common properties

Output the field type (e.g. image):

```
{{ page.content.field.type }}
```

Output the field's name:

```
{{ page.content.field.name }}
```

Whether the field contains HTML (boolean):

```
{{ page.content.field.hasHtml() }}
```

Accessing the field directly will output a string:

```
{% if page.content.field %}
```

You can also use this to test to see whether the content field contains anything:

```
{% if page.content.field is not empty %}
    Do something
{% endif %}
```

Output the field's raw value. This is useful if it is not a string:

```
{{ page.content.field.value }}
```

#### ShortText

Short text field, we do not expect any line returns or HTML. 

```
{{ page.content.field }}
```

#### PlainText

Plain text field, can have line returns but we do not expect any HTML.

```
{{ page.content.field }}
```

#### Escaping content
By default Twig auto-escapes all variables using the HTML strategy. If the text field is intended to be used 
in a different context, ensure you use the correct escaping. For example, if the text is outputted in CSS: 

```
<a href="link" class="{{ page.content.field|e("css") }}">My text</a> 
```

See:
* https://twig.symfony.com/doc/2.x/templates.html#html-escaping 
* https://twig.symfony.com/doc/2.x/filters/escape.html

#### RichText

HTML text. By default Twig auto-escapes all variables using the HTML strategy. You need to use the `raw` 
filter in Twig to avoid escaping HTML. 

It is assumed the HTML in a RichText field is safe to display. It is the CMS responsibility to ensure the HTML
returned is safe for use.

```
{{ page.field.name|raw }}
```

#### Date

Date field. Default output format is Y-m-d which is a bit ugly. E.g. 2019-01-30

```
{{ page.content.field }}
```

You can customise the date format via `format`. This returns: Wed 30th Jan, 2019 

```
{{ page.content.field.format("D jS M, Y") }}
```

You can also use the variable as a date in Twig. For example, you can also format the date via the Twig `date` filter. 

```
{{ page.content.field|date("D jS M, Y") }}
```

See https://twig.symfony.com/doc/2.x/filters/date.html

And you can compare dates via:

```
{% if date(page.content.field1) < date(page.content.field2) %}
    Do something
{% endif %}
```

See https://twig.symfony.com/doc/2.x/functions/date.html

#### DateTime

Datetime field. 

Default output is a standarsided format, Atom, e.g. 2019-01-30T15:52:01+00:00. This is compatible with ISO-8601.  

```
{{ page.content.field }}
```

DateTime has the same functionality as Date. Customise the date format via the `date` filter. This returns:  30 Jan 2019, 15:52:01 GMT 

```
{{ page.content.field.format("j M Y, H:i:s T") }}
```

#### Returning part of a Date or DateTime field

If you want to compare dates for a DateTime field you need to compare just the date part, otherwise the comparison 
will use the full date and time. 

You can return parts of a Date or DateTime field as so: 

Return the date (e.g. 2019-01-30):

```
page.content.field.date
```

Return the month (e.g. 1 for Jan, 7 for July):

```
page.content.field.month
```


Return the day (e.g. 30):

```
page.content.field.day
```

Return the weekday (e.g. 1 for Monday through 7 for Sunday)

```
page.content.field.weekday
```

Return the hour in 24-hour format (e.g. 1 for 1am, 15 for 3pm):

```
page.content.field.hour
```

Return the minute with leading zeros (e.g. 05 for 5 mins, 15 for 15 mins)

```
page.content.field.minutes
```

Return the seconds with leading zeros (e.g. 05 for 5 secs, 15 for 15 secs)

```
page.content.field.seconds
```

#### TODO:  Number / Integer / Decimal

An integer or decimal number. 

#### Boolean

A true / false variable.

If you just try to output the variable it output true or false. 

```
{{ page.content.field }}
```

Please note you cannot this in a comparison since a string always returns true in PHP. You can access the boolean 
value directly via `page.content.field.value`. You can use the following shortcuts.

Test whether value is true:

```
{% if page.content.field.true %}
    Do something
{% endif %}
```

Test whether value is false:  

```
{% if page.content.field.false %}
    Do something
{% endif %}
```

Test whether two boolean fields have the same value:

```
{% if page.content.field1.value == page.content.field2.value %}
    Do something
{% endif %}
```

#### Image

An image. Outputting the variable will return the default image URL value:

```
{{ page.content.field }}
```

There are different text fields associated with an image you can also output.

Title:

```
{{ page.content.field.title }}
```
 
Alt text:

```
{{ page.content.field.alt }}
```

Caption:

```
{{ page.content.field.caption }}
```

If different sizes have been setup there are multiple ways to return these. If any of these functions do not match 
an image null is returned.

Return image URL by image size name:

```
{{ page.content.field.byName('thumb') }}
```

Return image URL by image width:

```
{{ page.content.field.byWidth(300) }}
```

Return image URL by image height:

```
{{ page.content.field.byHeight(100) }}
```

Return image URL by image width and height:

```
{{ page.content.field.byWidthHeight(300, 100) }}
```

#### Flexible Content

**Flexible content** is a way of supporting flexible mix of content types that may apply to a piece of content. They are built 
up of **Components** which themselves are a set of different **Content fields**. 

The relationship is:   

* Flexible Content, is a collection of:
   * Components, is a collection of:
       * Content fields

You can loop over flexible content and output it.

E.g. Looping over a flexible content field called `page_content`:


```
{% for component in page.content.page_content %}

    {{ component.name }}
    {% for field in component %}
    
        {{ field.type }}
        {{ field.name }}
    
    {% endfor %}
    
{% endfor %}
```

{% endraw %}