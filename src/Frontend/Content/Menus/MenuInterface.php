<?php
/**
 * Created by PhpStorm.
 * User: Brian
 * Date: 21/02/2019
 * Time: 12:14
 */

namespace Studio24\Frontend\Content\Menus;


use Studio24\Frontend\Content\MenuItemCollection;

interface MenuInterface
{
    public function getChildren() : MenuItemCollection;
}