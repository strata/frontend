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
        $this->url = $this->setBaseUrl($url);
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

    protected function setBaseUrl(string $fullUrl): string
    {
        $urlArray = parse_url($fullUrl);
        $path = (isset($urlArray['path']))? $urlArray['path'] : '/';
        return $path;
    }
}
