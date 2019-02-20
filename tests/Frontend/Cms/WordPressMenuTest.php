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

        // @todo tests

        // Test it!
        $menu = $wordpress->getMenu(8);

        // @todo tests
    }

}
