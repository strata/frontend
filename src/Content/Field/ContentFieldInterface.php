<?php

declare(strict_types=1);

namespace Strata\Frontend\Content\Field;

/**
 * Content field interface
 *
 * @package Strata\Frontend\Content
 */
interface ContentFieldInterface
{
    public function getType(): string;
    public function hasHtml(): bool;
    public function setName(string $name): ContentFieldInterface;
    public function getName(): string;
    public function getValue();
    public function __toString(): ?string;
}
