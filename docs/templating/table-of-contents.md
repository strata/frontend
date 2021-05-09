# Table of contents

The table of contents helper can be used to generate a `<ul>` list of table of content links, linking to all the headings 
in your web page. This is useful if your web page is long and you want to provide quick links at the top of the page
for users.

The `tableOfContents` function parses a block of HTML for heading tags, outputs a `<ul>` list of table of anchor 
links to the headings in your page, and parses ID attributes into headings in the main content (to enable anchor links). 

## Usage

First pass the HTML that you want to extract headings from to the `tableOfContents` function.
The following example assumes the `content` variable contains the content you wish to parse for headings.


```twig
{% set toc = table_of_contents(content) %}
```

By default, table of contents parses all H2 and H3 tags. You can specify what headings you want to extract
by passing an array of heading tags (lower-case) as the second argument:

```twig
{% set toc = table_of_contents(content, ['h2']) %}
```

## Output headings

You can output the table of contents as a `<ul>` which links to heading anchor links within the page content: 

```twig
{{ toc.headings }}
```

You can add HTML attributes to the `<ul>` tag by passing an array to the `ul()` method:

```twig
{{ toc.headings.ul({'class': 'my-class-name'}) }}
```

If you want more control you can iterate over the headings object itself and create your own HTML. The `headings` object
is iterable and each item contains the following properties:

* `int $level` - the heading level (e.g. for a H2 this is 2, for a H3 this is 3)
* `string $name` - heading name
* `string $link` - heading link (e.g. #heading-title)
* `HeadingsCollection $children` - collection of any child headings 

Example:

```twig
{% for heading in toc.headings %}
    <li><a href="{{ heading.link }}">{{ heading.name }}</a>
    {% if heading.children is not empty %}
    <ul>
      {% for sub_heading in heading.children %}
      <li><a href="{{ sub_heading.link }}">{{ sub_heading.name }}</a>
      {% endfor %}
    </ul>
    {% endif %}
    </li>
{% endfor %}
```

## Output parsed HTML content

You can output the original HTML content you passed to `tableOfContents()` with ID attributes injected into 
heading tags, which enables anchor navigation:

```twig
{{ toc.html }}
```

The helper can deal with duplicate headings and inserts an incrementing integer number to help fix these (anchor links on a
HTML page must be unique since they link to ID attributes).

## Gotchas

If your HTML content has incorrectly nested headings you may find inconsistent results. 

For example, if your HTML has a H2 with no H3s and an H4 then this H4 will be excluded from the table of
contents (if you asked to include H4s). If your HTML has any H3s then an incorrectly 
nested H4 will be attached to the last H3 in your HTML.