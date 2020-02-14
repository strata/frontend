<?php

namespace Studio24\Frontend\Content;

use Studio24\Frontend\Content\Field\ContentFieldInterface;

interface ContentInterface
{
    public function addContent(ContentFieldInterface $contentField);
}
