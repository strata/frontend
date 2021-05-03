<?php

declare(strict_types=1);

namespace Strata\Frontend\Api\Providers;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Strata\Frontend\Api\ListResponse;
use Strata\Frontend\Content\Pagination\Pagination;
use Strata\Frontend\Api\RestApiAbstract;
use Strata\Frontend\Exception\ApiException;
use Strata\Frontend\Utils\FileInfoFormatter;

/**
 * @deprecated Kept for reference while integrating Strata Data
 */
class Wordpress extends RestApiAbstract
{

    /**
     * Setup HTTP client
     *
     * @return Client
     * @throws ApiException
     */
    public function setupHttpClient(): Client
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
     * @throws \Strata\Frontend\Exception\FailedRequestException
     * @throws \Strata\Frontend\Exception\PermissionException
     * @throws \Strata\Frontend\Exception\PaginationException
     */
    public function listPosts($apiEndpoint, $page = 1, array $options = []): ListResponse
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
     * @throws \Strata\Frontend\Exception\PaginationException
     */
    public function getPagination(int $page, int $limit, ResponseInterface $response): Pagination
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
     * @throws \Strata\Frontend\Exception\FailedRequestException
     * @throws \Strata\Frontend\Exception\PermissionException
     */
    public function getPost($apiEndpoint, int $id): array
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
     * @throws \Strata\Frontend\Exception\FailedRequestException
     * @throws \Strata\Frontend\Exception\PermissionException
     */
    public function getAuthor(int $id): array
    {
        $this->permissionRead();
        $this->expectedResponseCode(200);

        $response = $this->get("wp/v2/users/$id");
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
     * @throws \Strata\Frontend\Exception\FailedRequestException
     * @throws \Strata\Frontend\Exception\PermissionException
     */
    public function getMenu(int $id): array
    {
        $this->permissionRead();
        $this->expectedResponseCode(200);

        $response = $this->get("wp-api-menus/v2/menus/$id");
        $data = $this->parseJsonResponse($response);

        return $data;
    }


    /**
     * Get media item data from ID
     *
     * @param int $id
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Strata\Frontend\Exception\FailedRequestException
     * @throws \Strata\Frontend\Exception\PermissionException
     */
    public function getMedia(int $id): array
    {
        $this->permissionRead();
        $this->expectedResponseCode(200);

        $response = $this->get("wp/v2/media/$id");
        $data = $this->parseJsonResponse($response);

        return $data;
    }

    /**
     * Get media file size from path
     *
     * @param string $url
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Strata\Frontend\Exception\FailedRequestException
     * @throws \Strata\Frontend\Exception\PermissionException
     */
    public function getMediaFileSize(string $url): string
    {
        $this->permissionRead();
        $this->expectedResponseCode(200);

        $response = $this->head($url, []);

        $contentLength = $response->getHeader('Content-length');

        $size = '0 B';

        if (empty($contentLength)) {
            return $size;
        } else {
            $contentLength = $contentLength[0];
        }

        $size = FileInfoFormatter::formatFileSize($contentLength);

        return $size;
    }

    /**
     * Retrieves all terms of a taxonomy
     *
     * @param string $taxonomy
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Strata\Frontend\Exception\FailedRequestException
     * @throws \Strata\Frontend\Exception\PermissionException
     */
    public function getTaxonomyTerms(string $taxonomy): array
    {
        $this->permissionRead();
        $this->expectedResponseCode(200);

        $response = $this->get("wp/v2/$taxonomy", ['query' => ['per_page' => 100]]);
        $data = $this->parseJsonResponse($response);

        return $data;
    }

    /**
     * Returns single term data
     *
     * @param string $taxonomy
     * @param int $termID
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Strata\Frontend\Exception\FailedRequestException
     * @throws \Strata\Frontend\Exception\PermissionException
     */
    public function getTerm(string $taxonomy, int $termID): array
    {
        $this->permissionRead();
        $this->expectedResponseCode(200);

        $response = $this->get("wp/v2/$taxonomy/$termID");
        $data = $this->parseJsonResponse($response);

        return $data;
    }
}
