<?php
declare(strict_types=1);

namespace Studio24\Frontend\Api\Providers;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Studio24\Frontend\Api\RestApiAbstract;
use Studio24\Frontend\Api\ListResponse;
use Studio24\Frontend\Content\Pagination\Pagination;

class RestApi extends RestApiAbstract
{
    /**
     * Setup HTTP client
     *
     * @return Client
     * @throws \Studio24\Frontend\Exception\ApiException
     */
    public function setupHttpClient() : Client
    {
        return new Client([
            'base_uri' => $this->getBaseUri(),
            'headers' => [
                'User-Agent' => $this->getUserAgent(),
            ]
        ]);
    }

    /**
     * Return a collection of content items
     *
     * @param string $apiEndpoint API endpoint to query for posts
     * @param int $page Page number to return
     * @param array $options Options to use when querying data from WordPress
     * @return ListResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Studio24\Frontend\Exception\FailedRequestException
     * @throws \Studio24\Frontend\Exception\PermissionException
     * @throws \Studio24\Frontend\Exception\PaginationException
     */
    public function list(string $apiEndpoint, $page = 1, array $options = []) : ListResponse
    {
        $this->permissionRead();
        $this->expectedResponseCode(200);

        // @todo May need to create patterns for REST APIs for things like pagination, returning meta data. This is fixed for now.

        // Build query params
        $query = array_merge(['page' => $page], $options);

        $response = $this->get($apiEndpoint, ['query' => $query]);
        $data = $this->parseJsonResponse($response);

        if (isset($options['limit'])) {
            $limit = $options['limit'];
        } else {
            $limit = 10;
        }
        $pages = $this->getPagination($page, $limit, $response);

        $response = new ListResponse($data['results'], $pages);
        $response->setMetaData($data['meta']);
        return $response;
    }

    /**
     * Get a single content item
     *
     * API endpoint format is expected to be: base_url/content_type/id
     *
     * @param string $apiEndpoint API endpoint to query for posts
     * @param mixed $id Post ID
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Studio24\Frontend\Exception\FailedRequestException
     * @throws \Studio24\Frontend\Exception\PermissionException
     */
    public function getOne($apiEndpoint, $id) : array
    {
        $this->permissionRead();
        $this->expectedResponseCode(200);

        $response = $this->get(sprintf('%s/%s', $apiEndpoint, $id));
        $data = $this->parseJsonResponse($response);

        return $data;
    }

    /**
     * Return pagination object for current request
     *
     * We expect pagination metadata to be stored in:
     *
     *  "meta": {
     *      "total_results": 148,
     *      "limit": 10,
     *      "page": 1
     *  },
     *
     * @todo Is limit the right word here? Consider per_page
     *
     * @param int $page Current page number
     * @param int $limit Number of results per page
     * @param ResponseInterface $response
     * @return Pagination
     * @throws \Studio24\Frontend\Exception\FailedRequestException
     * @throws \Studio24\Frontend\Exception\PaginationException
     */
    public function getPagination(int $page, int $limit, ResponseInterface $response) : Pagination
    {
        $pages = new Pagination();

        // @todo remove this duplication
        $data = $this->parseJsonResponse($response);

        if (isset($data['meta']) && isset($data['meta']['total_results'])) {
            $pages->setTotalResults((int) $data['meta']['total_results'])
                ->setResultsPerPage($limit)
                ->setPage($page);
        }

        return $pages;
    }
}
