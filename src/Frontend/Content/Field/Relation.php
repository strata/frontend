<?php
declare(strict_types=1);

namespace Studio24\Frontend\Content\Field;

use Studio24\Frontend\Content\BaseContent;
use Studio24\Frontend\Content\Page;

/**
 * Relation field
 *
 * Contains a collection of components, which contain content fields
 *
 * @package Studio24\Frontend\Content\Field
 */
class Relation extends ContentField
{
    const TYPE = 'relation';

    protected $content;

    /**
     * Create Relation content field
     *
     * @param string $name
     *
     * @throws \Studio24\Frontend\Exception\ContentFieldException
     */
    public function __construct(string $name)
    {
        $this->setName($name);
        $this->content = new BaseContent();
    }

    /**
     * Set the Page Object
     *
     * @param \Studio24\Frontend\Content\BaseContent $baseContentObject
     * @return \Studio24\Frontend\Content\Field\Relation
     */
    public function setContent(BaseContent $baseContentObject): Relation
    {
        $this->content = $baseContentObject;

        return $this;
    }

    /**
     * get the Page Object
     *
     * @return bool
     */
    public function hasContent(): bool
    {
        return !empty($this->getContent());
    }

    /**
     * get the Page Object
     *
     * @return Page
     */
    public function getContent(): ?BaseContent
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getValue(): ?string
    {
        if ($this->hasContent()) {
            return $this->getContent()->getTitle();
        }

        return '';
    }

    /**
     * Return string representation of content field
     *
     * @return string
     */
    public function __toString(): ?string
    {
        return $this->getValue();
    }
}
