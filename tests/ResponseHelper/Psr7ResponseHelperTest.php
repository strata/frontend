<?php

declare(strict_types=1);

namespace Test\Response;

use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Http\Rest;
use Strata\Data\Query\Query;
use Strata\Data\Query\QueryManager;
use Strata\Frontend\Exception\InvalidResponseHeaderValueException;
use Strata\Frontend\ResponseHelper\Psr7ResponseHelper;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class Psr7ResponseHelperTest extends TestCase {

    private Psr7ResponseHelper $helper;

    public function setup(): void
    {
        $response = new Response();
        $this->helper = new Psr7ResponseHelper($response);
    }

    public function testCacheHeaders()
    {
        $this->helper->cacheControl();
        $this->assertContains('public, must-revalidate, max-age=86400', $this->helper->getResponse()->getHeader('Cache-Control'));

        $this->helper->doNotCache();
        $this->assertContains('private, no-store, no-cache', $this->helper->getResponse()->getHeader('Cache-Control'));

        $this->helper->cacheControl(CacheLifetime::HOUR);
        $this->assertContains('public, must-revalidate, max-age=3600', $this->helper->getResponse()->getHeader('Cache-Control'));
    }

    public function testSecurityHeaders()
    {
        $this->helper->setFrameOptions();
        $this->assertContains('deny', $this->helper->getResponse()->getHeader('X-Frame-Options'));

        $this->helper->setContentTypeOptions();
        $this->assertContains('nosniff', $this->helper->getResponse()->getHeader('X-Content-Type-Options'));

        $this->helper->setReferrerPolicy();
        $this->assertContains('same-origin', $this->helper->getResponse()->getHeader('Referrer-Policy'));
    }

    public function testInvalidOptions()
    {
        $this->expectException(InvalidResponseHeaderValueException::class);
        $this->helper->setFrameOptions('invalidorigin');
    }

    public function testResponseTagger()
    {
        $this->helper->getResponseTagger()->addTags(['global','test1']);
        $this->helper->getResponseTagger()->addTags(['test2']);
        $this->helper->setHeadersFromResponseTagger();

        $this->assertContains('global,test1,test2', $this->helper->getResponse()->getHeader('X-Cache-Tags'));
    }

    public function testResponseTaggerFromQueryManager()
    {
        // Create a bunch of mock responses
        $responses = array_fill(0, 4, new MockResponse('{"message": "OK"}'));

        $manager = new QueryManager();
        $rest = new Rest('https://example.com/');
        $rest->setHttpClient(new MockHttpClient($responses));
        $manager->addDataProvider('test1', $rest);

        $query = new Query();
        $query->setUri('test1')->cacheTags(['test1', 'test2']);
        $manager->add('query1', $query);
        $query = new Query();
        $query->setUri('test2')->cacheTags(['test3', 'test4']);
        $manager->add('query2', $query);
        $data = $manager->get('query1');

        $this->helper->addTagsFromQueryManager($manager);
        $this->assertContains('test1,test2,test3,test4', $this->helper->getResponse()->getHeader('X-Cache-Tags'));

        $query = new Query();
        $query->setUri('test1')->cacheTags(['test5', 'test6']);
        $manager->add('query3', $query);
        $query = new Query();
        $query->setUri('test1')->cacheTags(['test7', 'test8'])->concurrent(false);
        $manager->add('query4', $query);
        $data = $manager->get('query3');

        $this->helper->addTagsFromQueryManager($manager);
        $this->assertContains('test1,test2,test3,test4,test5,test6', $this->helper->getResponse()->getHeader('X-Cache-Tags'));
    }

}
