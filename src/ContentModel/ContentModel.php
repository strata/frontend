<?php

declare(strict_types=1);

namespace Strata\Frontend\ContentModel;

use Strata\Frontend\Exception\ConfigParsingException;
use Strata\Frontend\Collection\ArrayAccessTrait;
use Symfony\Component\Yaml\Yaml;

/**
 * Represents a content model definition
 *
 * This contains a collection of content types
 *
 * @package Strata\Frontend\ContentType
 */
class ContentModel extends \ArrayIterator
{
    /**
     * Array of global content type variables
     *
     * @var array
     */
    protected $global = [];

    /**
     * Constructor
     *
     * @param string|null $configFile
     * @throws ConfigParsingException
     */
    public function __construct(string $configFile = null)
    {
        if ($configFile !== null) {
            $this->parseConfig($configFile);
        }
    }

    /**
     * Parse the content model YAML config file
     *
     * You can populate:
     * - $this->contentTypes
     * - $this->global
     *
     * @param string $file
     * @return ContentModel
     * @throws ConfigParsingException
     */
    public function parseConfig(string $file): ContentModel
    {
        $configDir = dirname($file);
        $data = Yaml::parseFile($file);

        if (!isset($data['content_types'])) {
            throw new ConfigParsingException("Content model YAML config file must contain a root 'content_types' element");
        }

        foreach ($data['content_types'] as $name => $values) {
            $contentType = new ContentType($name);
            if (isset($values['api_endpoint'])) {
                $contentType->setApiEndpoint($values['api_endpoint']);
            }
            if (isset($values['taxonomies'])) {
                $contentType->setTaxonomies($values['taxonomies']);
            }
            if (isset($values['source_content_type'])) {
                $contentType->setSourceContentType($values['source_content_type']);
            }
            if (isset($values['content_fields'])) {
                $contentType->parseConfig($configDir . '/' . $values['content_fields']);
            }
            $this->addItem($contentType);
        }

        if (isset($data['global']) && is_iterable($data['global'])) {
            foreach ($data['global'] as $name => $value) {
                $this->setGlobal($name, $value);
            }
        }

        return $this;
    }

    public function getGlobal($name)
    {
        if (isset($this->global[$name])) {
            return $this->global[$name];
        }
        return null;
    }

    public function setGlobal($name, $value): ContentModel
    {
        $this->global[$name] = $value;
        return $this;
    }

    /**
     * Whether the content type exists in the content model
     *
     * @param string $contentType
     * @return bool
     */
    public function hasContentType(string $contentType): bool
    {
        return $this->offsetExists($contentType);
    }

    /**
     * Return content type, if it exists
     *
     * @param string $contentType
     * @return ContentType|null
     */
    public function getContentType(string $contentType): ?ContentType
    {
        if ($this->hasContentType($contentType)) {
            return $this->offsetGet($contentType);
        }
        return null;
    }

    /**
     * Return a content type matched by the source content type
     *
     * E.g. For a content model that has News articles which are stored in WordPress as "posts",
     * ContentType::getBySourceContentType('posts') will return the News content type
     *
     * @param string $sourceContentType
     * @return ContentType|null
     */
    public function getBySourceContentType(string $sourceContentType): ?ContentType
    {
        foreach ($this as $contentType) {
            if ($contentType->getSourceContentType() === $sourceContentType) {
                return $contentType;
            }
        }
        return null;
    }

    /**
     * Add an item to the collection
     *
     * @param ContentType $item
     * @return ContentModel Fluent interface
     */
    public function addItem(ContentType $item): ContentModel
    {
        $this->offsetSet($item->getName(), $item);
        return $this;
    }

    /**
     * Return current item
     *
     * @return ContentType
     */
    public function current(): ContentType
    {
        return parent::current();
    }

    /**
     * Return item by key
     *
     * @param string $index
     * @return ContentType
     */
    public function offsetGet($index): ContentType
    {
        return parent::offsetGet($index);
    }
}
