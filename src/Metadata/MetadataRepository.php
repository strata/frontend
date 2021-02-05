<?php

declare(strict_types=1);

namespace Strata\Data\Metadata;

use Strata\Data\Storage\StorageInterface;

class MetadataRepository implements RepositoryInterface
{

    /**
     * @var string
     */
    protected $identifier = 'metadata';

    /**
     * @var \Strata\Data\Storage\StorageInterface
     */
    protected $storage;

    /**
     * MetadataRepository constructor.
     * @param \Strata\Data\Storage\StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
        $this->storage->setKey($this->identifier);
        $this->storage->createTableIfItDoesntExist($this);
    }

    /**
     * @return array
     * @throws \Strata\Data\Exception\InvalidMetadataId
     */
    public function all(): array
    {
        $items = [];
        foreach ($this->storage->all() as $item) {
            $items[] = $this->createObjectFromArray($item);
        }

        return $items;
    }

    /**
     * @param \Strata\Data\Metadata\Metadata $metadata
     * @return \Strata\Data\Metadata\Metadata
     * @throws \Strata\Data\Exception\InvalidMetadataId
     */
    public function store(Metadata $metadata): Metadata
    {
        $id = $this->storage->save($metadata->toArray());

        if (empty($metadata->getId())) {
            $metadata->setId($id);
        }

        return $metadata;
    }

    /**
     * @param $id
     * @return bool
     */
    public function delete($id): bool
    {
        if (!$this->exists($id)) {
            return false;
        }

        $this->storage->delete($id);

        return true;
    }

    /**
     * @param $id
     * @return bool
     */
    public function exists($id)
    {
        return $this->storage->has($id);
    }

    /**
     * @param $id
     * @return \Strata\Data\Metadata\Metadata|null
     * @throws \Strata\Data\Exception\InvalidMetadataId
     */
    public function find($id): ?Metadata
    {
        if (!$this->exists($id)) {
            return null;
        }

        $data = $this->storage->get($id);
        return $this->createObjectFromArray($data);
    }

    /**
     * Creates the object from an array of data
     *
     * @param array $data
     * @return \Strata\Data\Metadata\Metadata
     * @throws \Strata\Data\Exception\InvalidMetadataId
     */
    protected function createObjectFromArray(array $data)
    {
        $metaData = new Metadata();

        if (isset($data['id'])) {
            $metaData->setId($data['id']);
        }

        if (isset($data['createdAt'])) {
            if (!$data['createdAt'] instanceof \DateTime) {
                $data['createdAt'] = \DateTime::createFromFormat('Y-m-d H:i:s', $data['createdAt']);
            }
            $metaData->setCreatedAt($data['createdAt']);
        }

        if (isset($data['updatedAt'])) {
            if (!$data['updatedAt'] instanceof \DateTime) {
                $data['updatedAt'] = \DateTime::createFromFormat('Y-m-d H:i:s', $data['updatedAt']);
            }
            $metaData->setUpdatedAt($data['updatedAt']);
        }

        if (isset($data['url'])) {
            $metaData->setUrl($data['url']);
        }

        if (isset($data['contentHash'])) {
            $metaData->setContentHash($data['contentHash']);
        }

        if (isset($data['attributes'])) {
            if (!is_array($data['attributes'])) {
                $data['attributes'] = explode(',', $data['attributes']);
            }
            $metaData->setAttributes($data['attributes']);
        }

        return $metaData;
    }

    /**
     * Gets the script rquired for the table set up.
     * This method can be extended to support multiple storage types
     *
     * @param string $storageType
     * @return string
     */
    public function getTableSetupScript(string $storageType): string
    {
        switch ($storageType) {
            case 'sqlite':
                return $this->getSqliteTableCreationSql();
        }
    }

    /**
     * Get the Sqlite creation SQL required to provision the table
     *
     * @return string
     */
    protected function getSqliteTableCreationSql()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS ' . $this->identifier . ' (id TEXT PRIMARY KEY, createdAt TEXT, updatedAt TEXT, url TEXT, contentHash TEXT, attributes BLOB)';

        return $sql;
    }
}
