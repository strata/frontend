<?php

declare(strict_types=1);

namespace Strata\Frontend\Content;

use Strata\Frontend\Content\Field\ContentFieldInterface;

interface ContentInterface
{
    public function addContent(ContentFieldInterface $contentField);
}
