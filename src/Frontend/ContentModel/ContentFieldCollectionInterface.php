<?php
declare(strict_types=1);

namespace Studio24\Frontend\ContentModel;

interface ContentFieldCollectionInterface
{
    public function parseContentFieldArray(string $name, array $data): FieldInterface;
}
