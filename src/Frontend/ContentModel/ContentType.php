<?php
declare(strict_types=1);

namespace Studio24\Frontend\ContentModel;

use Studio24\Frontend\Content\Field\ArrayContent;
use Studio24\Frontend\Content\Field\Audio;
use Studio24\Frontend\Content\Field\Boolean;
use Studio24\Frontend\Content\Field\Date;
use Studio24\Frontend\Content\Field\DateTime;
use Studio24\Frontend\Content\Field\Document;
use Studio24\Frontend\Content\Field\FlexibleContent;
use Studio24\Frontend\Content\Field\Image;
use Studio24\Frontend\Content\Field\Number;
use Studio24\Frontend\Content\Field\PlainText;
use Studio24\Frontend\Content\Field\Relation;
use Studio24\Frontend\Content\Field\RichText;
use Studio24\Frontend\Content\Field\ShortText;
use Studio24\Frontend\Content\Field\Video;
use Studio24\Frontend\Exception\ConfigParsingException;
use Symfony\Component\Yaml\Yaml;

/**
 * Represents a content type definition (e.g. News post)
 *
 * This contains a collection of content fields
 *
 * @package Studio24\Frontend\Content
 */
class ContentType extends \ArrayIterator implements ContentFieldCollectionInterface
{

    /**
     * Array of valid content types
     *
     * Add any new content types here
     *
     * @var array
     */
    protected static $validContentFields = [
        ArrayContent::TYPE,
        Audio::TYPE,
        Boolean::TYPE,
        Date::TYPE,
        DateTime::TYPE,
        Document::TYPE,
        FlexibleContent::TYPE,
        Image::TYPE,
        Number::TYPE,
        PlainText::TYPE,
        Relation::TYPE,
        RichText::TYPE,
        ShortText::TYPE,
        Video::TYPE
    ];

    protected $name;

    protected $apiEndpoint;

    protected $taxonomies;

    public function __construct(string $name)
    {
        parent::__construct();

        $this->setName($name);
        $this->taxonomies = array();
    }

    /**
     * Register a content type name
     *
     * If you create a new content type, make sure you register it to ensure the Content Model system recognises it
     *
     * @param string $name
     */
    public static function registerContentType(string $name)
    {
        self::$validContentFields[] = $name;
    }

    /**
     * Parse the content fields YAML config file for this content type
     *
     * @todo Add more info on where the error is in the YAML file
     *
     * @param string $file
     * @return ContentType
     * @throws ConfigParsingException
     */
    public function parseConfig(string $file): ContentType
    {
        $configDir = dirname($file);
        $data = Yaml::parseFile($file);

        if (!is_array($data)) {
            throw new ConfigParsingException("Content types YAML config file must contain an array of content fields");
        }

        foreach ($data as $name => $values) {
            if ($name = 'config') {
                $values = Yaml::parseFile($configDir.'/'.$values);
            }
            if (!is_array($values)) {
                throw new ConfigParsingException(sprintf("Content field definition must contain an array of values, including the 'type' property, %s found", gettype($values)));
            }
            $this->addItem($this->parseContentFieldArray($name, $values));
        }

        return $this;
    }

    /**
     * Parse an array into a Content Field object
     *
     * @param string $name
     * @param array $data
     * @return FieldInterface
     * @throws ConfigParsingException
     */
    public function parseContentFieldArray(string $name, array $data): FieldInterface
    {
        if (!isset($data['type'])) {
            throw new ConfigParsingException("You must set a 'type' for a content type, e.g. type: plaintext");
        }
        if (!$this->validContentFields($data['type'])) {
            throw new ConfigParsingException(sprintf("Invalid content field type '%s'", $data['type']));
        }

        switch ($data['type']) {
            case FlexibleContent::TYPE:
                if (!isset($data['components'])) {
                    throw new ConfigParsingException("You must set a 'components' array for a flexible content field");
                }
                $contentField = new FlexibleField($name, $data['components']);
                break;

            case ArrayContent::TYPE:
                if (!isset($data['content_fields'])) {
                    throw new ConfigParsingException("You must set a 'content_fields' array for an array content field");
                }
                $contentField = new ArrayField($name, $data['content_fields']);
                break;

            default:
                $contentField = new Field($name, $data['type']);

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
     * Is the passed content field name a valid content field type?
     *
     * @param string $field Content field type
     * @return bool
     */
    public function validContentFields(string $field)
    {
        return in_array($field, self::$validContentFields);
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
     * @return ContentType Fluent interface
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
     * @return ContentType Fluent interface
     */
    public function setApiEndpoint(string $apiEndpoint): ContentType
    {
        $this->apiEndpoint = $apiEndpoint;
        return $this;
    }

    /**
     * @return array
     */
    public function getTaxonomies(): array
    {
        return $this->taxonomies;
    }

    /**
     * @param array $taxonomies
     * @return ContentType Fluent interface
     */
    public function setTaxonomies(array $taxonomies): ContentType
    {
        $this->taxonomies = $taxonomies;
        return $this;
    }

    /**
     * Add an item to the collection
     *
     * @param ContentModelFieldInterface $item
     * @return ContentType Fluent interface
     */
    public function addItem(FieldInterface $item): ContentType
    {
        $this->offsetSet($item->getName(), $item);
        return $this;
    }

    /**
     * Return current item
     *
     * @return FieldInterface
     */
    public function current(): FieldInterface
    {
        return parent::current();
    }

    /**
     * Return item by key
     *
     * @param string $index
     * @return FieldInterface
     */
    public function offsetGet($index): FieldInterface
    {
        return parent::offsetGet($index);
    }
}
