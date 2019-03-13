<?php
declare(strict_types=1);

namespace App\Tests\Frontend\Cms;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Studio24\Frontend\Cms\Wordpress;
use Studio24\Frontend\Content\Menus\Menu;
use Studio24\Frontend\Content\Menus\MenuItem;
use Studio24\Frontend\Content\Menus\MenuItemCollection;

class MenuTest extends TestCase
{
    /** @var Wordpress $wordpress */

    /** @var Menu $menu2 */
    private $menu2;
    /** @var Menu $menu8 */
    private $menu8;
    /** @var Menu $menu10 */
    private $menu10;
    /** @var Menu $menu10 */
    private $menu3;

    public function setUp() : void
    {
        // Create a mock and queue responses
        $mock = new MockHandler([
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../responses/menu/menu.2.json')
            ),
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../responses/menu/menu.8.json')
            ),
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../responses/menu/menu.10.json')
            ),
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../responses/menu/menu.3.json')
            ),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $wordpress = new Wordpress('something');
        $wordpress->setClient($client);
        $this->menu2 = $wordpress->getMenu(2);
        $this->menu8 = $wordpress->getMenu(8);
        $this->menu10 = $wordpress->getMenu(10);
        $this->menu3 = $wordpress->getMenu(3);
    }

    public function testMenu()
    {
        $this->assertSame(8, $this->menu8->getId());

        $this->assertInstanceOf(Menu::class, $this->menu8);
        $this->assertInstanceOf(MenuItemCollection::class, $this->menu8->getChildren());
        $this->assertInstanceOf(MenuItem::class, $this->menu8->getChildren()->current());

        $this->assertNull($this->menu3);
    }

    public function testMenuDetailChildren()
    {
        $this->assertSame(2, $this->menu2->getId());

        $x = 0;
        $children = $this->menu2->getChildren();
        while ($children->valid()) {
            $item = $children->current();
            switch ($x) {
                case 0:
                    $this->assertEquals('Explore', $item->getLabel());
                    $this->assertEquals("http://localhost/explore/", $item->getUrl());

                    $y = 0;
                    $childItems = $item->getChildren();
                    while ($childItems->valid()) {
                        $childItem = $childItems->current();
                        switch ($y) {
                            case 1:
                                $this->assertEquals("Countries", $childItem->getLabel());
                                $this->assertEquals("http://localhost/explore/countries/", $childItem->getUrl());
                                break;
                            case 2:
                                $this->assertEquals("Species", $childItem->getLabel());
                                break;
                        }
                        $y++;
                        $childItems->next();
                    }
                    break;
                case 1:
                    $this->assertEquals("Our Work", $item->getLabel());
                    break;
            }
            $x++;
            $children->next();
        }
    }

    public function testMenuCountChildren()
    {
        $this->assertSame(8, $this->menu8->getId());

        $this->assertSame(5, $this->menu8->getChildren()->count());
        $this->assertSame(1, $this->menu8->getChildren()->current()->getChildren()->count());
        $this->assertSame(15, $this->menu10->getChildren()->count());
        $this->assertSame(5, $this->menu10->getChildren()->current()->getChildren()->count());
    }
}
