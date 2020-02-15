<?php

declare(strict_types=1);

namespace App\Tests\Frontend\Content\Field;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Strata\Frontend\Cms\Wordpress;
use Strata\Frontend\ContentModel\ContentModel;

class RelationTest extends TestCase
{
    public function testRelation()
    {
        // Create a mock and queue response
        $mock = new MockHandler([
            new Response(
                200,
                ['X-WP-Total' => 1, 'X-WP-TotalPages' => 1],
                file_get_contents(__DIR__ . '/../../responses/acf/page.10.json')
            ),
            new Response(
                200,
                ['X-WP-Total' => 1, 'X-WP-TotalPages' => 1],
                file_get_contents(__DIR__ . '/../../responses/acf/users.1.json')
            ),
            new Response(
                200,
                ['X-WP-Total' => 1, 'X-WP-TotalPages' => 1],
                file_get_contents(__DIR__ . '/../../responses/acf/users.195.json')
            ),
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        $api = new Wordpress('something');
        $api->setClient($client);
        $contentModel = new ContentModel(__DIR__ . '/../config/content-model.yaml');
        $api->setContentModel($contentModel);
        $api->setContentType('page2');

        // Test it!
        $page = $api->getPage(10);

        $related_post = $page->getContent()->get('related_post');

        $this->assertInstanceOf('Strata\Frontend\Content\Field\Relation', $related_post);

        $this->assertEquals('careers', $related_post->getContentType());
        $this->assertEquals('Joe Bloggs', $related_post->getContent()->getAuthor()->getName());
    }
}
