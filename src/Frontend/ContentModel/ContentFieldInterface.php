<?php
declare(strict_types=1);

namespace Studio24\Frontend\ContentModel;

interface ContentFieldInterface
{
    public function getName(): string;
    public function setName(string $name);
    public function getType(): string;
    public function getOption(string $name);
}
