<?php

declare(strict_types=1);

namespace Strata\Frontend\Repository\Resolver;

use Strata\Frontend\Content\Field\ArrayContent;
use Strata\Frontend\Content\Field\Boolean;
use Strata\Frontend\Content\Field\Component;
use Strata\Frontend\Content\Field\ContentFieldCollection;
use Strata\Frontend\Content\Field\ContentFieldInterface;
use Strata\Frontend\Content\Field\Date;
use Strata\Frontend\Content\Field\DateTime;
use Strata\Frontend\Content\Field\Decimal;
use Strata\Frontend\Content\Field\FlexibleContent;
use Strata\Frontend\Content\Field\Image;
use Strata\Frontend\Content\Field\Number;
use Strata\Frontend\Content\Field\PlainArray;
use Strata\Frontend\Content\Field\PlainText;
use Strata\Frontend\Content\Field\Relation;
use Strata\Frontend\Content\Field\RelationArray;
use Strata\Frontend\Content\Field\RichText;
use Strata\Frontend\Content\Field\ShortText;
use Strata\Frontend\Exception\ContentFieldTranslationNotFoundException;
use Strata\Frontend\Schema\ContentType;
use Strata\Frontend\Schema\Field\ArraySchemaField;
use Strata\Frontend\Schema\Field\FlexibleSchemaField;
use Strata\Frontend\Schema\Field\SchemaFieldInterface;

/**
 * Common functionality to resolve custom content fields to content objects
 *
 * Extend this class for specific APIs/CMSs to reflect different API data structures
 *
 * @package Strata\Frontend\Repository\Translation
 */
class ContentFieldResolver implements ResolverInterface
{
    /**
     * Fieldname to identify flexible component fields
     * @see resolveFlexibleField
     * @var string
     */
    protected string $flexibleComponentNameField = 'component';

    /**
     * Takes a content schema field object and returns a content field from source data value
     *
     * @param SchemaFieldInterface $contentField
     * @param $value
     * @return ContentFieldInterface Content field object, or null on failure to resolve content field
     * @throws ContentFieldTranslationNotFoundException
     */
    public function resolveContentField(SchemaFieldInterface $contentField, $value): ?ContentFieldInterface
    {
        $methodName = 'resolve' . ucfirst($contentField->getType()) . 'Field';

        if (!method_exists($this, $methodName)) {
            // @todo logger
            throw new ContentFieldTranslationNotFoundException(sprintf('Content field resolver for content type %s not found, ensure you have a resolver method called %s', $contentField->getType(), $methodName));
            return null;
        }

        return $this->$methodName($contentField, $value);
    }

    /**
     * Resolve a short text content field from source data value
     *
     * @param SchemaFieldInterface $contentField
     * @param $value
     * @return ShortText|null
     * @throws \Strata\Frontend\Exception\ContentFieldException
     */
    public function resolveTextField(SchemaFieldInterface $contentField, $value): ?ShortText
    {
        return new ShortText($contentField->getName(), (string) $value);
    }

    /**
     * Resolve a number content field from source data value
     *
     * @param SchemaFieldInterface $contentField
     * @param $value
     * @return Number|null
     * @throws \Strata\Frontend\Exception\ContentFieldException
     */
    public function resolveNumberField(SchemaFieldInterface $contentField, $value): ?Number
    {
        return new Number($contentField->getName(), $value);
    }

    /**
     * Resolve a plain text content field from source data value
     *
     * @param SchemaFieldInterface $contentField
     * @param $value
     * @return PlainText|null
     * @throws \Strata\Frontend\Exception\ContentFieldException
     */
    public function resolvePlaintextField(SchemaFieldInterface $contentField, $value): ?PlainText
    {
        return new PlainText($contentField->getName(), (string) $value);
    }

    /**
     * Resolve a rich text content field from source data value
     *
     * @param SchemaFieldInterface $contentField
     * @param $value
     * @return RichText|null
     * @throws \Strata\Frontend\Exception\ContentFieldException
     */
    public function resolveRichtextField(SchemaFieldInterface $contentField, $value): ?RichText
    {
        return new RichText($contentField->getName(), (string) $value);
    }

    /**
     * Resolve a date content field from source data value
     *
     * @param SchemaFieldInterface $contentField
     * @param $value
     * @return Date|null
     * @throws \Strata\Frontend\Exception\ContentFieldException
     */
    public function resolveDateField(SchemaFieldInterface $contentField, $value): ?Date
    {
        return new Date($contentField->getName(), $value);
    }

    /**
     * Resolve a datetime content field from source data value
     *
     * @param SchemaFieldInterface $contentField
     * @param $value
     * @return DateTime|null
     * @throws \Strata\Frontend\Exception\ContentFieldException
     */
    public function resolveDatetimeField(SchemaFieldInterface $contentField, $value): ?DateTime
    {
        return new DateTime($contentField->getName(), $value);
    }

    /**
     * Resolve a boolean content field from source data value
     *
     * @param SchemaFieldInterface $contentField
     * @param $value
     * @return bool|null
     * @throws \Strata\Frontend\Exception\ContentFieldException
     */
    public function resolveBooleanField(SchemaFieldInterface $contentField, $value): ?Boolean
    {
        return new Boolean($contentField->getName(), $value);
    }

    /**
     * Resolve an array content field from source data value
     *
     * This represents an array of repeated content fields
     *
     * @param ArraySchemaField $contentField
     * @param $value
     * @return ArrayContent|null
     * @throws ContentFieldTranslationNotFoundException
     * @throws \Strata\Frontend\Exception\ContentFieldException
     */
    public function resolveArrayField(ArraySchemaField $contentField, $value): ?ArrayContent
    {
        $array = new ArrayContent($contentField->getName());

        if (!is_array($value) || empty($value)) {
            return null;
        }

        foreach ($value as $row) {
            // For each row add a set of content fields
            $item = new ContentFieldCollection();

            foreach ($contentField as $childField) {
                if (!isset($row[$childField->getName()])) {
                    continue;
                }

                $childValue = $row[$childField->getName()];
                $contentField = $this->resolveContentField($childField, $childValue);

                if ($contentField !== null) {
                    $item->addItem($this->resolveContentField($childField, $childValue));
                }
            }
            $array->addItem($item);
        }
        return $array;
    }

    /**
     * Resolve a plain array content field from source data value
     *
     * This represents a simple array of multiple values
     *
     * @param SchemaFieldInterface $contentField
     * @param $value
     * @return PlainArray|null
     * @throws \Strata\Frontend\Exception\ContentFieldException
     */
    public function resolvePlainArrayField(SchemaFieldInterface $contentField, $value): ?PlainArray
    {
        if (!is_array($value)) {
            return null;
        }
        return new PlainArray($contentField->getName(), $value);
    }

    /**
     * Resolve a decimal content field from source data value
     *
     * @param SchemaFieldInterface $contentField
     * @param $value
     * @return Decimal|null
     * @throws \Strata\Frontend\Exception\ContentFieldException
     */
    public function resolveDecimalField(SchemaFieldInterface $contentField, $value): ?Decimal
    {
        $precision = $contentField->getOption('precision');
        $round = $contentField->getOption('round');
        return new Decimal($contentField->getName(), $value, $precision, $round);
    }

    public function resolveImageField(SchemaFieldInterface $contentField, $value): ?Image
    {
        // TODO: Implement resolveImageField() method.
        return null;
    }

    public function resolveRelationField(SchemaFieldInterface $contentField, $value): ?Relation
    {
        // TODO: Implement resolveRelationField() method.
        return null;
    }

    public function resolveRelationArrayField(SchemaFieldInterface $contentField, $value): ?RelationArray
    {
        // TODO: Implement resolveRelationArrayField() method.
        return null;
    }

    /**
     * Resolve a flexible content field from source data value
     * @param SchemaFieldInterface $contentField
     * @param $value
     * @return FlexibleContent|null
     * @throws ContentFieldTranslationNotFoundException
     * @throws \Strata\Frontend\Exception\ContentFieldException
     */
    public function resolveFlexibleField(FlexibleSchemaField $contentField, $value): ?FlexibleContent
    {
        $flexible = new FlexibleContent($contentField->getName());

        foreach ($value as $valueComponent) {
            // get component name
            if (!isset($valueComponent[$this->flexibleComponentNameField])) {
                continue;
            }
            $componentName = $valueComponent[$this->flexibleComponentNameField];

            // if component not in content schema, skip
            if (!$contentField->has($componentName)) {
                continue;
            }

            // build component content fields
            $component = new Component($componentName);
            foreach ($contentField->offsetGet($componentName) as $componentField) {
                if (!isset($valueComponent[$componentField->getName()])) {
                    continue;
                }
                $componentFieldValue = $valueComponent[$componentField->getName()];
                $componentFieldObject = $this->resolveContentField($componentField, $componentFieldValue);
                if ($componentFieldObject === null) {
                    continue;
                }
                $component->addContent($componentFieldObject);
            }

            $flexible->addComponent($component);
        }

        if (empty($flexible)) {
            return null;
        }
        return $flexible;
    }
}
