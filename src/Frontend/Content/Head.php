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
        "focuskw",
        "title",
        "metadesc",
        "linkdex",
        "metakeywords",
        "meta-robots-noindex",
        "meta-robots-nofollow",
        "meta-robots-adv",
        "canonical",
        "redirect",
        "opengraph-title",
        "opengraph-description",
        "opengraph-image",
        "twitter-title",
        "twitter-description",
        "twitter-image"
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
    public function getMeta(string $name)
    {

    }

    public function getMetaHtml(string $name): string
    {

    }

    public function getAllMetaHtml(): string
    {

    }


    /**
     * @param mixed $meta
     */
    public function setMeta(array $meta): void
    {


    }

    public function addMeta(string $name, string $content)
    {

    }

    public function __toString(): string
    {
        return $this->getAllMetaHtml();
    }

}
