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
    private array $levels;
    private array $uniqueIds = [];
    private array $parsedHeadings;
    private ?HeadingCollection $headings = null;
    private bool $debug = false;

    /**
     * Constructor
     *
     * @param string $html HTML content to generate TOC for
     * @param array $levels Array of heading levels you want to generate TOC for
     * @throws ViewHelperException
     */
    public function __construct(string $html, array $levels = ['h2', 'h3'])
    {
        $this->html5 = new HTML5();

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
     * Return levels of HTML headings we are generating ToC for
     * @return array
     */
    public function getLevels(): array
    {
        return $this->levels;
    }

    /**
     * Set levels of HTML headings we are generating ToC for
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
     * Parse headings from HTML
     *
     * Parses headings from HTML content (DOMNodeList), updates the HTML content with heading ID attributes (anchor links),
     * and returns a flat array of found headings.
     *
     * @param \DOMNodeList|null $childNodes Parse HTML from passed $childNodes or from the HTML content passed in the constructor
     * @param array $headings Optional existing headings array to add further parsed headings to
     * @return array Headings array
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
                    // Create safe string for ID attribute
                    $id = $this->escapeIdAttribute($node->nodeValue);
                }
                $id = $this->getUniqueId($id);

                $headings[] = [
                    'level' => (int) ltrim($node->tagName, 'h'),
                    'name' => $this->escapeHeadingName($node->nodeValue),
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
     * Generate a safe string to use as an ID attribute
     *
     * To avoid inadvertent errors, only ASCII letters, digits, '_', and '-' should be used and the value for an id
     * attribute should start with a letter.
     * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes/id
     *
     * @param string $string
     * @return string
     */
    public function escapeIdAttribute(string $string): string
    {
        $string = mb_strtolower($string, 'UTF-8');

        // Convert spaces
        $string = preg_replace('/\&nbsp;/', '-', $string);
        $string = preg_replace('/\s/', '-', $string);

        // Remove anything that isn't an ASCII letter, number, underscore _ or dash -
        $string = strip_tags($string);
        $string = preg_replace('/[^A-Za-z0-9_-]+/', '', $string);

        // Ensure string starts with a letter
        if (!preg_match('/^[a-z]/', $string)) {
            $string = 'h-' . $string;
        }

        // Remove duplicate spaces
        $string = preg_replace('/-{2,}/', '-', $string);

        return $string;
    }

    /**
     * Generate a safe string for use as the heading anchor link value
     * @param string $string
     * @return string
     */
    public function escapeHeadingName(string $string): string
    {
        // Filter
        $string = htmlentities($string);

        // Convert nbsp back to spaces
        $string = preg_replace('/\&nbsp;/i', ' ', $string);

        return $string;
    }

    /**
     * Return array of parsed headings from HTML
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
