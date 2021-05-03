<?php

declare(strict_types=1);

namespace Strata\Frontend\Data\Repository\CraftCms;

use Strata\Data\Collection;
use Strata\Data\Http\GraphQL;
use Strata\Data\Http\Response\CacheableResponse;
use Strata\Frontend\Content\Page;
use Strata\Frontend\Data\ContentRepository;
use Strata\Frontend\Data\Repository\CraftCms\Mapper\MapPage;
use Strata\Frontend\Data\Repository\CraftCms\Mapper\MapPageCollection;
use Strata\Frontend\Data\RepositoryCommonTrait;
use Strata\Frontend\Data\RepositoryInterface;
use Strata\Frontend\Schema\Schema;

/**
 * Class to help access content from CraftCMS API
 */
class CraftCms extends ContentRepository implements RepositoryInterface
{

    /**
     * Constructor
     *
     * @param string $baseUrl API base URI
     * @param ?Schema|string $contentSchema Content schema content file or schema object
     */
    public function __construct(string $baseUrl, $contentSchema = null)
    {
        $this->provider = new GraphQL($baseUrl);

        if (null !== $contentSchema) {
            $this->setContentSchema($contentSchema);
        }
    }

    /**
     * Return Strata Data provider to use to retrieve data
     *
     * @return GraphQL
     */
    public function getProvider(): GraphQL
    {
        return $this->provider;
    }

    /**
     * Set API authorization token to use with all requests
     *
     * @param string $token
     */
    public function setAuthorization(string $token)
    {
        $this->getProvider()->setDefaultOptions([
            'auth_bearer' => $token
        ]);
    }

    /**
     * Run a ping request on the GraphQL API to check it is responding OK
     *
     * @return bool
     * @throws \JsonException
     * @throws \Strata\Data\Exception\BaseUriException
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function ping(): bool
    {
        return $this->getProvider()->ping();
    }

    /**
     * Run a query against the GraphQL API
     *
     * @param string $query GraphQL query
     * @param array $variables Array of variables to pass to GraphQL (key & value pairs)
     * @param string|null $operationName Operation name to execute (only required if query contains multiple operations)
     * @return CacheableResponse
     * @throws \JsonException
     * @throws \Strata\Data\Exception\BaseUriException
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function query(string $query, array $variables = [], ?string $operationName = null): CacheableResponse
    {
        return $this->getProvider()->query($query, $variables, $operationName);
    }

    /**
     * Map API response to an item
     *
     * @param CacheableResponse $response
     * @param string $contentPropertyPath
     * @return Page
     * @throws \Strata\Data\Exception\DecoderException
     * @throws \Strata\Data\Exception\MapperException
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function mapPage(CacheableResponse $response, string $contentPropertyPath): Page
    {
        $mapper = new MapPage();
        $mapper->setContentType($this->getContentType());

        $data = $this->getProvider()->decode($response);
        return $mapper->map($data, $contentPropertyPath);
    }

    /**
     * Map API response to a collection of items
     *
     * @param CacheableResponse $response
     * @param string $contentPropertyPath
     * @param string $totalPropertyPath
     * @return Collection Collection of Page objects
     * @throws \Strata\Data\Exception\MapperException
     */
    public function mapCollection(CacheableResponse $response, string $contentPropertyPath, string $totalPropertyPath): Collection
    {
        $mapper = new MapPageCollection();
        $mapper->setContentType($this->getContentType());
        $mapper->setTotalResults($totalPropertyPath);

        $data = $this->getProvider()->decode($response);
        return $mapper->map($data, $contentPropertyPath);
    }

}
