<?php

namespace Studio24\Frontend\Content;

use Studio24\Frontend\Content\Field\DateTime;

interface ContentInterface
{
    public function setUrlPattern($urlPattern);
    public function getUrlPattern(): Url;
    public function getUrl(): string;
    public function getId();
    public function getUrlSlug(): string;
    public function getDatePublished(): DateTime;
    public function getDateModified(): DateTime;
}