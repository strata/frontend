<?php

declare(strict_types=1);

namespace Strata\Frontend\Content\Menus;

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
     * @var bool $active
     */
    protected $active = false;
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

        $regex = '/^' . preg_quote($oldDomain, '/') . '/';

        $newUrl = preg_replace($regex, $newDomain, $this->getUrl());

        $this->setUrl($newUrl);

        return $this;
    }

    /**
     * Does the current URL contain the
     *
     * @param $currentPath
     * @return bool
     */
    public function urlContainsPath(string $currentPath)
    {
        // Remove the final slash from currentPath if it exists
        $currentPath = rtrim($currentPath, '/');
        $currentUrl = rtrim($this->getUrl(), '/');

        // We check the $currentPath isn't empty, then check if the $currentPath is at the end of the current url
        // The - on strlen ensures we use the end of the string for comparison rather than the start
        if (!empty($currentPath) && substr($currentUrl, -strlen($currentPath)) == $currentPath) {
            return true;
        }

        return false;
    }
}
