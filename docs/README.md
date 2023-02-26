# Strata Frontend

_Please note: documentation is in progress._

Strata is a set of tools to help to build front-end websites that use a headless CMS to manage content or get data from a
bunch of different data sources. An approach that is ever more typical on the modern web.

Strata is based on the idea of delivering robust, efficient HTML pages on the frontend reading content from a variety of
data sources. Basically the "front" of a headless CMS.

Delivery is via HTML, since this is the foundation of the web. You should consider your core page content cacheable, and
therefore the same for everyone. Personalised content can be accessed via JavaScript, which can avoid caching.

The aim is to deliver fast, accessible user experiences on the modern web.

## Layers

Strata consists of the following application layers:

* **Data layer** - retrieve content from data sources, includes automated decoding, caching and error handling
* **Frontend tools** - to help build the front-end website, including template helpers and caching

### Data layer

The data layer is powered by [Strata Data](https://github.com/strata/data), a separate project which can also be used to
help make data integration projects easier.

You connect to a **repository** which is responsible for accessing data from external APIs. Repositories use support
caching, events, and mapping data to content models. Repositories have custom methods for data retrieval that fits the
API. If caching is used then data is retrieved from the cache, making requests fast.

Data is returned from a repository in a raw format, for HTTP-based APIs this is a HTTP response.

Once you have retrieved data you can map it to a content object (or collection of content objects) via **mappers**.
These parse the response data into typed objects that you can pass to your templates to output to the frontend.

{% page-ref page="data/README.md" %}

### Templating

Content is outputted to the template layer, in Symfony this is Twig. Content models are designed to output content easily
and a range of template helpers (filters, functions) exist to automate content generation.

{% page-ref page="templating/README.md" %}

### Caching

Caching is built-in to enable high performance. We have Strata-powered websites running via WordPress API with sub 50ms
response times.

At the data layer Strata can automatically cache the raw API response data, removing external API requests for page generation.

{% page-ref page="development/caching.md" %}
