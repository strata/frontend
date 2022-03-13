<?php

declare(strict_types=1);

namespace Test\Response;

use FOS\HttpCache\ResponseTagger;
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

class Psr7ResponseHelperTest extends TestCase
{
    private Psr7ResponseHelper $helper;

    public function testCacheHeaders()
    {
        $helper = new Psr7ResponseHelper();
        $response = new Response();

        $helper->cacheControl();
        $response = $helper->apply($response);
        $this->assertContains('public, must-revalidate, max-age=86400', $response->getHeader('Cache-Control'));

        $helper->doNotCache();
        $response = $helper->apply($response);
        $this->assertContains('private, no-store, no-cache', $response->getHeader('Cache-Control'));

        $helper->cacheControl(CacheLifetime::HOUR);
        $response = $helper->apply($response);
        $this->assertContains('public, must-revalidate, max-age=3600', $response->getHeader('Cache-Control'));
    }

    public function testSecurityHeaders()
    {
        $helper = new Psr7ResponseHelper();
        $response = new Response();

        $helper->setFrameOptions()
               ->setContentTypeOptions()
               ->setReferrerPolicy();
        $response = $helper->apply($response);

        $this->assertContains('deny', $response->getHeader('X-Frame-Options'));
        $this->assertContains('nosniff', $response->getHeader('X-Content-Type-Options'));
        $this->assertContains('same-origin', $response->getHeader('Referrer-Policy'));
    }

    public function testInvalidOptions()
    {
        $helper = new Psr7ResponseHelper();

        $this->expectException(InvalidResponseHeaderValueException::class);
        $helper->setFrameOptions('invalidorigin');
    }

    public function testResponseTagger()
    {
        $helper = new Psr7ResponseHelper();
        $response = new Response();
        $responseTagger = new ResponseTagger();

        $responseTagger->addTags(['global','test1']);
        $responseTagger->addTags(['test2']);
        $helper->setHeadersFromResponseTagger($responseTagger);
        $response = $helper->apply($response);

        $this->assertContains('global,test1,test2', $response->getHeader('X-Cache-Tags'));
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

        $helper = new Psr7ResponseHelper();
        $response = new Response();
        $responseTagger = new ResponseTagger();

        $responseTagger = $helper->applyResponseTagsFromQuery($responseTagger, $manager);
        $this->assertNotContains('test1,test2,test3,test4', $response->getHeader('X-Cache-Tags'));

        $helper->setHeadersFromResponseTagger($responseTagger);
        $response = $helper->apply($response);
        $this->assertContains('test1,test2,test3,test4', $response->getHeader('X-Cache-Tags'));

        $query = new Query();
        $query->setUri('test1')->cacheTags(['test5', 'test6']);
        $manager->add('query3', $query);
        $query = new Query();
        $query->setUri('test1')->cacheTags(['test7', 'test8'])->concurrent(false);
        $manager->add('query4', $query);
        $data = $manager->get('query3');

        $helper->applyResponseTagsFromQuery($responseTagger, $manager, true);
        $response = $helper->apply($response);
        $this->assertContains('test1,test2,test3,test4,test5,test6', $response->getHeader('X-Cache-Tags'));
    }
}
