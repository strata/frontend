<?php

declare(strict_types=1);

namespace Strata\Frontend\View;

use Masterminds\HTML5;
use Strata\Frontend\Exception\ViewHelperException;
use Strata\Frontend\View\TableOfContents\Heading;
use Strata\Frontend\View\TableOfContents\HeadingCollection;

/**
 * Class to help generate table of contents (TOC) for HTML contents from headings
 *
 * Usage:
 *
 * // instantiate class (defaults to headings for H2 and H3)
 * $toc = new TableOfContents($html);
 *
 * // or specify heading levels to generate TOC for
 * $toc = new TableOfContents($html, ['h2', 'h3', 'h4']);
 *
 * // Return headings extracted from HTML (iterable object)
 * $headings = $toc->getHeadings();
 *
 * // output headings as UL TOC
 * echo $headings;
 *
 * // or set attributes on UL tag
 * echo $headings->ul(['class' => 'my-class']);
 *
 * // output HTML content with anchor links in headings
 * echo $toc->html();
 */
class TableOfContents
{
    private \DOMDocumentFragment $content;
    private HTML5 $html5;
    private ViewFilters $filters;
    private array $levels;
    private array $uniqueIds = [];
    private array $parsedHeadings;
    private ?HeadingCollection $headings = null;
    private bool $debug = false;

    /**
     * Constructor
     *
     * @param string $html HTML content to generate TOC for
     * @param array $levels Array of heading levels you want to generate TOC for, defaults to h2, h3
     * @throws ViewHelperException
     */
    public function __construct(string $html, array $levels = ['h2', 'h3'])
    {
        $this->html5 = new HTML5();
        $this->filters = new ViewFilters();

        $this->content =  $this->html5->loadHTMLFragment($html);
        $this->setLevels($levels);
        $this->parsedHeadings = $this->parseHeadingsFromHtml();
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }

    /**
     * Enable debug mode (outputs levels parsed in html)
     */
    public function enableDebug()
    {
        $this->debug = true;
    }

    public function disableDebug()
    {
        $this->debug = false;
    }

    /**
     * @return array
     */
    public function getLevels(): array
    {
        return $this->levels;
    }

    /**
     * Set heading levels
     * @param array $levels
     */
    public function setLevels(array $levels): void
    {
        $allowed = ['h1', 'h2', 'h2', 'h3', 'h4', 'h5', 'h6'];
        $error = [];
        foreach ($levels as $value) {
            if (!in_array($value, $allowed)) {
                $error[] = $value;
            }
        }
        if (count($error) > 0) {
            throw new ViewHelperException(sprintf('Heading levels can only contain h1-h6, invalid values: %s', implode(', ', $error)));
        }
        $this->levels = $levels;
    }

    /**
     * Return flat array of headings found in HTML content and update HTML content with heading ID attributes (anchor links)
     *
     * @param \DOMNodeList|null $childNodes
     * @param array $headings
     * @return array
     */
    protected function parseHeadingsFromHtml(?\DOMNodeList $childNodes = null, array $headings = []): array
    {
        $top = false;
        if (null === $childNodes) {
            $childNodes = $this->content->childNodes;
            $top = true;
        }

        /** @var \DOMElement $node */
        foreach ($childNodes as $node) {
            if (!($node instanceof \DOMElement)) {
                continue;
            }
            // Match a heading
            if (in_array($node->tagName, $this->levels)) {
                // Use id if exists, or generate from heading text
                if (!empty($node->getAttribute('id'))) {
                    $id = $node->getAttribute('id');
                } else {
                    $id = $this->filters->slugify($node->nodeValue);
                }
                $id = $this->getUniqueId($id);

                $headings[] = [
                    'level' => (int) ltrim($node->tagName, 'h'),
                    'name' => $node->nodeValue,
                    'link' => '#' . $id,
                    'children' => [],
                ];
                // Add id attribute to content node
                $node->setAttribute('id', $id);
            }

            // Parse child elements
            if (!empty($node->childNodes)) {
                $headings = $this->parseHeadingsFromHtml($node->childNodes, $headings);
            }
        }

        return $headings;
    }

    /**
     * Return flat array of parsed headings from HTML
     * @return array
     */
    public function getParsedHeadings(): array
    {
        return $this->parsedHeadings;
    }

    /**
     * Return a unique ID string for use in heading anchor links
     * @param string $id
     * @param int $increment
     * @return string
     * @throws ViewHelperException if 50 or more identical headings in HTML content
     */
    protected function getUniqueId(string $id, int $increment = 0): string
    {
        if ($increment > 48) {
            throw new ViewHelperException('Cannot generate unique ID, too many similar heading titles in content');
        }

        if ($increment > 0) {
            $testId = sprintf('%s-%d', $id, $increment);
        } else {
            $testId = $id;
        }
        if (!in_array($testId, $this->uniqueIds)) {
            $this->uniqueIds[] = $testId;
            return $testId;
        }
        $increment++;
        return $this->getUniqueId($id, $increment);
    }

    /**
     * Return hierarchical collection of headings found in HTML content
     *
     * This generates once, and saves the value in $this->headings
     *
     * To output HTML use: $this->headings->ul()
     *
     * @return HeadingCollection
     */
    public function getHeadings(): HeadingCollection
    {
        if ($this->headings instanceof HeadingCollection) {
            return $this->headings;
        }

        $collection = new HeadingCollection();
        $headings = $this->getParsedHeadings();
        if (empty($headings)) {
            return new HeadingCollection();
        }

        $rootLevel = $headings[0]['level'];
        $parents = [
            $rootLevel => $collection
        ];

        foreach ($headings as $heading) {
            $heading = new Heading($heading['level'], $heading['name'], $heading['link']);
            // Skip incorrectly nested headings (e.g. jumping from H2 to H4)
            if (!isset($parents[$heading->level])) {
                continue;
            }
            $addToCollection = $parents[$heading->level];
            $addToCollection->add($heading);
            $parents[$heading->level + 1] = $heading->children;
        }

        $this->headings = $collection;
        return $collection;
    }

    /**
     * Return HTML for parsed HTML content
     *
     * @return mixed
     */
    public function html()
    {
        $html = '';
        if ($this->isDebug()) {
            $levels = implode(', ', $this->levels);
            $html .= sprintf('<!-- Table of Contents generated for levels %s -->', $levels) . PHP_EOL;
        }
        $html .= $this->html5->saveHTML($this->content);
        return $html;
    }
}
