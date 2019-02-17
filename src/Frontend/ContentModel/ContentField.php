<?php
declare(strict_types=1);

namespace Studio24\Frontend\ContentModel;

/**
 * Represents a content field definition (e.g. title field)
 *
 * @package Studio24\Frontend\ContentModel
 */
class ContentField implements ContentFieldInterface
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
     * @return ContentField Fluent interface
     */
    public function setName(string $name): ContentField
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
     * @return ContentField Fluent interface
     */
    public function setType(string $type): ContentField
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
    public function getOption(string $name)
    {
        if (isset($this->options[$name])) {
            return $this->options[$name];
        }

        // @todo Check global option

        return null;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return ContentField Fluent interface
     */
    public function addOption(string $name, $value): ContentField
    {
        $this->options[$name] = $value;
        return $this;
    }

    /**
     * @param array $options
     * @return ContentField Fluent interface
     */
    public function setOptions(array $options): ContentField
    {
        foreach ($options as $name => $value) {
            $this->addOption($name, $value);
        }
        return $this;
    }
}
