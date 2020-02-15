<?php

declare(strict_types=1);

namespace Strata\Frontend\ContentModel;

interface ContentFieldCollectionInterface
{
    public function parseContentFieldArray(string $name, array $data, string $configDir): FieldInterface;
}
