<?php

declare(strict_types=1);

namespace Strata\Frontend\Data\Repository\WordPress;

use Strata\Data\Collection;
use Strata\Data\Http\Http;
use Strata\Data\Http\Response\CacheableResponse;
use Strata\Data\Http\Rest;
use Strata\Data\Mapper\MapCollection;
use Strata\Data\Mapper\MapItem;
use Strata\Data\Traits\EventDispatcherTrait;
use Strata\Data\Transform\Data\CallableData;
use Strata\Data\Transform\Value\DateTimeValue;
use Strata\Data\Transform\Value\IntegerValue;
use Strata\Frontend\Api\Providers\RestApi;
use Strata\Frontend\Content\Page;
use Strata\Frontend\Data\ContentRepository;
use Strata\Frontend\Data\Repository\WordPress\Mapper\MapPage;
use Strata\Frontend\Data\RepositoryCommonTrait;
use Strata\Frontend\Data\RepositoryInterface;
use Strata\Frontend\Data\RepositoryMapperInterface;
use Strata\Frontend\Data\Repository\WordPress\PageRepositoryMapTrait;
use Strata\Frontend\Schema\Schema;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Class to access content from WordPress API
 */
class WordPress extends ContentRepository implements RepositoryInterface
{

    /**
     * Constructor
     *
     * @param string $baseUrl API base URI
     * @param ?Schema|string $contentSchema Content schema content file or schema object
     */
    public function __construct(string $baseUrl, $contentSchema = null)
    {
        $this->provider = new Rest($baseUrl);

        if (null !== $contentSchema) {
            $this->setContentSchema($contentSchema);
        }
    }

    /**
     * Return Strata Data provider to use to retrieve data
     *
     * @return Rest
     */
    public function getProvider(): Rest
    {
        return $this->provider;
    }

    /**
     * Set API authorization to use with all requests
     *
     * Please note WordPress does not usually require authorization for GET requests, so it is recommend to only use
     * this with requests that require authentication.
     *
     * Requires Application Passwords WordPress plugin
     * @see https://wordpress.org/plugins/application-passwords/
     * @param string $token
     */
    public function setAuthorization(string $username, string $password)
    {
        $this->getProvider()->setDefaultOptions([
            'auth_basic' => [$username, $password]
        ]);
    }

    /**
     * Check the WordPress API is available
     *
     * @return bool
     * @throws \JsonException
     * @throws \Strata\Data\Exception\BaseUriException
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function ping(): bool
    {
        // WordPress API root should return a 200
        return $this->getProvider()->exists('');
    }

    public function getPage(int $id, array $queryParams = [], array $requestOptions = []): ResponseInterface
    {
        // endpoint = wp/v2/posts/\d
        return $this->getProvider()->get($this->getContentApiEndpoint($id), $queryParams, $requestOptions);
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
    public function mapPage(CacheableResponse $response, string $contentPropertyPath = null): Page
    {
        $mapper = new MapPage();
        $mapper->setContentType($this->getContentType());

        $data = $this->getProvider()->decode($response);
        return $mapper->map($data, $contentPropertyPath);
    }

    public function getItem(array $data, string $contentPropertyPath): Page
    {
        $mapper = $this->getRepositorySchema()->getItemMapper();
        return $mapper->map($data, $contentPropertyPath);
    }

    /**
     * Return collection of entries
     *
     * @param array $data
     * @param string $contentPropertyPath
     * @param string $totalPropertyPath
     * @return Collection Collection of Page objects
     * @throws \Strata\Data\Exception\MapperException
     */
    public function getList(array $data, string $contentPropertyPath, string $totalPropertyPath): Collection
    {
        $mapper = $this->getRepositorySchema()->getCollectionMapper();
        $mapper->totalResults($totalPropertyPath);
        return $mapper->map($data, $contentPropertyPath);
    }
}
