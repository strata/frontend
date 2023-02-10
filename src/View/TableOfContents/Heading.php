<?php

declare(strict_types=1);

namespace Strata\Frontend\View\TableOfContents;

class Heading
{
    public int $level;
    public string $name;
    public string $link;
    public HeadingCollection $children;

    /**
     * Heading constructor.
     * @param int $level
     * @param string $name
     * @param string $link
     */
    public function __construct(int $level, string $name, string $link)
    {
        $this->level = $level;
        $this->name = $name;
        $this->link = $link;
        $this->children = new HeadingCollection();
    }
}
