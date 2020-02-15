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
     * Array of metadata
     *
     * This is for any data that is not part of the paginated results
     *
     * @var array
     */
    protected $metadata = [];

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
        $this->setPagination($pagination);
        $this->setResponseData($data);
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

    /**
     * Return metadata
     *
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * Set the response data
     *
     * @param array $responseData
     * @return ListResponse
     */
    public function setResponseData(array $responseData): ListResponse
    {
        $this->responseData = $responseData;
        return $this;
    }

    /**
     * Set the metadata
     *
     * @param array $metadata
     * @return ListResponse
     */
    public function setMetadata(array $metadata): ListResponse
    {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * Add one meta data item
     *
     * @param string $key
     * @param $value
     * @return ListResponse
     */
    public function addMetadata(string $key, $value): ListResponse
    {
        $this->metadata[$key] = $value;
        return $this;
    }

    /**
     * Set pagination object
     *
     * @param PaginationInterface $pagination
     * @return ListResponse
     */
    public function setPagination(PaginationInterface $pagination): ListResponse
    {
        $this->pagination = $pagination;
        return $this;
    }
}
