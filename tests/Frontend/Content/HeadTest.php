<?php
/**
 * Created by PhpStorm.
 * User: bdeboevere
 * Date: 2019-03-11
 * Time: 14:54
 */

namespace App\Tests\Frontend\Content;

use PHPUnit\Framework\TestCase;
use Studio24\Frontend\Content\Head;

class HeadTest extends TestCase
{
    /**
     * @var Head $test_head
     */
    protected $test_head;

    public function setUp()
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

        $og_title = $head->addMeta("og:title", "some title for opengraph");
        $non_existing = $head->addMeta("non-existing", "This should fail");

        $this->assertTrue($og_title);
        $this->assertFalse($non_existing);
    }

    public function testGetTitle()
    {
        $this->assertSame("This is the head used in the test", $this->test_head->getTitle());
    }

    public function testGetMeta()
    {
        $head = clone $this->test_head;

        $this->assertTrue($head->addMeta("og:title", "some title for opengraph"));
        $this->assertFalse($head->addMeta("non-existing", "This should fail"));

        $this->assertSame("some title for opengraph", $head->getMeta("og:title"));
        $this->assertNull($head->getMeta("non-existing"));
    }

    public function testGetMetaHtml()
    {
        $head = clone $this->test_head;
        $this->assertTrue($head->addMeta("og:title", "some title for opengraph"));
        $html = $head->getMetaHtml("og:title");

        $this->assertSame("<meta name=\"og:title\" content=\"some title for opengraph\">", $html);
        $this->assertNull($head->getMetaHtml("non-existing"));
    }

    public function testAddManyTags()
    {
        $head = clone $this->test_head;

        $head->addMeta("og:description", "description");
        $head->addMeta("og:image", "image");
        $head->addMeta("og:title", "title");
        $head->addMeta("twitter:description", "description");
        $head->addMeta("twitter:image", "description");
        $head->addMeta("twitter:title", "description");

        $head->addMeta("keywords", "test,php,fontend,wordpress");
        $head->addMeta("robots", "noindex, nofollow");
        $head->addMeta("description", "Hello test");

        $this->assertSame("<meta name=\"og:description\" content=\"description\">
<meta name=\"og:image\" content=\"image\">
<meta name=\"og:title\" content=\"title\">
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
