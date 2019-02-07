<?php
declare(strict_types=1);

namespace Studio24\Frontend\ContentModel;

use Studio24\Frontend\Content\Field\FlexibleContent;
use Symfony\Component\Yaml\Yaml;

/**
 * Represents a content type definition (e.g. News post)
 *
 * This contains a collection of content fields
 *
 * @package Studio24\Frontend\Content
 */
class ContentType implements \ArrayAccess, \SeekableIterator, \Countable
{

    protected $name;

    protected $apiEndpoint;

    /**
     * Collection of content fields
     * @var array
     */
    protected $contentFields = [];

    /**
     * Content type collection key
     *
     * @var string
     */
    protected $key;

    public function __construct(string $name)
    {
        $this->setName($name);
    }

    /**
     * Parse the content fields YAML config file for this content type
     *
     * @param string $file
     * @return ContentType
     * @throws ConfigParsingException
     */
    public function parseConfig(string $file): ContentType
    {
        $data = Yaml::parseFile($file);

        if (!is_array($data)) {
            throw new ConfigParsingException("Content types YAML config file must contain an array of content fields");
        }

        foreach ($data as $name => $values) {
            $this->addItem($this->parseContentFieldArray($name, $values));
        }

        return $this;
    }

    /**
     * Parse an array into a Content Field object
     *
     * @param string $name
     * @param array $data
     * @return ContentFieldInterface
     */
    public function parseContentFieldArray(string $name, array $data): ContentFieldInterface
    {
        if (!isset($data['type'])) {
            throw new ConfigParsingException("You must set a 'type' for a content type, e.g. type: plaintext");
        }
        if ($data['type'] === FlexibleContent::TYPE) {
            if (!isset($data['components'])) {
                throw new ConfigParsingException("You must set a 'components' array for a flexible content field");
            }
            $contentField = new FlexibleContentField($name, $data['components']);
        } else {
            $contentField = new ContentField($name, $data['type']);

            unset($data['type']);
            if (is_array($data)) {
                foreach ($data as $name => $value) {
                    $contentField->addOption($name, $value);
                }
            }
        }

        return $contentField;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ContentModel Fluent interface
     */
    public function setName(string $name): ContentType
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getApiEndpoint(): string
    {
        return $this->apiEndpoint;
    }

    /**
     * @param string $apiEndpoint
     * @return ContentModel Fluent interface
     */
    public function setApiEndpoint(string $apiEndpoint): ContentType
    {
        $this->apiEndpoint = $apiEndpoint;
        return $this;
    }

    /**
     * Add an item to the collection
     *
     * @param ContentFieldInterface $item
     * @return ContentModel Fluent interface
     */
    public function addItem(ContentFieldInterface $item): ContentType
    {
        $this->contentFields[$item->getName()] = $item;
        return $this;
    }

    /**
     * @return ContentFieldInterface
     */
    public function current() : ContentFieldInterface
    {
        return $this->contentFields[$this->key];
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
        return array_keys($this->contentFields);
    }

    public function offsetExists($offset)
    {
        return isset($this->contentFields[$offset]);
    }

    /**
     * @param mixed $offset
     * @return ContentFieldInterface
     */
    public function offsetGet($offset) : ContentFieldInterface
    {
        return isset($this->contentFields[$offset]) ? $this->contentFields[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->contentFields[] = $value;
        } else {
            $this->contentFields[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->contentFields[$offset]);
    }

    public function count() : int
    {
        return count($this->contentFields);
    }

    public function seek($position)
    {
        if (!isset($this->contentFields[$position])) {
            throw new \OutOfBoundsException(sprintf('Invalid content field key: %s', $position));
        }
        $this->key = $position;
    }
}
