<?php

declare(strict_types=1);

namespace Strata\Frontend\Content;

use Strata\Frontend\Exception\MetaTagNotAllowedException;

class Head
{
    protected $title;

    /**
     * Array of meta tags for this page in the format name => content
     * @var array
     */
    protected $meta = [];

    private $allowedMeta = [
        "description",
        "keywords",
        "robots",
        "og:title",
        "og:image",
        "og:description",
        "twitter:card",
        "twitter:title",
        "twitter:description",
        "twitter:image"
    ];

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getMeta(string $name): ?string
    {
        if (isset($this->meta[$name])) {
            return $this->meta[$name];
        }
        return null;
    }

    public function getMetaHtml(string $name): ?string
    {
        if (isset($this->meta[$name])) {
            return $this->createMetaOutput($name, $this->meta[$name], false);
        }
        return null;
    }

    public function getAllMetaHtml(): string
    {
        $html = "";
        foreach ($this->meta as $name => $content) {
            $html .= $this->createMetaOutput($name, $content);
        }
        return $html;
    }

    /**
     * @param mixed $meta
     */
    public function setMeta(array $meta): void
    {
        $this->meta = $meta;
    }


    /**
     * @param string $name
     * @param string $content
     * @throws MetaTagNotAllowedException
     */
    public function addMeta(string $name, string $content): void
    {
        if (!in_array($name, $this->allowedMeta)) {
            throw new MetaTagNotAllowedException();
        }
        $this->meta[$name] = $content;
    }

    private function createMetaOutput(string $name, string $content, bool $linebreak = true): string
    {
        $output = '';
        if (preg_match('/^og:/', $name)) {
            $output = "<meta property=\"" . $name . "\" content=\"" . $content . "\">";
        } else {
            $output = "<meta name=\"" . $name . "\" content=\"" . $content . "\">";
        }

        if ($linebreak == true) {
            $output .= "\n";
        }
        return $output;
    }

    public function __toString(): string
    {
        return $this->getAllMetaHtml();
    }
}
