<?php

declare(strict_types=1);

namespace Strata\Frontend\Content\Field;

use Strata\Frontend\Content\BaseContent;
use Strata\Frontend\Content\Page;

/**
 * Relation field
 *
 * Contains a collection of components, which contain content fields
 *
 * @package Strata\Frontend\Content\Field
 */
class Relation extends ContentField
{
    protected $content;

    /**
     * Content type of relation
     *
     * @var string
     */
    protected $contentType;

    /**
     * Create Relation content field
     *
     * @param string $name
     * @param string $contentType
     *
     * @throws \Strata\Frontend\Exception\ContentFieldException
     */
    public function __construct(string $name, $contentType = null)
    {
        $this->setName($name);
        $this->content = new Page();
        if ($contentType !== null) {
            $this->setContentType($contentType);
        }
    }

    /**
     * Set content type for this relation item
     *
     * @param string $contentType
     * @return Relation Fluent interface
     */
    public function setContentType($contentType): Relation
    {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * Return content type for this relation item
     *
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }


    /**
     * Set the Page Object
     *
     * @param \Strata\Frontend\Content\BaseContent $baseContentObject
     * @return \Strata\Frontend\Content\Field\Relation
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
    public function __toString(): string
    {
        return $this->getValue();
    }
}
