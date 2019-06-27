<?php

namespace App\Tests\Frontend\Utils;

use PHPUnit\Framework\TestCase;
use Studio24\Frontend\Utils\Redirects;

class RedirectsTest extends TestCase
{

    public function testRegex()
    {
        $redirects = new Redirects();

        $this->assertEquals('!^/news/(.+)$!', $redirects->regex('/news/*'));
        $this->assertEquals('!^/conservation/(.+)/test$!', $redirects->regex('/conservation/*/test'));
        $this->assertEquals('!^/my\-news/(.+)$!', $redirects->regex('/my-news/*'));
    }

    public function testReplace()
    {
        $redirects = new Redirects();

        $this->assertEquals('/news/1234', $redirects->replace('/news/*', [1234]));
        $this->assertEquals('/news/', $redirects->replace('/news/*', []));
        $this->assertEquals('/news/1234/foo/5678', $redirects->replace('/news/*/foo/*', [1234, 5678]));
        $this->assertEquals('/news/1234/foo/', $redirects->replace('/news/*/foo/*', [1234]));
        $this->assertEquals('/news/1234/foo/5678/bar/9123', $redirects->replace('/news/*/foo/*/bar/*', [1234, 5678, 9123]));
        $this->assertEquals('/news/1234/foo/5678/bar/', $redirects->replace('/news/*/foo/*/bar/*', [1234, 5678]));
        $this->assertEquals('1234/news', $redirects->replace('*/news', [1234]));
        $this->assertEquals('news', $redirects->replace('news', []));
    }

    public function testRedirects()
    {
        $redirects = new Redirects();

        $redirects->addRedirect('/support/shop', '/support');
        $redirects->addRedirect('/posts/*', '/news/*');
        $redirects->addRedirect('/wp-content/uploads/*', 'https://domain.com/wp-content/uploads/old-images/*');
        $redirects->addRedirect('/about/team', '/team', 302);
        $redirects->addRedirect('*/test', '/test/*');

        $this->assertTrue($redirects->match('/support/shop'));
        $this->assertEquals('/support', $redirects->getLastDestination());
        $this->assertEquals(301, $redirects->getLastHttpStatus());

        $this->assertFalse($redirects->match('/foo/bar'));
        $this->assertFalse($redirects->match('/posts'));
        $this->assertFalse($redirects->match('/posts/'));

        $this->assertTrue($redirects->match('/about/team'));
        $this->assertEquals('/team', $redirects->getLastDestination());
        $this->assertEquals(302, $redirects->getLastHttpStatus());

        $this->assertTrue($redirects->match('/posts/12345'));
        $this->assertEquals('/news/12345', $redirects->getLastDestination());
        $this->assertEquals(301, $redirects->getLastHttpStatus());

        $this->assertTrue($redirects->match('123/test'));
        $this->assertEquals('/test/123', $redirects->getLastDestination());
    }

    public function testCsvFile()
    {
        $redirects = new Redirects();

        $redirects->loadFromCsv(__DIR__ . '/redirects-test.csv');

        $this->assertEquals(10, count($redirects->getRedirects()));

        $this->assertTrue($redirects->match('/support/shop'));
        $this->assertEquals('/shop', $redirects->getLastDestination());
        $this->assertEquals(301, $redirects->getLastHttpStatus());

        $this->assertFalse($redirects->match('/posts/12345'));
        $this->assertFalse($redirects->match('/about/team'));

        $this->assertTrue($redirects->match('/support/membership'));
        $this->assertEquals('/membership', $redirects->getLastDestination());
        $this->assertEquals(302, $redirects->getLastHttpStatus());

        $this->assertTrue($redirects->match('/conservation/12345'));
        $this->assertEquals('/conservation-challenges/12345', $redirects->getLastDestination());

        $this->assertTrue($redirects->match('/conservation/foo/bar'));
        $this->assertEquals('/conservation-challenges/foo/bar', $redirects->getLastDestination());

        $this->assertTrue($redirects->match('/why-do-we-work-with-big-business'));
        $this->assertEquals('/why-we-work-with-big-business', $redirects->getLastDestination());

        $this->assertTrue($redirects->match('/marine-plastics'));
        $this->assertEquals('/conservation-challenges/ocean-plastic-pollution?utm_medium=social&utm_source=twitter.com&utm_campaign=marine+plastics', $redirects->getLastDestination());

        $this->assertTrue($redirects->match('/donation?amount=10&currency=USD&type=donation&frequency=single&reason=default'));
        $this->assertEquals('/donation?amount=10&currency=USD&type=donation&frequency=single&reason=testing123&source=ACME', $redirects->getLastDestination());
    }
}
