<?php
declare(strict_types=1);

namespace App\Tests\Frontend\Content\Field;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Studio24\Frontend\Cms\Wordpress;
use Studio24\Frontend\ContentModel\ContentModel;

class DecimalTest extends TestCase
{
    public function testDecimal()
    {
        // Create a mock and queue two responses
        $mock = new MockHandler([
            new Response(
                200,
                ['X-WP-Total' => 1, 'X-WP-TotalPages' => 1],
                file_get_contents(__DIR__ . '/../../responses/content/field/post.1.json')
            ),
            new Response(
                200,
                ['X-WP-Total' => 1, 'X-WP-TotalPages' => 1],
                file_get_contents(__DIR__ . '/../../responses/content/field/users.1.json')
            )
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $api = new Wordpress('http://demo.wp-api.org/wp-json/wp/v2/');
        $api->setClient($client);
        $contentModel = new ContentModel(__DIR__ . '/../config/content-model.yaml');
        $api->setContentModel($contentModel);
        $api->setContentType('project');

        $project = $api->getPageByUrl('/2019/05/23/hello-world/');

        $this->assertIsFloat($project->getContent()->get('price')->getValue());
        $this->assertEquals(19.99, $project->getContent()->get('price')->getValue());
        $this->assertEquals(15.50, $project->getContent()->get('price_round')->getValue());
    }
}
