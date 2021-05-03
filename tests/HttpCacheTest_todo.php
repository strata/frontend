<?php

declare(strict_types=1);

namespace Strata\Data\Tests;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use PHPUnit\Framework\TestCase;
use Strata\Data\Http\RestApi;
use Strata\Data\Populate_DELETE\ArrayStrategy;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\FilesystemTagAwareAdapter;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Toflar\Psr6HttpCacheStore\Psr6StoreInterface;

class HttpCacheTestTodo extends TestCase
{
    const CACHE_DIR = __DIR__ . '/cache';
    const CACHE_NAMESPACE = 'strata_data';

    /**
     * This method is called after each test.
     *
     * Delete cache files
     */
    protected function tearDown(): void
    {
        $adapter = new LocalFilesystemAdapter(self::CACHE_DIR);
        $filesystem = new Filesystem($adapter);
        //$filesystem->deleteDirectory(self::CACHE_NAMESPACE);
    }

    public function testCache()
    {
        $responses = [
            new MockResponse('OK 1'),
            new MockResponse('OK 2'),
        ];
        $api = new RestApi('https://example.com/api/');
        $api->setHttpClient(new MockHttpClient($responses));
        $api->setCache(new FilesystemTagAwareAdapter(self::CACHE_NAMESPACE, 3600, self::CACHE_DIR));
        $api->enableCache()
            ->setCacheLifetime(30);

        $this->assertEquals('OK 1', $api->get('test-1')->getContent());
        $this->assertEquals('OK 1', $api->get('test-1')->getContent());
        sleep(2);
        $this->assertEquals('OK 2', $api->get('test-1')->getContent());
    }

    public function testInvalidCacheTagsAdapter()
    {
        $this->expectException('Strata\Data\Exception\CacheException');

        $api = new RestApi('https://example.com/api/');
        $api->setCache(new FilesystemAdapter(self::CACHE_NAMESPACE, 3600, self::CACHE_DIR));
        $api->setCacheTags(['test']);
    }

    public function testTags()
    {
        $responseHeaders = [
            'response_headers' => [
                'Cache-Control: public, max-age=2',
                'Cache-Tags: my-tag'
            ]
        ];
        $responses = [
            new MockResponse('OK 1', $responseHeaders),
            new MockResponse('OK 2', $responseHeaders),
            new MockResponse('OK 3', $responseHeaders),
            new MockResponse('OK 4', $responseHeaders),
        ];
        $api = new RestApi('https://example.com/api/');
        $api->setHttpClient(new MockHttpClient($responses));
        $api->enableHttpCache(self::CACHE_DIR);

        // test cache tags & tag invalidation
        $this->assertEquals('OK 1', $api->get('test-1')->getContent());
        $response = $api->get('test-1');
        $this->assertEquals('OK 1', $response->getContent());
        $this->assertTrue($this->isCacheTagInResponse('my-tag', $response));

        /** @var Psr6StoreInterface $cache */
        $cache = $api->getHttpClient()->getCache();
        $cache->invalidateTags(['my-tag']);
        $this->assertEquals('OK 2', $api->get('test-1')->getContent());

        // test setting a custom tag on a response (to aid invalidation)

        /** @var CachingHttpClient */
        $api->getHttpClient()->addTag('custom-tag');
        $response = $api->get('test-2');

        $this->assertEquals('OK 3', $response->getContent());
        $this->assertTrue($this->isCacheTagInResponse('custom-tag', $response));
        $this->assertTrue($this->isCacheTagInResponse('my-tag', $response));
    }

    public function testDifferentTags()
    {
        $responseHeaders = [
            'response_headers' => [
                'Cache-Control: public, max-age=2',
                'Cache-Tags: my-tag'
            ]
        ];
        $responses = [
            new MockResponse('OK 1', $responseHeaders),
            new MockResponse('OK 2', $responseHeaders),
            new MockResponse('OK 3', $responseHeaders),
            new MockResponse('OK 4', $responseHeaders),
        ];
        $api = new RestApi('https://example.com/api/');
        $api->setHttpClient(new MockHttpClient($responses));
        $api->enableHttpCache(self::CACHE_DIR);

        // test cache tags & tag invalidation
        $api->getHttpClient()->addTag('custom-tag');
        $response = $api->get('test-1');
        $this->assertTrue($this->isCacheTagInResponse('my-tag', $response));
        $this->assertTrue($this->isCacheTagInResponse('custom-tag', $response));

        $api->getHttpClient()->addTag('second-tag');
        $response = $api->get('test-2');
        $this->assertTrue($this->isCacheTagInResponse('custom-tag', $response));
        $this->assertTrue($this->isCacheTagInResponse('second-tag', $response));

        $api->getHttpClient()->setTags([]);
        $response = $api->get('test-3');
        $this->assertFalse($this->isCacheTagInResponse('custom-tag', $response));
        $this->assertFalse($this->isCacheTagInResponse('second-tag', $response));
        $this->assertTrue($this->isCacheTagInResponse('my-tag', $response));
    }

    /**
     * Whether a cache tag exists in the Cache-Tags response header string
     *
     * @param string $tag
     * @param ResponseInterface $response
     * @return bool
     */
    public function isCacheTagInResponse(string $tag, ResponseInterface $response): bool
    {
        $headers = $response->getHeaders();
        if (!isset($headers['cache-tags'])) {
            return false;
        }
        $tags = explode(',', $headers['cache-tags'][0]);
        return in_array($tag, $tags);
    }

    public function testLiveRequest()
    {
        $api = new RestApi('http://httpbin.org/');
        $api->setCache(new FilesystemAdapter('cache', 0, self::CACHE_DIR));
        $api->enableCache(3600);
        $api->getCache()->clear(); // remove

        $contents1 = $api->get('uuid')->toArray();
        $contents2 = $api->get('uuid')->toArray();
        $this->assertEquals($contents1, $contents2);

        $api->disableCache();
        $contents2 = $api->get('uuid')->toArray();
        $this->assertNotEquals($contents1, $contents2);
    }
}
