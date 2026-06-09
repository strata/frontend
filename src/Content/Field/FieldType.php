<?php

declare(strict_types=1);

namespace Strata\Frontend\Content\Field;

use Strata\Frontend\Exception\ContentFieldException;

/**
 * Class to store list of valid content field types and classes
 *
 * Add any new content fields to this class
 */
class FieldType
{
    const ARRAY = 'array';
    const AUDIO = 'audio';
    const BOOLEAN = 'boolean';
    const DATE = 'date';
    const DATETIME = 'datetime';
    const DECIMAL = 'decimal';
    const DOCUMENT = 'document';
    const FLEXIBLE_CONTENT = 'flexible';
    const IMAGE = 'image';
    const NUMBER = 'number';
    const PLAIN_ARRAY = 'plainarray';
    const PLAIN_TEXT = 'plaintext';
    const RELATION = 'relation';
    const RELATION_ARRAY = 'relation_array';
    const RICH_TEXT = 'richtext';
    const SHORT_TEXT = 'text';
    const TAXONOMY_TERMS = 'taxonomyterms';
    const VIDEO = 'video';

    /**
     * Return class for a content field type
     *
     * @param string $type Content field type
     * @return string Class name that represents the field type
     * @throws ContentFieldException
     */
    public static function getClass(string $type): string
    {
        return match ($type) {
            self::ARRAY => ArrayContent::class,
            self::AUDIO => Audio::class,
            self::BOOLEAN => Boolean::class,
            self::DATE => Date::class,
            self::DATETIME => DateTime::class,
            self::DECIMAL => Decimal::class,
            self::DOCUMENT => Document::class,
            self::FLEXIBLE_CONTENT => FlexibleContent::class,
            self::IMAGE => Image::class,
            self::NUMBER => Number::class,
            self::PLAIN_ARRAY => PlainArray::class,
            self::PLAIN_TEXT => PlainText::class,
            self::RELATION => Relation::class,
            self::RELATION_ARRAY => RelationArray::class,
            self::RICH_TEXT => RichText::class,
            self::SHORT_TEXT => ShortText::class,
            self::TAXONOMY_TERMS => TaxonomyTerms::class,
            self::VIDEO => Video::class,
            default => throw new ContentFieldException(sprintf('Field type %s not recognised', $type)),
        };
    }

    /*

 * `video` -
      *

         TaxonomyTerms::TYPE,
         Video::TYPE
      * */


    /**
     * Test whether a content field type is defined
     *
     * @param string $type
     * @return bool
     */
    public static function exists(string $type): bool
    {
        try {
            self::getClass($type);
            return true;
        } catch (ContentFieldException) {
            return false;
        }
    }

    /**
     * Return array of valid field types
     *
     * @return array
     */
    public static function getFieldTypes(): array
    {
        $reflection = new \ReflectionClass(self::class);
        return $reflection->getConstants();
    }
}
