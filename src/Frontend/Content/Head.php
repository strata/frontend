<?php
declare(strict_types=1);

namespace Studio24\Frontend\Content;

class Head
{
    protected $title;

    /**
     * Array of meta tags for this page in the format name => content
     * @var array
     */
    protected $meta = [];

    protected $allowedMeta = [
        "description",
        "keywords",
        "robots",
        "og:title",
        "og:image",
        "og:description",
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
            return "<meta name=\"" . $name . "\" content=\"" . $this->meta[$name] . "\">";
        }
        return null;
    }

    public function getAllMetaHtml(): string
    {
        $html = "";
        foreach ($this->meta as $name => $content) {
            $html .= "<meta name=\"" . $name . "\" content=\"" . $content . "\">\n";
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

    public function addMeta(string $name, string $content): bool
    {
        if (in_array($name, $this->allowedMeta)) {
            $this->meta[$name] = $content;
            return true;
        }
        return false;
    }

    public function __toString(): string
    {
        return $this->getAllMetaHtml();
    }

}
