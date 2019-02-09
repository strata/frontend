<?php

namespace App\Tests\Frontend\Content;

use PHPUnit\Framework\TestCase;
use Studio24\Frontend\Content\Page;
use Studio24\Frontend\Content\Url;

class UrlTest extends TestCase
{

    public function testSetParam()
    {
        // @todo
    }

    public function testParseParamOptions()
    {
        // @todo
    }

    public function NEW_testGetUrl()
    {
        // @todo
        $content = new Page();
        $content->setUrlSlug('test-page');
        $content->setDatePublished('2019-02-12');

        $url = new Url('news/:slug');
        $this->assertEquals('news/test-page', $url->getUrl($content));

        $url = new Url('news/:date_published/:slug');
        $this->assertEquals('news/2019/02/12/test-page', $url->getUrl($content));

        $url = new Url('news/:date_published(format=Y)/:slug');
        $this->assertEquals('news/2019/test-page', $url->getUrl($content));
    }

}
