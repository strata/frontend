<?php

declare(strict_types=1);

namespace Strata\Frontend\Schema\Api;

/**
 * Class to represent a single API method, whether it returns an item or list of items, and fields returned for each item
 */
class ApiMethod
{
    /**
     * API response represents a single item
     */
    const TYPE_ITEM = 'item';

    /**
     * API response represents a list of items
     */
    const TYPE_LIST = 'list';

    private string $name;
    private string $description;

    /**
     * Type, item or list
     * @var string
     */
    private string $type;

    /**
     * Name of item type returned by list
     * @var string
     */
    private string $listType;

    /**
     * Array of available fields
     * @var Field[]
     */
    private array $fields;

    public function __construct(string $name, string $type, ?string $description = null)
    {
        $this->name = $name;
        $this->type = $type;
        if (null !== $description) {
            $this->description = $description;
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getListType(): string
    {
        return $this->listType;
    }

    /**
     * @param string $listType
     */
    public function setListType(string $listType): void
    {
        $this->listType = $listType;
    }

    /**
     * Add a field returned in API method response
     * @param string $name
     * @param Field $field
     */
    public function addField(string $name, Field $field)
    {
        $this->fields[$name] = $field;
    }

    /**
     * Remove a field returned in API method response
     * @param string $name
     */
    public function removeField(string $name)
    {
        if (isset($this->fields[$name])) {
            unset($this->fields[$name]);
        }
    }

    /**
     * Return all fields returned in API method response
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

}