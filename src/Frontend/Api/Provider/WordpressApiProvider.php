<?php
declare(strict_types=1);

namespace Studio24\Frontend\Api\Provider;

use Psr\Http\Message\ResponseInterface;
use Studio24\Frontend\Content\Pagination\Pagination;
use Studio24\Frontend\Utils\FileInfoFormatter;

class WordpressApiProvider extends RestApiProvider
{

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
     * Return author
     *
     * @param int $id User ID
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Studio24\Frontend\Exception\FailedRequestException
     * @throws \Studio24\Frontend\Exception\PermissionException
     * @throws \Studio24\Frontend\Exception\NotFoundException
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
     * @throws \Studio24\Frontend\Exception\FailedRequestException
     * @throws \Studio24\Frontend\Exception\PermissionException
     * @throws \Studio24\Frontend\Exception\NotFoundException
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
     * @throws \Studio24\Frontend\Exception\FailedRequestException
     * @throws \Studio24\Frontend\Exception\PermissionException
     * @throws \Studio24\Frontend\Exception\NotFoundException
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
     * @throws \Studio24\Frontend\Exception\FailedRequestException
     * @throws \Studio24\Frontend\Exception\PermissionException
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
     * @throws \Studio24\Frontend\Exception\FailedRequestException
     * @throws \Studio24\Frontend\Exception\PermissionException
     * @throws \Studio24\Frontend\Exception\NotFoundException
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
     * @throws \Studio24\Frontend\Exception\FailedRequestException
     * @throws \Studio24\Frontend\Exception\PermissionException
     * @throws \Studio24\Frontend\Exception\NotFoundException
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
