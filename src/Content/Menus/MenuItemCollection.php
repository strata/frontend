<?php

declare(strict_types=1);

namespace Strata\Frontend\Content\Menus;

class MenuItemCollection implements \SeekableIterator, \Countable
{
    protected $collection = [];
    protected $position = 0;

    /**
     * Add an item to the collection
     *
     * @param MenuItem $item MenuItem
     * @return MenuItemCollection Fluent interface
     */
    public function addItem(MenuItem $item): MenuItemCollection
    {
        $this->collection[] = $item;
        return $this;
    }

    /**
     * @return MenuItem
     */
    public function current(): MenuItem
    {
        return $this->collection[$this->position];
    }

    public function next()
    {
        ++$this->position;
    }

    public function key()
    {
        return $this->position;
    }

    public function valid()
    {
        return isset($this->collection[$this->position]);
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function count(): int
    {
        return count($this->collection);
    }

    public function seek($position)
    {
        if (!isset($this->collection[$position])) {
            throw new \OutOfBoundsException(sprintf('Invalid seek position: %s', $position));
        }
        $this->position = $position;
    }

    /**
     * @param string $oldUrl
     * @param string $newUrl
     * @return MenuItemCollection
     */
    public function setBaseUrls(string $oldUrl = '', string $newUrl = ''): MenuItemCollection
    {
        if (empty($this->collection)) {
            return $this;
        }

        foreach ($this->collection as $key => $menuItem) {
            if (!empty($menuItem->getChildren())) {
                $this->collection[$key]->getChildren()->setBaseUrls($oldUrl, $newUrl);
            }

            $this->collection[$key]->setBaseUrl($oldUrl, $newUrl);
        }

        return $this;
    }

    /**
     * @param string $currentPath
     * @return MenuItemCollection
     */
    public function setActiveItems(string $currentPath = ''): MenuItemCollection
    {
        if (empty($this->collection)) {
            return $this;
        }

        foreach ($this->collection as $key => $menuItem) {
            if (!empty($menuItem->getChildren())) {
                $this->collection[$key]->getChildren()->setActiveItems($currentPath);
            }

            // Set the menu item active if this is the current path
            if ($this->collection[$key]->urlContainsPath($currentPath)) {
                $this->collection[$key]->setActive(true);
            }
        }

        return $this;
    }
}
