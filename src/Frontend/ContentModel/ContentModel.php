<?php
declare(strict_types=1);

namespace Studio24\Frontend\ContentModel;

use Studio24\Exception\ConfigParsingException;
use Symfony\Component\Yaml\Yaml;

/**
 * Represents a content model definition
 *
 * This contains a collection of content types
 *
 * @package Studio24\Frontend\ContentType
 */
class ContentModel implements \ArrayAccess, \SeekableIterator, \Countable
{
    /**
     * Collection of content types
     * @var array
     */
    protected $contentTypes = [];

    /**
     * Content type collection key
     *
     * @var string
     */
    protected $key;

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
     * Add an item to the collection
     *
     * @param ContentType $item
     * @return ContentModel Fluent interface
     */
    public function addItem(ContentType $item) : ContentModel
    {
        $this->contentTypes[$item->getName()] = $item;
        return $this;
    }

    /**
     * @return ContentType
     */
    public function current() : ContentType
    {
        return $this->contentTypes[$this->key];
    }

    public function next()
    {
        $keys = $this->getKeys();
        foreach ($keys as $num => $key) {
            if ($this->key === $key) {
                $this->key = $keys[$num + 1];
            }
        }
    }

    public function key()
    {
        return $this->key;
    }

    public function valid()
    {
        return isset($this->array[$this->key]);
    }

    public function rewind()
    {
        $this->key = $this->getKeys()[0];
    }

    /**
     * Return current collection array keys
     *
     * @return array
     */
    public function getKeys() : array
    {
        return array_keys($this->contentTypes);
    }

    public function offsetExists($offset)
    {
        return isset($this->contentTypes[$offset]);
    }

    /**
     * @param mixed $offset
     * @return ContentType
     */
    public function offsetGet($offset) : ContentType
    {
        return isset($this->contentTypes[$offset]) ? $this->contentTypes[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->contentTypes[] = $value;
        } else {
            $this->contentTypes[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->contentTypes[$offset]);
    }

    public function count() : int
    {
        return count($this->contentTypes);
    }

    public function seek($position)
    {
        if (!isset($this->contentTypes[$position])) {
            throw new \OutOfBoundsException(sprintf('Invalid content type key: %s', $position));
        }
        $this->key = $position;
    }


}