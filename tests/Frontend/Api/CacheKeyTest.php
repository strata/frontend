<?php
declare(strict_types=1);

namespace App\Tests\Frontend\Api\Providers;

use PHPUnit\Framework\TestCase;
use Studio24\Frontend\Cms\RestData;

class CacheKeyTest extends TestCase
{
    public function testCacheKey()
    {
        $api = new RestData('something');
        $nullVar = null;
        $boolVar = true;

        $this->assertEquals('search.news', $api->buildCacheKey('search', 'news'));
        $this->assertEquals('news.list.123', $api->buildCacheKey('news', 'list', 123));
        $this->assertEquals('news.list.NULL', $api->buildCacheKey('news', 'list', $nullVar));
        $this->assertEquals('news.list.true', $api->buildCacheKey('news', 'list', $boolVar));
        $this->assertEquals('news.list.12.99', $api->buildCacheKey('news', 'list', 12.99));
        $this->assertEquals('news.list.123.page=1.category=thing', $api->buildCacheKey('news', 'list', '123', ['page' => 1, 'category' => 'thing']));
        $this->assertEquals('cache', $api->buildCacheKey(''));

        $this->assertEquals('search.newstime', $api->buildCacheKey('search', 'news:time'));
        $this->assertEquals('search.news', $api->buildCacheKey('search', '{news}'));
        $this->assertEquals('demoexample.com.test.123', $api->buildCacheKey('demo@example.com', 'test', 123));
    }

    public function testInvalidArray()
    {
        $api = new RestData('something');

        $this->expectException('Studio24\Frontend\Exception\ApiException');
        $api->buildCacheKey('list', '123', ['page' => 1, 'key' => [0, 1, 2]]);
    }

    public function testInvalidParam()
    {
        $api = new RestData('something');

        $this->expectException('Studio24\Frontend\Exception\ApiException');
        $api->buildCacheKey('thing1', 'thing2', $api);
    }
}
