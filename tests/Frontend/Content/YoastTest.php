<?php

namespace App\Tests\Frontend\Content;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Studio24\Frontend\Cms\Wordpress;
use Studio24\Frontend\Content\Yoast;

class YoastTest extends TestCase
{
    /** @var Wordpress $wordpress */
    private $wordpress;

    public function setUp(): void
    {
        // Create a mock and queue responses
        $mock = new MockHandler([
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../responses/flexible-content/posts.1.json')
            ),
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../responses/flexible-content/posts.2.json')
            )
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $this->wordpress = new Wordpress('something');
        $this->wordpress->setClient($client);
    }

    public function testYoastClass()
    {
        $yoast = new Yoast();

        $this->assertInstanceOf(Yoast::class, $yoast);
    }

    public function testYoastAddData()
    {
        $yoast = new Yoast();

        $yoast->setTitle("New Title");
        $yoast->setMetadescription("New Description");
        $yoast->setMetakeywords("test,php,frontend");

        $this->assertSame("New Title", $yoast->getTitle());
        $this->assertSame("<meta name=\"description\" content=\"New Description\">", $yoast->getMetadescription());
    }

    public function testTwitterYoastData()
    {
        $yoast = new Yoast();

        $yoast->setTwitter("twitter_title", "twitter_description", "twitter_image");

        $this->assertSame(
            '<meta name="twitter:title" content="twitter_title"><meta name="twitter:description" content="twitter_description"><meta name="twitter:image" content="twitter_image">',
            $yoast->getTwitter()
        );
    }

    public function testAddYoastFromPage()
    {
        $posts = json_decode(file_get_contents(__DIR__ . '/../responses/flexible-content/posts.1.json'));

        $post = (array)$posts[7]->yoast;

        $yoast = new Yoast();

        $yoast->setOpengraph($post["opengraph-title"], $post["opengraph-description"], $post["opengraph-image"]);

        $this->assertSame(
            '<meta name="og:title" content="10 of your favourite Instagram posts"><meta name="og:description" content="As 2018 comes to a close, we look back at some of your favourite @FaunaFloraInt Instagram posts from the year..."><meta name="og:image" content="https://complex.demo/wp-content/uploads/2018/12/ten-of-your-favourite-instagram-posts-of-2018.png">',
            $yoast->getOpengraph()
        );
        $post = (array)$posts[3]->yoast;

        $yoast->setTwitter($post["twitter-title"], $post["twitter-description"], $post["twitter-image"]);

        $this->assertSame(
            '<meta name="twitter:image" content="https://complex.demo/wp-content/uploads/2019/01/lets-talk-about-the-elephant-that-wasnt-in-the-room-5.png">',
            $yoast->getTwitter()
        );
    }

    public function testFullPostData()
    {
        $posts = json_decode(file_get_contents(__DIR__ . '/../responses/flexible-content/posts.1.json'));

        $post = (array)$posts[0];

        $yoast = new Yoast();

        $yoast_data = (array)$post["yoast"];

        $yoast->setTitle(isset($yoast_data["title"]) && strlen($yoast_data["title"]) > 0 ? $yoast_data["title"] : $post["title"]->rendered);
        $yoast->setMetadescription($yoast_data["metadesc"]);
        $yoast->setMetakeywords($yoast_data["metakeywords"]);
        $yoast->setTwitter($yoast_data["twitter-title"], $yoast_data["twitter-description"], $yoast_data["twitter-image"]);
        $yoast->setTwitter($yoast_data["opengraph-title"], $yoast_data["opengraph-description"], $yoast_data["opengraph-image"]);

        $this->assertSame("", $yoast->getTwitter());
        $this->assertSame("", $yoast->getOpengraph());
        $this->assertSame("When is a Marine Protected Area not a Marine Protected Area?", $yoast->getTitle());
        $this->assertSame("", $yoast->getMetadescription());
        $this->assertSame("", $yoast->getMetakeywords());
    }
}
