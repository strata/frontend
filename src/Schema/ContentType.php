<?php

declare(strict_types=1);

namespace Strata\Frontend\Schema;

use Strata\Frontend\Content\Field\ArrayContent;
use Strata\Frontend\Content\Field\Audio;
use Strata\Frontend\Content\Field\Boolean;
use Strata\Frontend\Content\Field\Component;
use Strata\Frontend\Content\Field\ContentFieldCollection;
use Strata\Frontend\Content\Field\ContentFieldInterface;
use Strata\Frontend\Content\Field\Date;
use Strata\Frontend\Content\Field\DateTime;
use Strata\Frontend\Content\Field\Decimal;
use Strata\Frontend\Content\Field\Document;
use Strata\Frontend\Content\Field\FieldType;
use Strata\Frontend\Content\Field\FlexibleContent;
use Strata\Frontend\Content\Field\Image;
use Strata\Frontend\Content\Field\Number;
use Strata\Frontend\Content\Field\PlainText;
use Strata\Frontend\Content\Field\PlainArray;
use Strata\Frontend\Content\Field\Relation;
use Strata\Frontend\Content\Field\RelationArray;
use Strata\Frontend\Content\Field\RichText;
use Strata\Frontend\Content\Field\ShortText;
use Strata\Frontend\Content\Field\TaxonomyTerms;
use Strata\Frontend\Content\Field\Video;
use Strata\Frontend\Exception\ConfigParsingException;
use Strata\Frontend\Schema\Api\Field;
use Strata\Frontend\Schema\Field\ArraySchemaField;
use Strata\Frontend\Schema\Field\FlexibleSchemaField;
use Strata\Frontend\Schema\Field\SchemaField;
use Strata\Frontend\Schema\Field\SchemaFieldInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * Represents a content type definition (e.g. News post)
 *
 * This contains a collection of content fields
 *
 * @package Strata\Frontend\Content
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
    protected static $validContentFields = [];

    protected $name;

    protected $apiEndpoint;

    protected $taxonomies;

    /**
     * Content type name from source CMS
     *
     * @var string
     */
    protected $sourceContentType;

    public function __construct(string $name)
    {
        parent::__construct();

        $this->setName($name);
        $this->taxonomies = array();

        // @todo do we need to populate valid content field types?
        self::$validContentFields = FieldType::getFieldTypes();
    }

    /**
     * Register a content type name
     *
     * If you create a new content type, make sure you register it to ensure the Content Model system recognises it
     *
     * @todo may need replacing, see FieldTypes::class
     * @param string $name
     */
    public static function registerContentType(string $name)
    {
        self::$validContentFields[] = $name;
    }

    /**
     * @return string
     */
    public function getSourceContentType(): ?string
    {
        return $this->sourceContentType;
    }

    /**
     * @param string $sourceContentType
     * @return ContentType Fluent interface
     */
    public function setSourceContentType(string $sourceContentType): ContentType
    {
        $this->sourceContentType = $sourceContentType;
        return $this;
    }

    /**
     * Is the passed content field name a valid content field type?
     *
     * @param string $field Content field type
     * @return bool
     */
    public function validContentFields(string $field): bool
    {
        return FieldType::exists($field);
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
    public function addItem(SchemaFieldInterface $item): ContentType
    {
        $this->offsetSet($item->getName(), $item);
        return $this;
    }

    /**
     * Return current item
     *
     * @return SchemaFieldInterface
     */
    public function current(): SchemaFieldInterface
    {
        return parent::current();
    }

    /**
     * Return item by key
     *
     * @param string $key
     * @return SchemaFieldInterface
     */
    public function offsetGet($key): SchemaFieldInterface
    {
        return parent::offsetGet($key);
    }

    /**
     * Parse an array into a Content SchemaField object
     *
     * @param string $name Content type name
     * @param array $data Content type field data
     * @param string $configDir
     * @return SchemaFieldInterface
     * @throws ConfigParsingException
     */
    public function parseContentFieldArray(string $name, array $data, string $configDir = ''): SchemaFieldInterface
    {
        if (isset($data['config'])) {
            $data = YAML::parseFile($configDir . '/' . $data['config']);
        }
        if (!isset($data['type'])) {
            throw new ConfigParsingException("You must set a 'type' for a content type, e.g. type: plaintext");
        }
        if (!$this->validContentFields($data['type'])) {
            throw new ConfigParsingException(sprintf("Invalid content field type '%s'", $data['type']));
        }

        switch ($data['type']) {
            case FieldType::FLEXIBLE_CONTENT:
                if (!isset($data['components'])) {
                    throw new ConfigParsingException("You must set a 'components' array for a flexible content field");
                }
                $contentField = new FlexibleSchemaField($name, $data['components']);
                break;

            case FieldType::ARRAY:
                if (!isset($data['content_fields'])) {
                    throw new ConfigParsingException("You must set a 'content_fields' array for an array content field");
                }
                $contentField = new ArraySchemaField($name, $data['content_fields']);
                break;

            default:
                // Validation
                if ($data['type'] === RelationArray::TYPE && !isset($data['content_type'])) {
                    throw new ConfigParsingException("You must set a 'content_type' array for a relation array content field");
                }

                $contentField = new SchemaField($name, $data['type']);

                unset($data['type']);
                if (is_array($data)) {
                    foreach ($data as $name => $value) {
                        $contentField->addOption($name, $value);
                    }
                }
        }

        return $contentField;
    }
}
