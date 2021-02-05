<?php
declare(strict_types=1);

namespace Strata\Data\Metadata;

use \DateTime;
use Strata\Data\Exception\InvalidMetadataId;

/**
 * Class to store metadata about an item of data
 *
 * @package Strata\Data\Metadata
 */
class Metadata
{
    /** @var int|string */
    protected $id;

    /** @var DateTime */
    protected $createdAt;

    /** @var DateTime */
    protected $updatedAt;

    /** @var string */
    protected $url;

    /** @var string */
    protected $contentHash;

    /** @var array */
    protected $attributes;

    /**
     * Return ID
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set ID
     *
     * @param int|string $id
     * @return Metadata Fluent interface
     * @throws InvalidMetadataId
     */
    public function setId($id): Metadata
    {
        $this->validateIdentifier($id);
        $this->id = $id;
        $this->updated();

        return $this;
    }

    /**
     * Validate whether the identifier argument is OK
     *
     * @param $id
     * @throws InvalidMetadataId
     */
    protected function validateIdentifier($id)
    {
        if (!is_string($id) && !is_int($id)) {
            throw new InvalidMetadataId(sprintf('$id argument must be string or integer, %s passed', gettype($id)));
        }
    }

    /**
     * Return createdAt datetime
     *
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * Set the updatedAt datetime
     *
     * @param \DateTime $createdAt
     * @return Metadata
     */
    public function setCreatedAt(\DateTime $createdAt): Metadata
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Return last updated datetime
     *
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * Set the updated datetime
     *
     * @param \DateTime $updatedAt
     * @return Metadata
     */
    public function setUpdatedAt(\DateTime $updatedAt): Metadata
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }


    /**
     * Update last updated with current DateTime
     */
    protected function updated(): void
    {
        $this->setUpdatedAt(new DateTime());
    }

    /**
     * Return URL of data source
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Set URL of data source
     *
     * @param string $url
     * @return Metadata Fluent interface
     */
    public function setUrl(string $url): Metadata
    {
        $this->url = $url;
        $this->updated();

        return $this;
    }

    /**
     * @return ContentHasher
     */
    public function getContentHash(): ?string
    {
        return $this->contentHash;
    }

    /**
     * Set content hash for this item, which helps us work out whether data has been updated
     *
     * @param string $contentHash
     * @return Metadata Fluent interface
     */
    public function setContentHash(string $contentHash): Metadata
    {
        $this->contentHash = $contentHash;
        $this->updated();

        return $this;
    }

    /**
     * Return all attributes
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Sets all attributes
     *
     * @param array $attributes
     * @return \Strata\Data\Metadata\Metadata
     */
    public function setAttributes(array $attributes): Metadata
    {
        $this->attributes = $attributes;
        $this->updated();

        return $this;
    }

    /**
     * Set one attribute
     *
     * @param $name
     * @param $value
     * @return Metadata Fluent interface
     */
    public function setAttribute($name, $value): Metadata
    {
        $this->attributes[$name] = $value;
        $this->updated();

        return $this;
    }

    /**
     * Does an attribute exist?
     *
     * @param string $name
     * @return bool
     */
    public function hasAttribute(string $name)
    {
        return (isset($this->attributes[$name]));
    }

    /**
     * Return attribute, or null if not set
     *
     * @param string $name
     * @return mixed|null
     */
    public function getAttribute(string $name)
    {
        if ($this->hasAttribute($name)) {
            return $this->attributes[$name];
        }
        return null;
    }

    /**
     *
     *
     * @return array
     */
    public function toArray()
    {
        return [
          'id'          => $this->getId(),
          'contentHash' => $this->getContentHash(),
          'url'         => $this->getUrl(),
          'attributes'  => implode($this->getAttributes(), ", "),
          'createdAt'   => $this->getCreatedAt()->format("Y-m-d H:i:s"),
          'updatedAt'   => $this->getUpdatedAt()->format("Y-m-d H:i:s"),
        ];
    }
}
