# Strata Frontend

Strata is based on the concept of reading in content from different sources (e.g. APIs) and delivering these to users on 
the frontend. The majority of websites are content-based, which is well suited to Strata. 

Content is transformed into content objects to standardise how you access content. For example, a standard pagination object 
is available. 

Delivery is via HTML, since this is the foundation of the web. Caching is built-in to enable high performance. We have 
Strata-powered websites running via WordPress API with sub 50ms response times (with PHP-based caching).

You should consider content cacheable, and therefore the same for everyone. Personalised content can be accessed via 
JavaScript, which can avoid caching. 

The aim is to deliver fast, accessible user experiences on the modern web.

{% page-ref page="installation.md" %}

## Layers

Strata is comprised of the following application layers:

* **Data layer** - retrieve content from data sources, includes automated decoding, caching and error handling
* **Content schema** - maps data to strongly typed content objects so they can be used in a consistent fashion
* **Templating** - use [Twig](https://twig.symfony.com/) to output efficient templating on the frontend
* **Caching** - help speed up data retrieval (both at the data layer and full-page caching)

### Data layer

The data layer is powered by [Strata Data](https://github.com/strata/data), a separate project which can also be used to 
help make data integration projects easier.

You connect to a **repository** which is responsible for accessing data from external APIs. Repositories use support 
caching, events, and mapping data to content models. Repositories have custom methods for data retrieval that fits the 
API. If caching is used then data is retrieved from the cache, making requests fast.

Data is returned from a repository in a raw format, for HTTP-based APIs this is a HTTP response.

Once you have retrieved data you can map it to a content object (or collection of content objects) via **mappers**.
These parse the response data into typed objects that you can pass to your templates to output to the frontend.

{% page-ref page="development/accessing-data.md" %}

### Content schema

Most content is stored in a CMS which has a custom or flexible content fields. To map data onto content objects you 
first need to define a **content schema** in configuration (YAML files).

This helps define the content fields that are mapped to objects (each API provider has custom mapping and field resolver 
classes to help define how data is mapped to objects).

Using strongly typed models is useful because:

* You only pass the data you want to the template layer
* Content is available in a consistent, simple format (e.g. `{{ page.title }}` rather than API response fields such as `{{ page.data.title.rendered }}`)
* You can use template helpers (such as outputting automated pagination)
* Content is accessed in a similar way across all APIs you may use, making adding future data sources (or changing them) easier

_Note: a CLI tool is being added to help automate the generation of content schema config files._

{% page-ref page="development/content-schema.md" %}

### Templating

Content is outputted to the template layer, in Symfony this is Twig. Content models are designed to output content easily 
and a range of template helpers (filters, functions) exist to automate content generation.

{% page-ref page="templating/index.md" %}

### Caching

Caching is at the data layer and full-page caching.  

At the data layer Strata can automatically cache the raw API response data, removing external API requests for page generation. 

Strata uses Symfony HTTPCache to offer full-page caching in PHP. However, performance can be increased by using a dedicated 
full page caching system such as [Varnish Cache](https://varnish-cache.org/). 

{% page-ref page="development/caching.md" %}
