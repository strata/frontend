# CraftCMS

## Setup

First ensure you have a [GraphQL public schema](https://craftcms.com/docs/3.x/graphql.html) setup in CraftCMS.

Setup your repository connection to the CraftCMS API via:

```php
use Strata\Frontend\Repository\CraftCms\CraftCms;

$api = new CraftCms('https://example.com/api');
```

You can test you have a valid API connection via:

```php
$success = $api->ping();
```

### Content schema

You can set the content schema by passing the path to your `content-schema.yaml` file either as the second argument to 
the `CraftCms` constructor, or by calling the method:

```php
$api->setContentSchema($configSchemaPath);
```

Please note you can call your content schema file what you wish, the above is just a convention.

### Authentication
You shouldn't have to authenticate for access to the public GraphQL schema. If you need to access a private GraphQL 
schema you will need to pass your authentication token (generated from the CMS).

```php
$api->setAuthorization($token);
```

## Usage

### query

GraphQL has only one way to access data, a GraphQL query. This can be used to get a variety of data in one API request.

You can lean more about [GraphQL queries](https://graphql.org/learn/queries/). 
You can use the [GraphQL Explorer](https://craftcms.com/docs/3.x/graphql.html#using-the-graphiql-ide) in your CMS to
review what data is available and how to craft GraphQL queries.

Example GraphQL query:

```graphql
{
  entry(section: "blogPosts", id: "101") {
    title
    slug
    uri
    postDate
    status
    language
    content
    ... on blogPosts_default_Entry {
      authors {
        ... on authors_author_BlockType {
          authorName
          authorEmailAddress
        }
      }
    }
  }
  latest_posts_count: entryCount(section: "pressReleases")
  latest_posts: entries(section: "blogPosts", limit: 3, offset: 0) {
    postDate
    status
    title
    url
  }
}
```

Access data from CraftCMS via:

```php
// returns a CacheableResponse object
$response = $api->query($query);
```

## Using data

### Raw array

You can decode the response to an array via:

```php
$data = $api->decode($response);
```

### Content object

You can map the response to a content objects via `mapItem()` and `mapCollection()`. This requires a valid 
[content schema](../development/content-schema.md) to be defined and set to the repository object.

`mapItem` takes two arguments: the response and the path to the content in the GraphQL response. The path is written as 
a property path. Array properties need to be written in index notation, specifying array keys within square brackets.

For example `[entry]` accesses `$data['entry']`

Read more about [accessing properties](https://docs.strata.dev/data/changing-data/property-paths).

`mapCollection` takes three arguments: the response, the path to the content in the GraphQL response, and the path to the 
total results field in the GraphQL response. This is used to automatically set pagination.

```php
// returns a Page object
$page = $api->mapItem($response, '[entry]');

// returns a PageCollection object
$latestPosts = $api->mapCollection($response, '[latest_posts]', '[latest_posts_count]');
```

These objects can then be passed to your view templates. The collection object has in-built pagination accessed via
`$latestPosts->getPagination()`.

## Passing variables

You can pass variables in your GraphQL query via the second argument to `query`.

Example GraphQL query:

```graphql
{
  entries(section: "blogPosts", limit: 3, offset: $offset) {
    postDate
    title
    url
  }
}
```

Pass an array of variables to the GraphQL query:

```php
$response = $api->query($query, ['offset' => 5]);
```

## Dealing with errors

GraphQL returns errors as a 200 HTTP response. Strata deals with GraphQL errors and throws a `Strata\Data\Exception\GraphQLException` for any 
returned errors. 

The returned GraphQL errors are set as the exception message. You can also access other information about the HTTP 
request from the exception object (if you try/catch it).

Return GraphQL errors as an array:

```php
$errors = $exception->getResponseErrorData();
```

You can confirm the last GraphQL query sent to the API via:

```php 
$lastQuery = $exception->getLastQuery();
```

GraphQL can return partial response data with errors, you can access this via:

```php
$partialData = $exception->getResponseData();
```

You can also display a HTTP trace (a summary of the HTTP request made and the response):

```php
$trace = $exception->getRequestTrace();
```



