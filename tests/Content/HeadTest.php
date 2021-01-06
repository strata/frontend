<?php

declare(strict_types=1);

namespace App\Tests\Frontend\Content;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Strata\Frontend\Cms\Wordpress;
use Strata\Frontend\Content\Head;
use Strata\Frontend\ContentModel\ContentModel;
use Strata\Frontend\Exception\MetaTagNotAllowedException;

class HeadTest extends TestCase
{
    /**
     * @var Head $test_head
     */
    private $test_head;

    public function setUp(): void
    {
        $this->test_head = new Head();
        $this->test_head->setTitle("This is the head used in the test");
    }

    public function testHeadClass()
    {
        $head = new Head();
        $this->assertInstanceOf(Head::class, $head);
    }

    public function testAllowedMeta()
    {
        $head = new Head();

        $head->addMeta("og:title", "some title for opengraph");
        $head->addMeta("og:image", "some image for opengraph");
        $head->addMeta("og:description", "some description for opengraph");

        $this->assertTrue(true);
    }

    public function testAddMetaException()
    {
        $this->expectException(MetaTagNotAllowedException::class);
        $head = clone $this->test_head;
        $head->addMeta("non-existing", "This should fail");
    }

    public function testGetTitle()
    {
        $this->assertSame("This is the head used in the test", $this->test_head->getTitle());
    }

    public function testGetMeta()
    {
        $head = clone $this->test_head;

        $head->addMeta("og:title", "some title for opengraph");

        $this->assertSame("some title for opengraph", $head->getMeta("og:title"));
        $this->assertNull($head->getMeta("non-existing"));
    }

    public function testGetMetaHtml()
    {
        $head = clone $this->test_head;
        $head->addMeta("og:title", "some title for opengraph");
        $html = $head->getMetaHtml("og:title");

        $this->assertSame("<meta property=\"og:title\" content=\"some title for opengraph\">", $html);
        $this->assertNull($head->getMetaHtml("non-existing"));
    }

    public function testAddManyTags()
    {
        $head = clone $this->test_head;

        $head->addMeta("og:description", "description");
        $head->addMeta("og:image", "image");
        $head->addMeta("og:title", "title");
        $head->addMeta("twitter:card", "summary");
        $head->addMeta("twitter:description", "description");
        $head->addMeta("twitter:image", "description");
        $head->addMeta("twitter:title", "description");

        $head->addMeta("keywords", "test,php,fontend,wordpress");
        $head->addMeta("robots", "noindex, nofollow");
        $head->addMeta("description", "Hello test");

        $this->assertSame("<meta property=\"og:description\" content=\"description\">
<meta property=\"og:image\" content=\"image\">
<meta property=\"og:title\" content=\"title\">
<meta name=\"twitter:card\" content=\"summary\">
<meta name=\"twitter:description\" content=\"description\">
<meta name=\"twitter:image\" content=\"description\">
<meta name=\"twitter:title\" content=\"description\">
<meta name=\"keywords\" content=\"test,php,fontend,wordpress\">
<meta name=\"robots\" content=\"noindex, nofollow\">
<meta name=\"description\" content=\"Hello test\">
", $head->getAllMetaHtml());

        $this->assertSame($head->__toString(), $head->getAllMetaHtml());
    }
}
