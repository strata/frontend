<?php

declare(strict_types=1);

namespace Strata\Data\Storage;

use Strata\Data\Metadata\RepositoryInterface;

class ArrayStorage implements StorageInterface
{

    /**
     * @var string
     */
    protected $key = '';

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Initialise storage mechanism
     * @param array $options
     * @return mixed
     */
    public function init(array $options = [])
    {
        $this->data = [];
    }

    /**
     * Set the key used to differentiate the current entity type in the storage
     *
     * @param string $key
     * @return mixed
     */
    public function setKey(string $key): ArrayStorage
    {
        $this->key = $key;
        return $this;
    }

    /**
     * Get the key used to differentiate the current entity type in the storage
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Does a metadata item exist for ID?
     *
     * @param $id
     * @return bool
     */
    public function has($id): bool
    {
        return array_key_exists($id, $this->data);
    }

    /**
     * Get a metadata item via ID
     *
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new \Exception('Item with id: ' . $id . ' could not be found - Reason: Not found in storage.');
        }

        return $this->data[$id];
    }

    /**
     * Write one metadata item to storage
     *
     * @param array $data
     * @return mixed
     */
    public function save(array $data)
    {
        if (isset($data['id']) && !empty($data['id'])) {
            $this->data[$data['id']] = $data;
            return $data['id'];
        }

        // Save the data
        $this->data[] = $data;

        // Find the ID of the inserted item
        end($this->data);
        $key = key($this->data);

        // Save this ID as a key in the array so we always have it
        $data = $this->data[$key];
        $data['id'] = $key;

        // Save the item with this new key added
        $this->data[$key] = $data;

        return $data['id'];
    }

    /**
     * Delete one metadata item based on ID
     *
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        if (!$this->has($id)) {
            throw new \Exception('Item with id: ' . $id . ' could not be deleted - Reason: Not found in storage.');
        }

        unset($this->data[$id]);
    }

    /**
     * Delete all metadata items
     *
     * @return mixed
     */
    public function deleteAll()
    {
        $this->init();
    }


    /**
     * Gets all items
     *
     * @return array
     */
    public function all(): array
    {
        // TODO: Implement all() method.
    }

    /**
     * Search for metadata items by attribute
     *
     * @param $attribute
     * @param $keyword
     * @return array of items
     */
    public function search($attribute, $keyword): array
    {
        // TODO: Implement search() method.
    }

    /**
     * Creates the table required to store the data if required
     *
     * @param \Strata\Data\Metadata\RepositoryInterface $repository
     */
    public function createTableIfItDoesntExist(RepositoryInterface $repository): void
    {
        // Nothing is required to be set up for the Array Storage system
        return;
    }
}
