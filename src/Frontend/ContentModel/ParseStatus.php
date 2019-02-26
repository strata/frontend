<?php
declare(strict_types=1);

namespace Studio24\Frontend\ContentModel;

/**
 * Class to track where we are in the content parsing process and return helpful error messages if anything goes wrong
 *
 * @package Studio24\Frontend\ContentModel
 */
class ParseStatus
{
    /**
     * Content type
     *
     * @var string
     */
    protected $contentType;

    /**
     * Content field name
     *
     * @var string
     */
    protected $fieldName;

    /**
     * Content field type
     *
     * @var string
     */
    protected $fieldType;

    /**
     * Parent content type
     *
     * @var ParseStatus
     */
    protected $parent;

    /**
     * Content we are parsing
     *
     * @var mixed
     */
    protected $content;

    /**
     * Constructor
     *
     * @param string $type Content type we are currently parsing
     * @param ParseStatus|null $parent
     */
    public function __construct(string $type, ParseStatus $parent = null)
    {
        $this->contentType = $type;

        if ($parent instanceof ParseStatus) {
            $this->parent = $parent;
        }
    }

    /**
     * We are currently parsing this field
     *
     * @param string $fieldName Field name we are parsing
     * @param string $fieldType Field type we are parsing
     * @param mixed $content Actual content we are parsing
     */
    public function parsing(string $fieldName, string $fieldType, $content)
    {
        $this->fieldName = $fieldName;
        $this->fieldType = $fieldType;
        $this->content = $content;
    }

    /**
     * Return array of parent content status objects
     *
     * @return array
     */
    public function getParents(): array
    {
        $parents = [];
        if ($this->parent instanceof ParseStatus) {
            $parents[] = sprintf('%s > %s (%s)', $this->parent->getContentType(), $this->parent->getFieldName(), $this->parent->getFieldType());
            $parents = array_merge($parents, $this->parent->getParents());
            $parents = array_reverse($parents);
        }
        return $parents;
    }

    /**
     * Return content type
     *
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * Return field name
     *
     * @return mixed
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * Return field type
     *
     * @return string
     */
    public function getFieldType(): string
    {
        return $this->fieldType;
    }

    /**
     * Return string representation of content we are currently parsing
     *
     * @return string
     */
    public function getContent(): string
    {
        return var_export($this->content, true) . PHP_EOL;
    }

    /**
     * Return string representation of current status in content parsing process
     *
     * @return string
     */
    public function __toString()
    {
        $parents = $this->getParents();
        if (count($parents) > 0) {
            $parents = sprintf('Parents: %s', implode(' > ', $parents), $this->contentType, $this->fieldName) . PHP_EOL;
        } else {
            $parents = '';
        }

        return $parents . sprintf(
            'Type: %s' . PHP_EOL . 'Field: %s (%s)' . PHP_EOL . 'Content: %s',
            $this->getContentType(),
            $this->getFieldName(),
            $this->getFieldType(),
            $this->getContent()
        );
    }
}
