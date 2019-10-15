<?php
declare(strict_types=1);

namespace Studio24\Frontend\Api\Provider;

use Psr\Http\Message\ResponseInterface;
use Studio24\Frontend\Api\Response\ApiListResponse;
use Studio24\Frontend\Content\Pagination\Pagination;

class RestApiProvider extends AbstractApiProvider
{

    /**
     * Return a collection of content items
     *
     * @param string $apiEndpoint API endpoint to query for posts
     * @param int $page Page number to return
     * @param array $options Options to use when querying data from WordPress
     * @return ApiListResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Studio24\Frontend\Exception\FailedRequestException
     * @throws \Studio24\Frontend\Exception\PermissionException
     * @throws \Studio24\Frontend\Exception\NotFoundException
     * @throws \Studio24\Frontend\Exception\PaginationException
     */
    public function list(string $apiEndpoint, $page = 1, array $options = []) : ApiListResponse
    {
        $this->permissionRead();
        $this->expectedResponseCode(200);

        // Build query params
        $query = array_merge(['page' => $page], $options);

        $response = $this->get($apiEndpoint, ['query' => $query]);
        $data = $this->parseJsonResponse($response);

        $limit = $this->resolvePaginationLimit($options);

        $pages = $this->getPagination($page, $limit, $response);

        return new ApiListResponse($data, $pages);
    }

    /**
     * Get a single content item
     *
     * @see https://developer.wordpress.org/rest-api/reference/posts/#retrieve-a-post
     *
     * @param string $apiEndpoint API endpoint to query for posts
     * @param int $id Post ID
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Studio24\Frontend\Exception\FailedRequestException
     * @throws \Studio24\Frontend\Exception\PermissionException
     * @throws \Studio24\Frontend\Exception\NotFoundException
     */
    public function getOne($apiEndpoint, int $id): array
    {
        $this->permissionRead();
        $this->expectedResponseCode(200);

        $response = $this->get(sprintf('%s/%d', $apiEndpoint, $id));
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
