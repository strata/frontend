<?php
/**
 * Created by PhpStorm.
 * User: bdeboevere
 * Date: 2019-03-11
 * Time: 16:22
 */

namespace App\Tests\Frontend\Cms;


use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Studio24\Frontend\Cms\Wordpress;
use Studio24\Frontend\ContentModel\ContentModel;

class HeadPageTest extends TestCase
{
    /**
     * @var Wordpress $wordpress
     */
    private $wordpress;

    public function setUp() : void
    {
        // Create a mock and queue responses
        $mock = new MockHandler([
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../responses/flexible-content/post.4.json')
            )
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $this->wordpress = new Wordpress('something', new ContentModel(__DIR__ . '/config/flexible-content/content_model.yaml'));
        $this->wordpress->setContentType('news');
        $this->wordpress->setClient($client);
    }

    public function testCreatePageWithHead()
    {
//        $json = file_get_contents(__DIR__ . '/../responses/flexible-content/post.3.json');
//        $page_data = (array)json_decode($json);
//        $page = $this->wordpress->createPage($page_data);
        $page = $this->wordpress->getPage(12345);
        $head = $page->getHead();
        $this->assertFalse(empty($head));
        $this->assertSame("On the horizon: looking ahead for global conservation, and hello from yoast", $head->getTitle());
    }
}
