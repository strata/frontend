<?php

namespace App\Tests\Frontend\Content;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Studio24\Frontend\Cms\Wordpress;
use Studio24\Frontend\Content\Yoast;
use Studio24\Frontend\ContentModel\ContentModel;

class YoastTest extends TestCase
{
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
            '<meta property="og:title" content="10 of your favourite Instagram posts"><meta property="og:description" content="As 2018 comes to a close, we look back at some of your favourite @FaunaFloraInt Instagram posts from the year..."><meta property="og:image" content="https://complex.demo/wp-content/uploads/2018/12/ten-of-your-favourite-instagram-posts-of-2018.png">',
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
        // Create a mock and queue responses
        $mock = new MockHandler([
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../responses/metatags/page.15.json')
            ),
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../responses/acf/users.1.json')
            )
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $wordpress = new Wordpress('something');
        $wordpress->setClient($client);
        $dir = __DIR__;
        $contentModel = new ContentModel(__DIR__ . '/config/content-model.yaml');
        $wordpress->setContentModel($contentModel);
        $wordpress->setContentType('page');

        $page = $wordpress->getPage(15);

        $this->assertEquals("About | Name of site", $page->getHead()->getTitle());

        $this->assertEquals("<meta name=\"twitter:title\" content=\"About | Name of site\">", $page->getHead()->getMetaHtml('twitter:title'));

        $this->assertSame("<meta name=\"description\" content=\"Focus is on protecting biodiversity, which underpins healthy ecosystems and is critical for the life support systems that all living things rely on.\">
<meta name=\"twitter:title\" content=\"About | Name of site\">
<meta name=\"twitter:description\" content=\"Focus is on protecting biodiversity, which underpins healthy ecosystems and is critical for the life support systems that all living things rely on.\">
<meta name=\"twitter:card\" content=\"summary_large_image\">
<meta property=\"og:title\" content=\"Facebook override title\">
<meta property=\"og:description\" content=\"Facebook override description\">
<meta property=\"og:image\" content=\"http://localhost/wp-content/uploads/2018/08/first-chance-to-seeor-last-spectacular-new-footage-of-vietnams-primates-3.png\">
", $page->getHead()->getAllMetaHtml());
    }
}
