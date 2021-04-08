<?php

declare(strict_types=1);

namespace Strata\Frontend\Storage;

use Strata\Frontend\Exception\MissingOptionException;
use Strata\Frontend\Exception\StorageException;
use Strata\Frontend\Metadata\Metadata;
use SQLite3;
use Strata\Frontend\Metadata\RepositoryInterface;

class SQLiteStorage implements StorageInterface
{
    protected $key = '';

    /**
     * @var SQLite3
     */
    protected $db;

    /**
     * Initialise storage mechanism
     * @param array $options
     * @return mixed
     */
    public function init(array $options = [])
    {
        if (!isset($options['filename'])) {
            throw new MissingOptionException('You must set the "filename" option, for the path to the SQLite3 database');
        }
        if (!is_writable($options['filename'])) {
            throw new StorageException(sprintf('Cannot write to filename path at %s', $options['filename']));
        }

        $this->db = new SQLite3($options['filename']);
    }

    /**
     * Set the key used to differentiate the current entity type in the storage
     *
     * @param string $key
     * @return mixed
     */
    public function setKey(string $key)
    {
        $this->key = $key;
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
     * Gets all items
     *
     * @return array
     */
    public function all(): array
    {
        $statement = $this->db->prepare('SELECT * FROM ' . $this->key);
        $results = $statement->execute();

        if (!$results) {
            return [];
        }

        $response = [];
        $index = 0;

        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $response[$index] = $row;
            $index++;
        }

        return $response;
    }

    /**
     * Get a metadata item via ID
     *
     * @param $id
     * @return bool
     */
    public function get($id)
    {
        $sql = "SELECT * FROM " . $this->key . " WHERE id='" . $id . "'";
        $result = $this->db->querySingle($sql, true);
        return $result;
    }

    /**
     * Does a metadata item exist for ID?
     *
     * @param $id
     * @return bool
     */
    public function has($id): bool
    {
        $sql = "SELECT EXISTS(SELECT 1 FROM " . $this->key . " WHERE id='" . $id . "' LIMIT 1);";

        $statement = $this->db->prepare($sql);
        $result = $statement->execute();

        if (!empty($row = $result->fetchArray())) {
            if ($row[0] === 1) {
                return true;
            }
        }

        return false;
    }



    /**
     * Delete one metadata item based on ID
     *
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $sql = "DELETE FROM " . $this->key . " WHERE id='" . $id . "'";

        $statement = $this->db->prepare($sql);
        $statement->execute();
    }

    /**
     * Delete all metadata items
     *
     * @return mixed
     */
    public function deleteAll()
    {
        $sql = "DELETE FROM " . $this->key;

        $statement = $this->db->prepare($sql);
        $statement->execute();
    }


    /**
     * Write one metadata item to storage
     *
     * @param array $data
     * @return mixed
     */
    public function save(array $data)
    {
        if ($this->has($data['id'])) {
            return $this->update($data);
        }

        if (empty($data['id'])) {
            // Create a random ID if one is not provided
            $data['id'] = bin2hex(random_bytes(8));
        }

        $dataKeys = array_keys($data);
        $dataKeysSql = "";
        foreach ($dataKeys as $index => $dataKey) {
            $dataKeysSql .= $dataKey;
            if ($index + 1 !== count($dataKeys)) {
                $dataKeysSql .= ", ";
            }
        }

        $dataValues = array_values($data);
        $dataValuesSql = "";
        foreach ($dataValues as $index => $dataValue) {
            if (empty($dataValue)) {
                $dataValuesSql .= "NULL";
            } else {
                $dataValuesSql .= "'" . $dataValue . "'";
            }

            if ($index + 1 !== count($dataValues)) {
                $dataValuesSql .= ", ";
            }
        }


        $sql = "INSERT INTO " . $this->key . "(" . $dataKeysSql . ") VALUES(" . $dataValuesSql . ")";

        $statement = $this->db->prepare($sql);
        $statement->execute();

        return $data['id'];
    }

    public function update(array $data)
    {
        $id = $data['id'];
        unset($data['id']);
        $dataKeys = array_keys($data);
        $dataValues = array_values($data);

        $insertSql = "";
        foreach ($dataKeys as $index => $dataKey) {
            $insertSql .= $dataKeys[$index] . " = '" . $dataValues[$index] . "'";
            if ($index + 1 !== count($dataKeys)) {
                $insertSql .= ", ";
            }
        }

        $sql = "UPDATE " . $this->key . " SET " . $insertSql . "where id='" . $id . "'";
        $result = $this->db->exec($sql);

        return $result;
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

    public function createTableIfItDoesntExist(RepositoryInterface $repository): void
    {
        $statement = $this->db->prepare($repository->getTableSetupScript('sqlite'));
        $statement->execute();
    }
}
