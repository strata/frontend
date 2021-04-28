<?php

declare(strict_types=1);

namespace Strata\Frontend\Schema;

use Strata\Frontend\Schema\Field\SchemaFieldInterface;

interface ContentFieldCollectionInterface
{
    public function parseContentFieldArray(string $name, array $data, string $configDir): SchemaFieldInterface;
}
