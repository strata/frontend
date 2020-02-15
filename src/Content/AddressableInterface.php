<?php

declare(strict_types=1);

namespace Strata\Frontend\Content;

use Strata\Frontend\Content\Field\DateTime;

interface AddressableInterface
{
    public function getId();
    public function getUrlSlug(): string;
    public function getDatePublished(): DateTime;
    public function getDateModified(): DateTime;
}
