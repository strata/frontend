<?php

declare(strict_types=1);

namespace App\Tests\Frontend\Api\Providers;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Strata\Data\Http\Response\MockResponseFromFile;
use Strata\Frontend\Api\Providers\Wordpress;
use Symfony\Component\HttpClient\MockHttpClient;

class WordPressApiTest extends TestCase
{
    public function testPosts()
    {
        // Create mock HTTP responses
        $responses = [
            new MockResponseFromFile(__DIR__ . '/wordpress/posts_1.json'),
            new MockResponseFromFile(__DIR__ . '/wordpress/posts_2.json'),
            new MockResponseFromFile(__DIR__ . '/wordpress/taxonomy.framework_type.json'),
            new MockResponseFromFile(__DIR__ . '/wordpress/taxonomy.framework_type.25.json'),
        ];
        $client = new MockHttpClient($responses);

        // @todo switch WordPress to use Strata Data


        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $api = new Wordpress('http://demo.wp-api.org/wp-json/wp/v2/');
        $api->setClient($client);

        // Test it!
        $posts = $api->listPosts('posts');
        $this->assertEquals(1, $posts->getPagination()->getPage());
        $this->assertEquals(12, $posts->getPagination()->getTotalResults());
        $this->assertEquals(2, $posts->getPagination()->getTotalPages());

        $data = $posts->getResponseData();
        $this->assertEquals(1, $data[0]['id']);
        $this->assertTrue(!empty($data[0]['title']['rendered']));

        $posts = $api->listPosts('posts', 2);
        $this->assertEquals(2, $posts->getPagination()->getPage());

        $data = $posts->getResponseData();
        $this->assertEquals(5, $data[0]['id']);
        $this->assertTrue(!empty($data[0]['title']['rendered']));

        $tax_terms_data = $api->getTaxonomyTerms('framework_type');
        $this->assertEquals('dps', $tax_terms_data[0]['slug']);
        $this->assertEquals('http://localhost/framework_type/g-cloud/', $tax_terms_data[1]['link']);
        $this->assertEquals(25, $tax_terms_data[2]['id']);

        $term_data = $api->getTerm('framework_type', 25);
        $this->assertEquals('Standard', $term_data['name']);
        $this->assertEquals('standard', $term_data['slug']);
        $this->assertEquals('http://localhost/framework_type/standard/', $term_data['link']);
        $this->assertEquals(25, $term_data['id']);
    }

    public function testFailedResponses()
    {
        // Create a mock and queue two responses
        $mock = new MockHandler([
            new Response(
                401,
                [],
                '{"code":"rest_forbidden","message":"Sorry, you are not allowed to do that.","data":{"status":401}}'
            ),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $api = new Wordpress('somewhere');
        $api->setClient($client);

        // Test it!
        $results = $api->getMedia(1000);
        $this->assertEmpty($results);
    }
}
