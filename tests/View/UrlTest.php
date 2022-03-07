<?php

namespace View;

use PHPUnit\Framework\TestCase;
use Strata\Frontend\View\Url;

class UrlTest extends TestCase
{
    public function testSlugify()
    {
        $this->assertEquals('my-name-is-earl', Url::slugify('My name is Earl'));
        $this->assertEquals('changing-spaces-here-and-here-and-123456-789', Url::slugify('Changing spaces here "and here" and 123=456-789'));
        $this->assertEquals('a-longer-string-here-and-here', Url::slugify('A longer string here ' . PHP_EOL . ' and here'));
        $this->assertEquals('my-title', Url::slugify('<h1>My title</h1>'));
        $this->assertEquals('urljavascriptalertthe-secret-is-to-askwindowlocationreplacedo-something', Url::slugify('url"javascript:alert(\'the secret is to ask.\');window.location.replace(\'Do something\')'));
        $this->assertEquals('a-lot-of-spaces', Url::slugify('a lot    of         spaces'));
    }

    public function testFixUrl()
    {
        $this->assertEquals('https://www.domain.com', Url::fixUrl('//www.domain.com'));
        $this->assertEquals('http://www.domain.com', Url::fixUrl('//www.domain.com', 'http'));
        $this->assertEquals('https://domain.com', Url::fixUrl('domain.com'));
        $this->assertEquals('http://domain.com', Url::fixUrl('domain.com', 'http'));
        $this->assertEquals('https://www.domain.com', Url::fixUrl('www.domain.com'));
        $this->assertEquals('http://www.domain.com', Url::fixUrl('www.domain.com', 'http'));
        $this->assertEquals('https://domain.com', Url::fixUrl('domain.com'));
        $this->assertEquals('https://domain.co.uk', Url::fixUrl('domain.co.uk'));
        $this->assertEquals('https://domain.com/contact/', Url::fixUrl('domain.com/contact/'));
        $this->assertEquals('https://domain.com/team/bob', Url::fixUrl('domain.com/team/bob'));
        $this->assertEquals('https://domain.com:8080/about', Url::fixUrl('domain.com:8080/about'));
        $this->assertEquals('https://domain.com/search?k=maths', Url::fixUrl('domain.com/search?k=maths'));
        $this->assertEquals('https://domain.com/search?k=maths#3', Url::fixUrl('domain.com/search?k=maths#3'));
        $this->assertEquals('https://me:pass@staging.domain.com/', Url::fixUrl('https://me:pass@staging.domain.com/'));
        $this->assertEquals('/team/bob', Url::fixUrl('/team/bob'));
        $this->assertEquals('../contact', Url::fixUrl('../contact'));
        $this->assertEquals('fake url', Url::fixUrl('fake url'));
    }

    public function testTrailingSlash()
    {
        $this->assertSame('/news', Url::removeTrailingSlash('/news/'));
        $this->assertSame('/news', Url::removeTrailingSlash('/news'));
        $this->assertSame('/news/foo-bar-page-name', Url::removeTrailingSlash('/news/foo-bar-page-name/'));
        $this->assertSame('/news/foo-bar-page-name', Url::removeTrailingSlash('/news/foo-bar-page-name'));
        $this->assertSame('/news/2010/foo-bar-page-name', Url::removeTrailingSlash('/news/2010/foo-bar-page-name/'));

        $this->assertSame('/news/', Url::addTrailingSlash('/news/'));
        $this->assertSame('/news/', Url::addTrailingSlash('/news'));
        $this->assertSame('/news/foo-bar-page-name/', Url::addTrailingSlash('/news/foo-bar-page-name/'));
        $this->assertSame('/news/foo-bar-page-name/', Url::addTrailingSlash('/news/foo-bar-page-name'));
        $this->assertSame('/news/2010/foo-bar-page-name/', Url::addTrailingSlash('/news/2010/foo-bar-page-name/'));
    }

    public function testRelativeUrl()
    {
        $this->assertSame('/news/post-name', Url::relativeUrl('https://domain.com/news/post-name'));
        $this->assertSame('/news/post-name', Url::relativeUrl('/news/post-name'));
        $this->assertSame('news/post-name', Url::relativeUrl('news/post-name'));
        $this->assertSame('/news/post-name', Url::relativeUrl('https://domain.com:8000/news/post-name'));
        $this->assertSame('/news/post-name', Url::relativeUrl('https://username:password@domain.com/news/post-name'));
        $this->assertSame('/news/', Url::relativeUrl('/news/'));
        $this->assertSame('/news/?page=2', Url::relativeUrl('/news/?page=2'));
        $this->assertSame('/news/?page=2&tag=something', Url::relativeUrl('/news/?page=2&tag=something'));
        $this->assertSame('/news/?page=2&tag=something#bookmark', Url::relativeUrl('/news/?page=2&tag=something#bookmark'));
    }
}
