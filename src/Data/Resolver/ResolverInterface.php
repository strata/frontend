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
use Strata\Frontend\Schema\Field\SchemaFieldInterface;

/**
 * Resolve custom content field from source data to content object
 */
interface ResolverInterface
{
    public function resolveContentField(SchemaFieldInterface $contentModelField, $value): ?ContentFieldInterface;

    public function resolveTextField(SchemaFieldInterface $contentModelField, $value): ?ShortText;

    public function resolvePlaintextField(SchemaFieldInterface $contentModelField, $value): ?PlainText;

    public function resolveRichtextField(SchemaFieldInterface $contentModelField, $value): ?RichText;

    public function resolveDateField(SchemaFieldInterface $contentModelField, $value): ?Date;

    public function resolveDatetimeField(SchemaFieldInterface $contentModelField, $value): ?DateTime;

    public function resolveBooleanField(SchemaFieldInterface $contentModelField, $value): ?Boolean;

    public function resolveNumberField(SchemaFieldInterface $contentModelField, $value): ?Number;

    public function resolveDecimalField(SchemaFieldInterface $contentModelField, $value): ?Decimal;

    public function resolvePlainArrayField(SchemaFieldInterface $contentModelField, $value): ?PlainArray;

    public function resolveArrayField(SchemaFieldInterface $contentModelField, $value): ?ArrayContent;

    public function resolveImageField(SchemaFieldInterface $contentModelField, $value): ?Image;

    public function resolveRelationField(SchemaFieldInterface $contentModelField, $value): ?Relation;

    public function resolveRelationArrayField(SchemaFieldInterface $contentModelField, $value): ?RelationArray;

    public function resolveFlexibleField(SchemaFieldInterface $contentModelField, $value): ?FlexibleContent;
}