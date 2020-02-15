<?php

/**
 * Created by PhpStorm.
 * User: Brian
 * Date: 21/02/2019
 * Time: 9:16
 */

namespace Studio24\Frontend\Content\Menus;

class Menu
{
    /**
     * @var int $id
     */
    protected $id;
    /**
     * @var string $name
     */
    protected $name;
    /**
     * @var string $slug
     */
    protected $slug;
    /**
     * @var string $description
     */
    protected $description;
    /**
     * @var MenuItemCollection $children
     */
    protected $children;

    /**
     * Menu constructor.
     */
    public function __construct()
    {
        $this->children = new MenuItemCollection();
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
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

    /**
     * @return MenuItemCollection
     */
    public function getChildren(): MenuItemCollection
    {
        return $this->children;
    }

    /**
     * @param MenuItemCollection $children
     */
    public function setChildren(MenuItemCollection $children): void
    {
        $this->children = $children;
    }

    /**
     * @param string $oldUrl
     * @param string $newUrl
     * @return Menu
     */
    public function setBaseUrls(string $oldUrl = '', string $newUrl = ''): Menu
    {
        if (empty($this->getChildren())) {
            return $this;
        }

        $this->getChildren()->setBaseUrls($oldUrl, $newUrl);

        return $this;
    }

    /**
     * @param string $currentPath
     * @return Menu
     */
    public function setActiveItems(string $currentPath = '/'): Menu
    {
        if (empty($this->getChildren())) {
            return $this;
        }

        $this->getChildren()->setActiveItems($currentPath);

        return $this;
    }
}
