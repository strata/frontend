<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Strata\Frontend\Content\Field\ArrayContent;
use Strata\Frontend\Content\Field\Audio;
use Strata\Frontend\Content\Field\Boolean;
use Strata\Frontend\Content\Field\Date;
use Strata\Frontend\Content\Field\DateTime;
use Strata\Frontend\Content\Field\Decimal;
use Strata\Frontend\Content\Field\Document;
use Strata\Frontend\Content\Field\FieldType;
use Strata\Frontend\Content\Field\FlexibleContent;
use Strata\Frontend\Content\Field\Image;
use Strata\Frontend\Content\Field\Number;
use Strata\Frontend\Content\Field\PlainArray;
use Strata\Frontend\Content\Field\PlainText;
use Strata\Frontend\Content\Field\Relation;
use Strata\Frontend\Content\Field\RelationArray;
use Strata\Frontend\Content\Field\RichText;
use Strata\Frontend\Content\Field\ShortText;
use Strata\Frontend\Content\Field\TaxonomyTerms;
use Strata\Frontend\Content\Field\Video;

class FieldTypeTest extends TestCase
{
    public function availableContentTypes(): array
    {
        return [
            ['array', ArrayContent::class],
            ['audio', Audio::class],
            ['boolean', Boolean::class],
            ['date', Date::class],
            ['datetime', DateTime::class],
            ['decimal', Decimal::class],
            ['document', Document::class],
            ['flexible', FlexibleContent::class],
            ['image', Image::class],
            ['number', Number::class],
            ['plainarray', PlainArray::class],
            ['plaintext', PlainText::class],
            ['relation', Relation::class],
            ['relation_array', RelationArray::class],
            ['richtext', RichText::class],
            ['text', ShortText::class],
            ['taxonomyterms', TaxonomyTerms::class],
            ['video', Video::class],
        ];
    }

    public function testGetFieldTypes()
    {
        $expectedFieldTypes = [];
        foreach ($this->availableContentTypes() as $item) {
            $expectedFieldTypes[] = $item[0];
        }
        $fieldTypes = array_values(FieldType::getFieldTypes());

        $this->assertSame($expectedFieldTypes, $fieldTypes);
    }

    /**
     * @dataProvider availableContentTypes
     */
    public function testExists(string $type)
    {
        $this->assertTrue(FieldType::exists($type));
    }

    /**
     * @dataProvider availableContentTypes
     */
    public function testGetClass(string $type, string $className)
    {
        $this->assertSame($className, FieldType::getClass($type));
    }
}
