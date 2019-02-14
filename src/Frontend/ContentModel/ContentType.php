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
class ContentType extends \ArrayIterator
{
    protected $name;

    protected $apiEndpoint;

    public function __construct(string $name)
    {
        parent::__construct();

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
     * @param ContentModelFieldInterface $item
     * @return ContentType Fluent interface
     */
    public function addItem(ContentFieldInterface $item): ContentType
    {
        $this->offsetSet($item->getName(), $item);
        return $this;
    }

    /**
     * Return current item
     *
     * @return ContentFieldInterface
     */
    public function current(): ContentFieldInterface
    {
        return parent::current();
    }

    /**
     * Return item by key
     *
     * @param string $index
     * @return ContentFieldInterface
     */
    public function offsetGet($index): ContentFieldInterface
    {
        return parent::offsetGet($index);
    }

}
