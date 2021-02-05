<?php

declare(strict_types=1);

namespace Strata\Data\Metadata;

use DateTime;

class MetadataFactory
{
    /**
     * Create a new Metadata object
     *
     * @return \Strata\Data\Metadata\Metadata
     * @throws \Exception
     */
    public function createNew(): Metadata
    {
        $metaData = new Metadata();
        $metaData->setCreatedAt(new DateTime());

        return $metaData;
    }
}
