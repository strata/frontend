<?php

declare(strict_types=1);

namespace Studio24\Frontend\ContentModel;

interface FieldInterface
{
    public function getName(): string;
    public function setName(string $name);
    public function getType(): string;
    public function hasOption(string $name): bool;
    public function getOption(string $name);
}
