<?php

declare(strict_types=1);

namespace Strata\Frontend\View\TableOfContents;

use Masterminds\HTML5;
use Strata\Data\Traits\IterableTrait;

class HeadingCollection implements \SeekableIterator, \Countable
{
    use IterableTrait;

    /**
     * Return current item
     * @return mixed
     */
    public function current(): Heading
    {
        return $this->collection[$this->position];
    }

    /**
     * Add an item to the collection
     * @param Heading $heading
     */
    public function add(Heading $heading)
    {
        $this->collection[] = $heading;
    }

    /**
     * Output the nested headings as HTML <ul> content
     *
     * @param array $attributes Array of HTML attributes to add to <ul> tag
     * @return string
     */
    public function ul(array $attributes = []): string
    {
        // Generate a UL DOMElement
        $html5 = new HTML5();
        $doc = $html5->loadHTML('<!DOCTYPE html><body><ul></ul></body></html>');
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;

        $results = $doc->getElementsByTagName('ul');
        $ul = $results->item(0);

        foreach ($attributes as $name => $value) {
            $ul->setAttribute($name, $value);
        }

        $this->buildListItems($doc, $ul, $this);

        return $html5->saveHTML($ul);
    }

    /**
     * Build list items for UL element
     *
     * @param \DOMDocument $doc
     * @param \DOMElement $ul
     * @param HeadingCollection $collection
     */
    protected function buildListItems(\DOMDocument $doc, \DOMElement $ul, HeadingCollection $collection)
    {
        foreach ($collection as $heading) {
            $link = $doc->createElement('a');
            $link->setAttribute('href', $heading->link);
            $link->nodeValue = $heading->name;

            $listItem =  $doc->createElement('li');
            $listItem->appendChild($link);

            if (count($heading->children) > 0) {
                $childUl = $doc->createElement('ul');
                $this->buildListItems($doc, $childUl, $heading->children);
                $listItem->appendChild($childUl);
            }

            $ul->appendChild($listItem);
        }
    }

    /**
     * Output table of contents as HTML
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->ul();
    }
}
