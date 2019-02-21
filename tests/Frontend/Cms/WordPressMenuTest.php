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

        // @todo tests, example code below (please edit to fit your code!)
        $x = 0;
        foreach ($menu as $item) {
            switch ($x) {
                case 0:
                    $this->assertEquals('Explore', $item->getTitle());
                    $this->assertEquals("http://localhost/explore/", $item->getUrl());

                    $y = 0;
                    foreach ($item->getChildren() as $childItem) {
                        switch ($y) {
                            case 1:
                                $this->assertEquals("Countries", $childItem->getTitle());
                                $this->assertEquals("http://localhost/explore/countries/", $childItem->getUrl());
                                break;
                            case 2:
                                $this->assertEquals("Species", $childItem->getTitle());
                                break;
                        }
                        $y++;
                    }
                    break;
                case 1:
                    $this->assertEquals("Our Work", $item->getTitle());
                    break;
            }
            $x++;
        }

        // Test it!
        $menu = $wordpress->getMenu(8);

        // @todo tests
    }

}
