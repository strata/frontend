<?php

declare(strict_types=1);

namespace Strata\Data\Metadata;

interface RepositoryInterface
{
    /**
     * Gets the script rquired for the table set up.
     * This method can be extended to support multiple storage types
     *
     * @param string $storageType
     * @return string
     */
    public function getTableSetupScript(string $storageType): string;
}
