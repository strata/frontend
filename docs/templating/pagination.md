# Pagination

The pagination object is returned on listing pages, usually in the `{{ pages.pagination }}` field.

The pagination object has the following variables and functions available. In the following examples we assume the 
pagination object is `{{ pagination }}`

## Variables

### page

Return current page.

```
{{ pagination.page }}
```

### totalResults

Return total number of results.

```
{{ pagination.totalResults }}
```

### totalPages

Return total number of pages available.

```
{{ pagination.totalPages }}
```

### resultsPerPage

Return number of results per page.
 
```
{{ pagination.resultsPerPage }}
```

### from

Return result number for first result on this page.

```
{{ pagination.from }}
```

### to

Return result number for last result on this page.

```
{{ pagination.to }}
```

#### Example

```
This is page {{ pagination.page }}, displaying results {{ pagination.from }}-{{ pagination.to }}
```

If pagination is on page 2 with 20 results per page, this will display: 

```
This is page 2, displaying results 11-20
```

### firstPage

Are we on the first page of pagination?

```
{% if pagination.firstPage %}
    Do something
{% end if %}
```

### first 

Output the first page of pagination. This is normally 1.

```
{{ pagination.first }}
```

### lastPage

Are we on the last page of pagination?

```
{% if pagination.lastPage %}
    Do something
{% end if %}
```

### last

Output the last page of pagination.

```
{{ pagination.last }}
```

### previous

Return the previous page number, or 1 if we are on the first page.

```
{{ pagination.previous }}
```

### next

Return the next page number, or the last page number if we are already on the last page.

```
{{ pagination.next }}
```

## Functions

### pageLinks($maxPages)

This returns an array of a set of page numbers to display in pagination. It defaults to showing 5 pages at a time, though 
you can customise this by altering the `$maxPages` value.

For example, using the default 5 page links for a pagination result set with 50 pages you would get the following results:

* Page 1: `[1,2,3,4,5]`
* Page 2: `[1,2,3,4,5]`
* Page 6: `[4,5,6,7,8]`
* Page 45: `[45,46,47,48,49]`
* Page 47: `[46,47,48,49,50]`
* Page 50: `[46,47,48,49,50]`

## Building pagination HTML

See an example at `templates/includes/pagination.html.twig`

When calling this template you need to pass the pagination object and the page route you want to generate for pagination 
links. For example:

```
{{ include('includes/pagination.html.twig', {'pagination': pages.pagination, 'route': 'news_list'}) }}
```
