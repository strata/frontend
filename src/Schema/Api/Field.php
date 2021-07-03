<?php

declare(strict_types=1);

namespace Strata\Frontend\Schema\Api;

use Strata\Frontend\Content\Field\FieldType;

class Field
{
    private string $name;
    private string $description;
    private string $type;

    public function __construct(string $name, string $type, ?string $description = null)
    {
        $this->name = $name;
        $this->setType($type);
        if (null !== $description) {
            $this->description = $description;
        }
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        if (FieldType::exists($type)) {
            $this->type = $type;
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

}