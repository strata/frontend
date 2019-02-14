<?php

namespace Studio24\Frontend\Content;

use Studio24\Frontend\Content\Field\DateTime;

interface ContentInterface
{
    public function getId();
    public function getUrlSlug(): string;
    public function getDatePublished(): DateTime;
    public function getDateModified(): DateTime;
}
