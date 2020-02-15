<?php

declare(strict_types=1);

namespace Strata\Frontend\ContentModel;

/**
 * Represents a content field definition (e.g. title field)
 *
 * @package Strata\Frontend\ContentModel
 */
class Field implements FieldInterface
{
    /**
     * Content field name
     *
     * @var string
     */
    protected $name;

    /**
     * Content field type
     *
     * @var string
     */
    protected $type;

    /**
     * Array of content field options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Constructor
     *
     * @param string $name
     * @param string $type
     */
    public function __construct(string $name, string $type)
    {
        $this->setName($name);
        $this->setType($type);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Field Fluent interface
     */
    public function setName(string $name): Field
    {
        $this->name = $name;
        return $this;
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
     * @return Field Fluent interface
     */
    public function setType(string $type): Field
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Return an option
     *
     * @param string $name
     * @return mixed|null
     */
    public function getOption(string $name, ContentModel $contentModel = null)
    {
        if (isset($this->options[$name])) {
            return $this->options[$name];
        }

        if (empty($contentModel)) {
            return null;
        }

        $globalOptionValue = $contentModel->getGlobal($name);

        if (!empty($globalOptionValue)) {
            return $globalOptionValue;
        }

        return null;
    }

    /**
     * Return whether the content field has this option set
     *
     * @param string $name
     * @return bool
     */
    public function hasOption(string $name): bool
    {
        return isset($this->options[$name]);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return Field Fluent interface
     */
    public function addOption(string $name, $value): Field
    {
        $this->options[$name] = $value;
        return $this;
    }

    /**
     * @param array $options
     * @return Field Fluent interface
     */
    public function setOptions(array $options): Field
    {
        foreach ($options as $name => $value) {
            $this->addOption($name, $value);
        }
        return $this;
    }
}
