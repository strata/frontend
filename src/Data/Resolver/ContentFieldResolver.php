<?php

declare(strict_types=1);

namespace Strata\Frontend\Data\Resolver;

use Strata\Frontend\Content\Field\ArrayContent;
use Strata\Frontend\Content\Field\Boolean;
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
use Strata\Frontend\Schema\Field\SchemaFieldInterface;

/**
 * Common functionality to resolve custom content fields to content objects
 *
 * Can be customised for different CMSs to reflect different API data structures
 *
 * @package Strata\Frontend\Data\Translation
 */
class ContentFieldResolver implements ResolverInterface
{
    /**
     * Takes a content schema field object and returns a content field from source value
     *
     * @param SchemaFieldInterface $contentModelField
     * @param $value
     * @return ContentFieldInterface Content field object, or null on failure to resolve content field
     * @throws ContentFieldTranslationNotFoundException
     */
    public function resolveContentField(SchemaFieldInterface $contentModelField, $value): ?ContentFieldInterface
    {
        $methodName = 'resolve' . ucfirst($contentModelField->getType()) . 'Field';

        if (!method_exists($this, $methodName)) {
            // @todo logger
            throw new ContentFieldTranslationNotFoundException(sprinf('Content field resolver for content type %s not found, ensure you have a resolver method called %s', $contentModelField->getType(), $methodName));
            return null;
        }

        return $this->$methodName($contentModelField, $value);
    }

    /**
     * @param SchemaFieldInterface $contentModelField
     * @param $value
     * @return ShortText|null
     * @throws \Strata\Frontend\Exception\ContentFieldException
     */
    public function resolveTextField(SchemaFieldInterface $contentModelField, $value): ?ShortText
    {
        return new ShortText($contentModelField->getName(), (string) $value);
    }

    public function resolveNumberField(SchemaFieldInterface $contentModelField, $value): ?Number
    {
        return new Number($contentModelField->getName(), $value);
    }

    public function resolvePlaintextField(SchemaFieldInterface $contentModelField, $value): ?PlainText
    {
        return new PlainText($contentModelField->getName(), (string) $value);
    }

    public function resolveRichtextField(SchemaFieldInterface $contentModelField, $value): ?RichText
    {
        return new RichText($contentModelField->getName(), (string) $value);
    }

    public function resolveDateField(SchemaFieldInterface $contentModelField, $value): ?Date
    {
        return new Date($contentModelField->getName(), $value);
    }

    public function resolveDatetimeField(SchemaFieldInterface $contentModelField, $value): ?DateTime
    {
        return new DateTime($contentModelField->getName(), $value);
    }

    public function resolveBooleanField(SchemaFieldInterface $contentModelField, $value): ?Boolean
    {
        return new Boolean($contentModelField->getName(), $value);
    }

    public function resolveArrayField(SchemaFieldInterface $contentModelField, $value): ?ArrayContent
    {
        $array = new ArrayContent($contentModelField->getName());

        if (!is_array($value) || empty($value)) {
            return null;
        }

        foreach ($value as $row) {

            // For each row add a set of content fields
            $item = new ContentFieldCollection();

            foreach ($contentModelField as $childField) {
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

    public function resolvePlainArrayField(SchemaFieldInterface $contentModelField, $value): ?PlainArray
    {
        if (!is_array($value)) {
            return null;
        }
        return new PlainArray($contentModelField->getName(), $value);
    }

    public function resolveDecimalField(SchemaFieldInterface $contentModelField, $value): ?Decimal
    {
        // TODO: Implement resolveDecimalField() method.
        return null;
    }

    public function resolveImageField(SchemaFieldInterface $contentModelField, $value): ?Image
    {
        // TODO: Implement resolveImageField() method.
        return null;
    }

    public function resolveRelationField(SchemaFieldInterface $contentModelField, $value): ?Relation
    {
        // TODO: Implement resolveRelationField() method.
        return null;
    }

    public function resolveRelationArrayField(SchemaFieldInterface $contentModelField, $value): ?RelationArray
    {
        // TODO: Implement resolveRelationArrayField() method.
        return null;
    }

    public function resolveFlexibleField(SchemaFieldInterface $contentModelField, $value): ?FlexibleContent
    {
        // TODO: Implement resolveFlexibleField() method.
        return null;
    }

}
