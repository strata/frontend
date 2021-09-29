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
        switch ($type) {
            case self::ARRAY:
                return ArrayContent::class;
            case self::AUDIO:
                return Audio::class;
            case self::BOOLEAN:
                return Boolean::class;
            case self::DATE:
                return Date::class;
            case self::DATETIME:
                return DateTime::class;
            case self::DECIMAL:
                return Decimal::class;
            case self::DOCUMENT:
                return Document::class;
            case self::FLEXIBLE_CONTENT:
                return FlexibleContent::class;
            case self::IMAGE:
                return Image::class;
            case self::NUMBER:
                return Number::class;
            case self::PLAIN_ARRAY:
                return PlainArray::class;
            case self::PLAIN_TEXT:
                return PlainText::class;
            case self::RELATION:
                return Relation::class;
            case self::RELATION_ARRAY:
                return RelationArray::class;
            case self::RICH_TEXT:
                return RichText::class;
            case self::SHORT_TEXT:
                return ShortText::class;
            case self::TAXONOMY_TERMS:
                return TaxonomyTerms::class;
            case self::VIDEO:
                return Video::class;
            default:
                throw new ContentFieldException(sprintf('Field type %s not recognised', $type));
        }
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
        } catch (ContentFieldException $e) {
            return false;
        }
    }

    /**
     * Return array of valid field types
     *
     * @return array
     */
    static function getFieldTypes(): array
    {
        $reflection = new \ReflectionClass(__CLASS__);
        return $reflection->getConstants();
    }
}
