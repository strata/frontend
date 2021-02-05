<?php

declare(strict_types=1);

namespace Strata\Data\Storage;

use Strata\Data\Metadata\Metadata;
use Strata\Data\Metadata\RepositoryInterface;

interface StorageInterface
{
    /**
     * Initialise storage mechanism
     * @param array $options
     * @return mixed
     */
    public function init(array $options = []);

    /**
     * Set the key used to differentiate the current entity type in the storage
     *
     * @param string $key
     * @return mixed
     */
    public function setKey(string $key);

    /**
     * Get the key used to differentiate the current entity type in the storage
     *
     * @return string
     */
    public function getKey(): string;

    /**
     * Gets all items
     *
     * @return array
     */
    public function all(): array;

    /**
     * Does a metadata item exist for ID?
     *
     * @param $id
     * @return bool
     */
    public function has($id): bool;

    /**
     * Get a metadata item via ID
     *
     * @param $id
     * @return bool
     */
    public function get($id);

    /**
     * Write one metadata item to storage
     *
     * @param array $data
     * @return mixed
     */
    public function save(array $data);

    /**
     * Delete one metadata item based on ID
     *
     * @param $id
     * @return mixed
     */
    public function delete($id);

    /**
     * Delete all metadata items
     *
     * @return mixed
     */
    public function deleteAll();

    /**
     * Search for metadata items by attribute
     *
     * @param $attribute
     * @param $keyword
     * @return array of items
     */
    public function search($attribute, $keyword): array;

    /**
     * Creates the table required to store the data if required
     *
     * @param \Strata\Data\Metadata\RepositoryInterface $repository
     */
    public function createTableIfItDoesntExist(RepositoryInterface $repository): void;
}
