<?php
declare(strict_types=1);

namespace App\Tests\Frontend\Content;

use PHPUnit\Framework\TestCase;
use Studio24\Frontend\Content\Page;
use Studio24\Frontend\Content\Url;

class UrlTest extends TestCase
{

    public function testValidParam()
    {
        $url = new Url();
        $this->assertTrue($url->validParam('id'));
        $this->assertTrue($url->validParam('slug'));
        $this->assertTrue($url->validParam('date_published'));
        $this->assertTrue($url->validParam('date_modified'));
        $this->assertFalse($url->validParam('author'));
        $this->assertFalse($url->validParam('post_id'));
        $this->assertFalse($url->validParam('armadillo'));
    }

    public function testSetParam()
    {
        $url = new Url();
        $url->setParam('id', ':id');
        $this->assertEquals(':id', $url->getReplace('id'));
        $this->assertTrue($url->hasParam('id'));
        $this->assertFalse($url->hasParam('slug'));

        $url->setParam('slug', ':slug(testing)', ['key1' => 'value1', 'key2' => 'value2']);
        $this->assertTrue($url->hasParam('slug'));
        $this->assertTrue($url->hasParam('id'));
        $this->assertFalse($url->hasParam('date_published'));
        $this->assertEquals(':slug(testing)', $url->getReplace('slug'));
        $this->assertEquals(null, $url->getReplace('date_published'));

        $params = $url->getParams();
        $this->assertTrue(array_key_exists('id', $params));
        $this->assertTrue(array_key_exists('slug', $params));
    }

    public function testGetOptions()
    {
        $url = new Url();
        $url->setParam('id', ':id');
        $this->assertEquals([], $url->getOptions('id'));

        $url->setParam('slug', ':slug', ['key1' => 'value1', 'key2' => 'value2']);
        $this->assertEquals('value1', $url->getOption('slug', 'key1'));
        $this->assertEquals('value2', $url->getOption('slug', 'key2'));

        $this->assertEquals([], $url->getOptions('date_published'));
        $this->assertEquals(null, $url->getOption('date_published', 'key'));
        $this->assertEquals(null, $url->getOption('slug', 'key5'));

        $this->expectException('Studio24\Frontend\Exception\UrlException');
        $url->getOption('armadillo', 'key1');
    }

    public function testParseParamOptions()
    {
        $url = new Url();

        $options = $url->parseParamOptions("(format=Y)");
        $this->assertEquals('Y', $options['format']);

        $options = $url->parseParamOptions('(key1=value1)');
        $this->assertEquals('value1', $options['key1']);

        $options = $url->parseParamOptions('(key1=value1,key2=value2)');
        $this->assertEquals('value1', $options['key1']);
        $this->assertEquals('value2', $options['key2']);

        $options = $url->parseParamOptions('format=Y-m,key2=value2,test=test@example.com');
        $this->assertEquals('Y-m', $options['format']);
        $this->assertEquals('test@example.com', $options['test']);
    }

    public function testParseParamValue()
    {
        $url = new Url();
        $url->setParam('id', ':id');
        $content = new Page();
        $content->setId(123);
        $this->assertEquals('news/123', $url->parseParamValue('news/:id', $content, 'id'));
    }

    public function testSetPattern()
    {
        $url = new Url();
        $url->setPattern('news/:id');
        $this->assertTrue($url->hasParam('id'));

        $url->setPattern('news/:slug');
        $this->assertTrue($url->hasParam('slug'));
        $this->assertFalse($url->hasParam('id'));

        $url->setPattern('news/:date_published');
        $this->assertTrue($url->hasParam('date_published'));
        $this->assertFalse($url->hasParam('slug'));
        $this->assertFalse($url->hasParam('id'));

        $url->setPattern('news/:date_published(format=Y/m)');
        $this->assertTrue($url->hasParam('date_published'));
        $this->assertEquals('Y/m', $url->getOption('date_published', 'format'));

        $url->setPattern('news/:date_modified(format=Y)');
        $this->assertTrue($url->hasParam('date_modified'));
        $this->assertFalse($url->hasParam('date_published'));
        $this->assertEquals('Y', $url->getOption('date_modified', 'format'));
    }

    public function testGetUrl()
    {
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
