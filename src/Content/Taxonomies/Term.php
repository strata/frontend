<?php

declare(strict_types=1);

namespace Strata\Frontend\Content\Taxonomies;

/**
 * Taxonomy term class
 *
 * @package Strata\Frontend\Content\Taxonomies
 */
class Term
{
    protected $id;

    protected $name;

    protected $slug;

    protected $link;

    protected $count;

    protected $description;

    /**
     * Term constructor.
     *
     * @param int $id
     * @param string $name
     * @param string $slug
     * @param string $link
     * @param int $count
     * @param string $description
     */
    public function __construct(
        int $id,
        string $name,
        string $slug,
        string $link,
        int $count = 0,
        string $description = ''
    ) {
        $this->setID($id);
        $this->setName($name);
        $this->setSlug($slug);
        $this->setLink($link);
        $this->setCount($count);
        $this->setDescription($description);
    }

    /**
     * ID getter
     *
     * @return int
     */
    public function getID(): int
    {
        return $this->id;
    }

    /**
     * ID setter
     *
     * @param int
     */
    public function setID(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    /**
     * @return integer
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param integer $count
     */
    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
