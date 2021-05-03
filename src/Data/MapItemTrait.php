<?php

declare(strict_types=1);

namespace Strata\Frontend\Data;

use Strata\Data\Exception\MapperException;
use Strata\Data\Mapper\MapperAbstract;
use Strata\Data\Mapper\MapperInterface;
use Strata\Frontend\Content\BaseContent;
use Strata\Frontend\Content\ContentInterface;
use Strata\Frontend\Content\Field\ContentFieldInterface;
use Strata\Frontend\Content\Page;
use Strata\Frontend\Data\Resolver\ResolverInterface;
use Strata\Frontend\Exception\ContentFieldException;
use Strata\Frontend\Exception\ContentFieldNotSetException;
use Strata\Frontend\Exception\ContentFieldTranslationNotFoundException;
use Strata\Frontend\Exception\ContentTypeNotSetException;
use Strata\Frontend\Schema\ContentType;
use Strata\Frontend\Schema\Field\SchemaFieldInterface;
use Strata\Frontend\Schema\Schema;

/**
 * Common functionality for mappers
 */
trait MapItemTrait
{
    private MapperInterface $mapper;
    private ContentType $contentType;
    private ResolverInterface $contentFieldResolver;

    public function __construct()
    {
        $this->mapper = new MapItem($this->getDefaultMapping());
        $this->mapper->toObject(Page::class);
    }

    public function setContentType(ContentType $schema)
    {
        $this->contentType = $schema;
    }

    public function getContentType(): ContentType
    {
        return $this->contentType;
    }

    public function setContentFieldResolver(ResolverInterface $contentFieldResolver)
    {
        $this->contentFieldResolver = $contentFieldResolver;
    }

    public function getContentFieldResolver(): ResolverInterface
    {
        return $this->contentFieldResolver;
    }

    public function setMapper(MapperInterface $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * Return data mapper to use with map method
     *
     * @return MapperInterface
     */
    public function getMapper(): MapperInterface
    {
        return $this->mapper;
    }

    /**
     * Return array of custom content field objects from source data
     *
     * @param array $data Source data
     * @return ContentFieldInterface[] Array of ContentFieldInterface objects
     * @throws ContentFieldNotSetException
     * @throws ContentTypeNotSetException
     * @throws \Strata\Frontend\Exception\ContentFieldException
     */
    public function mapCustomContentFields(array $data): array
    {
        $contentFields = [];
        $contentType = $this->getContentType();
        foreach ($data as $name => $value) {
            if (!$contentType->offsetExists($name)) {
                /* @todo logger
                if ($this->hasLogger()) {
                $this->getLogger()->info(sprintf("Content field definition not found for field '%s' in content type '%s'", $name, $contentType->getName()));
                } */
                continue;
            }

            $fieldModel     = $contentType->offsetGet($name);
            $fieldContent   = $this->getContentField($fieldModel, $value);
            if ($fieldContent !== null) {
                $contentFields[] = $fieldContent;
            }
        }

        return $contentFields;
    }

    /**
     * Return a content field populated with passed data
     *
     * @param SchemaFieldInterface $field Content field definition
     * @param mixed $value Content field value
     * @return ContentFieldInterface Populated content field object, or null on failure
     * @throws ContentFieldException
     */
    protected function getContentField(SchemaFieldInterface $field, $value): ?ContentFieldInterface
    {
        try {
            return $this->getContentFieldResolver()->resolveContentField($field, $value);
        } catch (\Error $e) {
            $message = sprintf("Fatal error when creating content field '%s' (type: %s) for value: %s", $field->getName(), $field->getType(), print_r($value, true));
            throw new ContentFieldException($message, 0, $e);
        } catch (\Exception $e) {
            $message = sprintf("Exception thrown when creating content field '%s' (type: %s) for value: %s", $field->getName(), $field->getType(), print_r($value, true));
            throw new ContentFieldException($message, 0, $e);
        }
        return null;
    }

}