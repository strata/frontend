<?php
/**
 * Created by PhpStorm.
 * User: BenDB
 * Date: 21/02/2019
 * Time: 09:19
 */

namespace Studio24\Frontend\Content\Menus;

class MenuItem
{
    /**
     * @var int $id
     */
    protected $id;
    /**
     * @var string $label
     */
    protected $label;
    /**
     * @var string $url
     */
    protected $url;
    /**
     * @var bool $url
     */
    protected $active;
    /**
     * @var MenuItemCollection $children
     */
    protected $children;

    /**
     * MenuItem constructor.
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
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
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

    public function setBaseUrl(string $oldDomain, string $newDomain): MenuItem
    {
        $oldDomain = rtrim($oldDomain, '/');
        $newDomain = rtrim($newDomain, '/');

        $regex = '/^'.preg_quote($oldDomain, '/').'/';

        $newUrl = preg_replace($regex, $newDomain, $this->getUrl());

        $this->setUrl($newUrl);

        return $this;
    }
}
