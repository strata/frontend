<?php

declare(strict_types=1);

namespace Strata\Frontend\Content;

use Strata\Data\Collection;

class PageCollection extends Collection
{
    /**
     * Metadata
     *
     * @var Metadata
     */
    private Metadata $metadata;

    public function __construct()
    {
        $this->metadata = new Metadata();
    }

    /**
     * Collection metadata
     *
     * @return Metadata
     */
    public function getMetadata(): Metadata
    {
        return $this->metadata;
    }

    /**
     * Return current item
     *
     * @return Page
     */
    public function current(): Page
    {
        return parent::current();
    }

}
