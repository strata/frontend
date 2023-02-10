<?php

declare(strict_types=1);

namespace Strata\Frontend\Content\Field;

use Strata\Frontend\Content\Taxonomies\TermCollection;
use Strata\Frontend\Exception\ContentFieldException;

/**
 * Relation field
 *
 * Contains a collection of components, which contain content fields
 *
 * @package Strata\Frontend\Content\Field
 */
class TaxonomyTerms extends ContentField
{
    protected $content;

    /**
     * Create TaxonomyTerms content field
     *
     * @param string $name
     *
     * @throws \Strata\Frontend\Exception\ContentFieldException
     */
    public function __construct(string $name)
    {
        $this->setName($name);
        $this->content = new TermCollection();
    }

    /**
     * Set the TermCollection Object
     *
     * @param \Strata\Frontend\Content\Taxonomies\TermCollection $termCollection
     * @return \Strata\Frontend\Content\Field\TaxonomyTerms
     */
    public function setContent(TermCollection $termCollection): TaxonomyTerms
    {
        $this->content = $termCollection;

        return $this;
    }

    /**
     * check the TermCollection Object is not empty
     *
     * @return bool
     */
    public function hasContent(): bool
    {
        return !empty($this->getContent());
    }

    /**
     * get the TermCollection Object
     *
     * @return TermCollection
     */
    public function getContent(): ?TermCollection
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getValue(): ?string
    {
        if ($this->hasContent()) {
            return $this->getContent()->count() . ' terms';
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
