<?php

declare(strict_types=1);

namespace Strata\Frontend\Schema\Field;

interface SchemaFieldInterface
{
    public function getName(): string;
    public function setName(string $name);
    public function getType(): string;
    public function hasOption(string $name): bool;
    public function getOption(string $name);
    public function getOptions(): array;
}
