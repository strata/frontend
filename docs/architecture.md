# Architecture

Strata Frontend is a collection of tools to help build frontend websites from different data sources.

At its simplest it is:

* **Data layer** - retrieve content from data sources, includes automated decoding, caching and error handling
* **Content schema** - maps data to strongly typed content objects so they can be used in a consistent fashion
* **Templating** - use [Twig](https://twig.symfony.com/) to output efficient templating on the frontend
* **Helpers** - tools to help build the frontend (e.g. pagination)
* **Caching** - help speed up data retrieval (both at the data layer and full-page caching)
* An **event system** - hook into different events to enable things like logging and profiling

## Data layer
The data layer is powered by [Strata Data](https://github.com/strata/data).

You connect to a **repository** which is responsible for accessing data from external APIs. Repositories 
support caching, events, and mapping data to content models.

Repositories have custom methods for data retrieval that fits the API. If caching is used then 
data is retrieved from the cache, making requests fast.

Data is returned from a repository in a raw format, for HTTP-based APIs this is a HTTP response. 

Once you have retrieved data you can map it to a content object (or collections) via **mappers**.
These parse the response data into typed objects that you can pass to your templates to output to the frontend. 

## Content schema
To define how content objects are returned you need to define a **content schema** in configuration (YAML files).

This helps define the content fields that are mapped to objects (each API provider has custom mapping classes to help 
define how data is mapped to objects - and you can create your own).

Creating strongly typed models is useful because:

* You only pass the data you want to the template layer
* Content is available in a consistent, simple format (e.g. `getTitle()` rather than API response fields such as `response.data.title.rendered`)
* Content is typed and you can use template helpers (such as outputting formatted dates) 
* Content is accessed in a similar way across all APIs you may use

Content model configuration can be fiddly, so a CLI tool exists to help generate and update existing content model config 
files.
