<?php
declare(strict_types=1);

namespace Studio24\Frontend\Api\Response;

use Studio24\Frontend\Content\Pagination\PaginationInterface;

/**
 * Simple class to model the response data for lists of data
 *
 * @package Studio24\Frontend\Api\Response
 */
class ApiListResponse
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

        if (isset($data['results']) && is_array($data['results'])) {
            $this->setResponseData($data['results']);
        } else {
            $this->setResponseData($data);
        }

        if (isset($data['meta'])) {
            $this->setMetadata($data['meta']);
        }
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
     * @return ApiListResponse
     */
    public function setResponseData(array $responseData): ApiListResponse
    {
        $this->responseData = $responseData;
        return $this;
    }

    /**
     * Set the metadata
     *
     * @param array $metadata
     * @return ApiListResponse
     */
    public function setMetadata(array $metadata): ApiListResponse
    {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * Add one meta data item
     *
     * @param string $key
     * @param $value
     * @return ApiListResponse
     */
    public function addMetadata(string $key, $value): ApiListResponse
    {
        $this->metadata[$key] = $value;
        return $this;
    }

    /**
     * Set pagination object
     *
     * @param PaginationInterface $pagination
     * @return ApiListResponse
     */
    public function setPagination(PaginationInterface $pagination): ApiListResponse
    {
        $this->pagination = $pagination;
        return $this;
    }
}
