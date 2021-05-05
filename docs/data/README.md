# Accessing data

You can access data either via repository classes or by making direct data requests via data providers.

## Repositories

Repositories make it easier to get data from specific data sources. They are basically wrappers around data providers and 
offer the same functionality with additional helpers to do things like setup authentication and map data automatically 
to content objects.

* [CraftCMS](craftcms.md)
* WordPress (currently broken - fix in progress)

## Data providers

Repositories use data providers to access data. You can use them directly too.

The basic principle is make a request to an API, return a HTTP response, decode it, and either make use of the decoded array
or map this to an object. 

* [HTTP data provider](https://docs.strata.dev/data/data-providers/http)
* [Transforming and mapping data](https://docs.strata.dev/data/changing-data/changing-data)
