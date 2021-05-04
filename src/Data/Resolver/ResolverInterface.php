<?php

declare(strict_types=1);

namespace Strata\Frontend\Data\Resolver;

use Strata\Frontend\Content\Field\ArrayContent;
use Strata\Frontend\Content\Field\Boolean;
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
use Strata\Frontend\Schema\ContentType;
use Strata\Frontend\Schema\Field\ArraySchemaField;
use Strata\Frontend\Schema\Field\FlexibleSchemaField;
use Strata\Frontend\Schema\Field\SchemaFieldInterface;

/**
 * Resolve custom content field from source data to content object
 */
interface ResolverInterface
{
    public function resolveContentField(SchemaFieldInterface $contentField, $value): ?ContentFieldInterface;

    public function resolveTextField(SchemaFieldInterface $contentField, $value): ?ShortText;

    public function resolvePlaintextField(SchemaFieldInterface $contentField, $value): ?PlainText;

    public function resolveRichtextField(SchemaFieldInterface $contentField, $value): ?RichText;

    public function resolveDateField(SchemaFieldInterface $contentField, $value): ?Date;

    public function resolveDatetimeField(SchemaFieldInterface $contentField, $value): ?DateTime;

    public function resolveBooleanField(SchemaFieldInterface $contentField, $value): ?Boolean;

    public function resolveNumberField(SchemaFieldInterface $contentField, $value): ?Number;

    public function resolveDecimalField(SchemaFieldInterface $contentField, $value): ?Decimal;

    public function resolvePlainArrayField(SchemaFieldInterface $contentField, $value): ?PlainArray;

    public function resolveArrayField(ArraySchemaField $contentField, $value): ?ArrayContent;

    public function resolveImageField(SchemaFieldInterface $contentField, $value): ?Image;

    public function resolveRelationField(SchemaFieldInterface $contentField, $value): ?Relation;

    public function resolveRelationArrayField(SchemaFieldInterface $contentField, $value): ?RelationArray;

    public function resolveFlexibleField(FlexibleSchemaField $contentField, $value): ?FlexibleContent;
}
