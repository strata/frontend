<?php
declare(strict_types=1);

namespace App\Tests\Frontend\Cms;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Studio24\Frontend\Cms\Wordpress;

class WordPressMenuTest extends TestCase
{
    public function testMenu()
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
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $wordpress = new Wordpress('something');
        $wordpress->setClient($client);

        // Test it!
        $menu = $wordpress->getMenu(2);
        $this->assertSame(2, $menu->getId());

        // @todo tests, example code below (please edit to fit your code!)
        $x = 0;
        $children = $menu->getChildren();
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

        // Test it!
//        $menu = $wordpress->getMenu(8);

        // @todo tests
    }

}
