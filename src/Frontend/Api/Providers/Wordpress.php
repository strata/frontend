<?php
declare(strict_types=1);

namespace Studio24\Frontend\Api\Providers;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Studio24\Frontend\Api\ListResponse;
use Studio24\Frontend\Content\Pagination\Pagination;
use Studio24\Frontend\Api\RestApiAbstract;
use Studio24\Frontend\Exception\ApiException;

class Wordpress extends RestApiAbstract
{

    /**
     * Setup HTTP client
     *
     * @return Client
     * @throws ApiException
     */
    public function setupHttpClient() : Client
    {
        return new Client([
            'base_uri' => $this->getBaseUri(),
            'headers' => [
                'User-Agent'            => $this->getUserAgent(),
            ]
        ]);
    }

    /**
     * Get multiple posts
     *
     * @see https://developer.wordpress.org/rest-api/reference/posts/#list-posts
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
    public function listPosts($apiEndpoint, $page = 1, array $options = []) : ListResponse
    {
        $this->permissionRead();
        $this->expectedResponseCode(200);

        // Build query params
        $query = array_merge(['page' => $page], $options);

        $response = $this->get($apiEndpoint, ['query' => $query]);
        $data = $this->parseJsonResponse($response);

        if (isset($options['per_page'])) {
            $limit = $options['per_page'];
        } else {
            $limit = 10;
        }
        $pages = $this->getPagination($page, $limit, $response);

        return new ListResponse($data, $pages);
    }

    /**
     * Return pagination object for current request
     *
     * @see https://developer.wordpress.org/rest-api/using-the-rest-api/pagination/
     *
     * @param int $page Current page number
     * @param int $limit Number of results per page
     * @param ResponseInterface $response
     * @return Pagination
     * @throws \Studio24\Frontend\Exception\PaginationException
     */
    public function getPagination(int $page, int $limit, ResponseInterface $response) : Pagination
    {
        $pages = new Pagination();

        if ($response->hasHeader('X-WP-Total')) {
            $pages->setTotalResults((int) $response->getHeaderLine('X-WP-Total'))
                  ->setResultsPerPage($limit)
                  ->setPage($page);
        }

        return $pages;
    }

    /**
     * Get a single post
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
     */
    public function getPost($apiEndpoint, int $id) : array
    {
        $this->permissionRead();
        $this->expectedResponseCode(200);

        $response = $this->get(sprintf('%s/%d', $apiEndpoint, $id));
        $data = $this->parseJsonResponse($response);

        return $data;
    }

    /**
     * Return author
     *
     * @param int $id User ID
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Studio24\Frontend\Exception\FailedRequestException
     * @throws \Studio24\Frontend\Exception\PermissionException
     */
    public function getAuthor(int $id): array
    {
        $this->permissionRead();
        $this->expectedResponseCode(200);

        $response = $this->get("users/$id");
        $data = $this->parseJsonResponse($response);

        return $data;
    }

    /**
     * Get menu data
     *
     * URL format https://domain.com/wp-json/wp-api-menus/v2/menus/2
     * @see https://github.com/unfulvio/wp-api-menus
     *
     * @param int $id Menu ID
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Studio24\Frontend\Exception\FailedRequestException
     * @throws \Studio24\Frontend\Exception\PermissionException
     */
    public function getMenu(int $id): array
    {
        $this->permissionRead();
        $this->expectedResponseCode(200);

        // @todo Need to alter other WP base URLs to https://domain.com/wp-json/ & API URL endpoints to the format: wp/v2/posts
        $response = $this->get("wp-api-menus/v2/menus/$id");
        $data = $this->parseJsonResponse($response);

        return $data;
    }


    /**
     * @param int $id
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Studio24\Frontend\Exception\FailedRequestException
     * @throws \Studio24\Frontend\Exception\PermissionException
     */
    public function getMedia(int $id): array
    {
        $this->permissionRead();
        $this->expectedResponseCode(200);

        $response = $this->get("media/$id");
        $data = $this->parseJsonResponse($response);

        return $data;
    }


}
