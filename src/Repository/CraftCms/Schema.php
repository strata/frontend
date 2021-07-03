<?php

declare(strict_types=1);

namespace Strata\Frontend\Repository\CraftCms;

use Strata\Data\Traits\IterableTrait;
use Strata\Frontend\Content\IterableContentTrait;
use Strata\Frontend\Schema\Api\ApiMethod;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class Schema implements \SeekableIterator, \Countable, \ArrayAccess
{
    use IterableContentTrait;

    private CraftCms $api;

    public function __construct(string $baseUri)
    {
        $this->api = new CraftCms($baseUri);

        // enable cache to avoid rebuilding data on every request
        $this->api->setCache(new FilesystemAdapter());
        $this->inspect();
    }

    public function add(string $methodName, ApiMethod $method)
    {
        $this->collection[$methodName] = $method;
    }

    public function current(): ApiMethod
    {
        return $this->collection[$this->position];
    }

    public function getFields(string $methodName): ?array
    {
        if (isset($this->collection[$methodName])) {
            return $this->collection[$methodName];
        }
        return null;
    }

    /**
     * Inspect schema, populate $this->methods
     * @see https://graphql.org/learn/introspection/
     * @return array
     */
    public function inspect(): array
    {
        $query = <<<EOD
{
   __schema {
    types {
      name
      kind
      fields {
      	name
        description
        args {
          name
          description
          defaultValue
        } 
        type {
          name
          kind
          ofType {
            name
            kind
          }
        }
      }
    }
  }
}
EOD;
        $response = $this->api->query($query);
        $results = $this->api->decode($response);

        // list interface types
        $interfaces = [];
        $queryData = [];
        foreach ($results['__schema']['types'] as $type) {
            if ($type['name'] === 'Query') {
                $queryData[] = $type;
                continue;
            }
            if ($type['kind'] !== 'INTERFACE') {
                continue;
            }
            $name = $type['name'];
            $fields = [];
            foreach ($type['fields'] as $field) {
                $typeField = [
                    'name' => $field['name'],
                    'description' => $field['description'],
                    'kind' => $field['type']['kind'],
                    'type' => $field['type']['name'],
                ];
                if ($typeField['kind'] === 'LIST') {
                    $typeField['list_type'] =  $field['type']['ofType']['name'];
                }
                $fields[] = $typeField;
            }
            $interfaces[] = [
                'name' => $name,
                'fields' => $fields
            ];
        }

        // list supported queries
        $queries = [];
        foreach ($queryData as $query) {
            foreach ($query['fields'] as $query) {
                $name = $query['name'];
                if ($query['type']['name'] !== null) {
                    $type = $query['type']['name'];
                } else {
                    switch ($query['type']['kind']) {
                        case 'LIST':
                        case 'NOT_NULL':
                            if (is_array($query['type']['kind']) && isset($query['type']['kind']['name'])) {
                                $type = $query['type']['kind']['name'];
                            } else {
                                $type = $query['type']['kind'];
                            }
                            break;
                        default:
                            throw new \Exception(var_dump($query['type']));
                    }
                }
                $queries[] = [
                    'name' => $query['name'],
                    'type' => $type,
                ];
            }
            break;
        }
    }

}