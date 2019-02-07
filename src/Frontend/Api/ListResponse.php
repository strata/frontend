<?php
declare(strict_types=1);

namespace Studio24\Frontend\Api;

use Studio24\Frontend\Content\Pagination\PaginationInterface;

/**
 * Simple class to model the response data for lists of data
 *
 * @package Studio24\Frontend\Api
 */
class ListResponse
{
    /**
     * Array of response data
     *
     * @var array
     */
    protected $responseData = [];

    /**
     * Pagination object (for lists)
     *
     * @var PaginationInterface
     */
    protected $pagination;

    /**
     * Constructor
     *
     * @param array $data
     * @param PaginationInterface $pagination
     */
    public function __construct(array $data, PaginationInterface $pagination)
    {
        $this->pagination = $pagination;
        $this->responseData = $data;
    }

    /**
     * Return pagination object
     *
     * @return PaginationInterface
     */
    public function getPagination(): PaginationInterface
    {
        return $this->pagination;
    }

    /**
     * Return response data as an array
     *
     * @return array
     */
    public function getResponseData(): array
    {
        return $this->responseData;
    }

}
