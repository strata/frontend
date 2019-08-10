<?php
declare(strict_types=1);

namespace Studio24\Frontend\ContentModel;

use Studio24\Frontend\Content\Field\ArrayContent;
use Studio24\Frontend\Collection\ArrayAccessTrait;

/**
 * Represents a content field definition (e.g. title field)
 *
 * This contains a collection of content fields
 *
 * @package Studio24\Frontend\ContentModel
 */
class ArrayField extends ContentFieldCollection implements FieldInterface, ContentFieldCollectionInterface
{

    /**
     * Content field type
     *
     * @var string
     */
    protected $type = ArrayContent::TYPE;

    /**
     * Constructor
     *
     * @param string $name
     * @param array $components
     * @throws \Studio24\Frontend\Exception\ConfigParsingException
     */
    public function __construct(string $name, array $components = [])
    {
        $this->setName($name);

        if (!empty($components)) {
            $this->addContentFields($components);
        }
    }

    /**
     * @param string $name
     * @return |null
     */
    public function getOption(string $name)
    {
        return null;
    }

    /**
     * @param string $name
     * @return |null
     */
    public function hasOption(string $name): bool
    {
        return false;
    }

    /**
     * Is the passed content field name a valid content field type?
     *
     * @param string $field Content field type
     * @return bool
     */
    public function validContentFields(string $field)
    {
        return parent::validContentFields($field);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Add content fields from an array of data (normally loaded from config file)
     *
     * @param array $contentFields
     * @return ArrayField
     * @throws \Studio24\Frontend\Exception\ConfigParsingException
     */
    public function addContentFields(array $contentFields): ArrayField
    {
        foreach ($contentFields as $name => $values) {
            $this->addItem($this->parseContentFieldArray($name, $values));
        }

        return $this;
    }

    public function getApiEndpoint(): string
    {
        throw new UnimplementedException(sprintf('% is not implemented in %s\%s', __METHOD__, __NAMESPACE__, __CLASS__));
    }

    public function setApiEndpoint(string $apiEndpoint): ContentType
    {
        throw new UnimplementedException(sprintf('% is not implemented in %s\%s', __METHOD__, __NAMESPACE__, __CLASS__));
    }
}
