<?php

declare(strict_types=1);

namespace Strata\Frontend\View\TableOfContents;

class Heading
{
    public HeadingCollection $children;

    /**
     * Heading constructor.
     * @param int $level
     * @param string $name
     * @param string $link
     */
    public function __construct(public int $level, public string $name, public string $link)
    {
        $this->children = new HeadingCollection();
    }
}
