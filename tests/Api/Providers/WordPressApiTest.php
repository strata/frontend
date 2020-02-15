<?php

declare(strict_types=1);

namespace App\Tests\Frontend\Api\Providers;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Studio24\Frontend\Api\Providers\Wordpress;

class WordPressApiTest extends TestCase
{
    public function testPosts()
    {
        // Create a mock and queue two responses
        $mock = new MockHandler([
            new Response(
                200,
                ['X-WP-Total' => 12, 'X-WP-TotalPages' => 2],
                file_get_contents(__DIR__ . '/../../responses/demo/posts_1.json')
            ),
            new Response(
                200,
                ['X-WP-Total' => 12, 'X-WP-TotalPages' => 2],
                file_get_contents(__DIR__ . '/../../responses/demo/posts_2.json')
            ),
            new Response(
                200,
                ['X-WP-Total' => 3, 'X-WP-TotalPages' => 1],
                file_get_contents(__DIR__ . '/../../responses/taxonomy/taxonomy.framework_type.json')
            ),
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../../responses/taxonomy/taxonomy.framework_type.25.json')
            ),
        ]);

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
