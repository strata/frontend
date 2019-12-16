<?php
declare(strict_types=1);

namespace App\Tests\Frontend\Cms\Content;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Studio24\Frontend\Cms\RestData;
use Studio24\Frontend\Cms\Wordpress;
use Studio24\Frontend\Content\Field\PlainArray;
use Studio24\Frontend\Content\Url;
use Studio24\Frontend\ContentModel\ContentModel;

class CustomApiTest extends TestCase
{
    public function testCustomRestApi()
    {
        // Create a mock and queue responses
        $mock = new MockHandler([
            new Response(
                200,
                ['X-WP-Total' => 12, 'X-WP-TotalPages' => 2],
                file_get_contents(__DIR__ . '/../responses/custom/projects.json')
            ),
            new Response(
                200,
                ['X-WP-Total' => 12, 'X-WP-TotalPages' => 2],
                file_get_contents(__DIR__ . '/../responses/custom/project.1.json')
            ),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $api = new RestData('something', new ContentModel(__DIR__ . '/config/custom/content_model.yaml'));
        $api->setClient($client);

        // Test it!
        $api->setContentType('projects');
        $projects = $api->list();

        $this->assertEquals(1, $projects->getPagination()->getPage());
        $this->assertEquals("Some name here", $projects->getMetadata()->offsetGet('my_title'));

        $x = 0;
        foreach ($projects as $item) {
            switch ($x) {
                case 0:
                    $this->assertEquals(1, $item->getContent()->get('id')->getValue());
                    $this->assertEquals('1', $item->getContent()->get('id'));
                    $this->assertEquals("Professional Economist Apprenticeship", $item->getContent()->get('title'));
                    $this->assertEquals("<p>Lorem ipsum text here</p>", $item->getContent()->get('description'));
                    $this->assertEquals("2018-07-11", $item->getContent()->get('open_date'));
                    $this->assertEquals("2019-02-01", $item->getContent()->get('close_date'));

                    // Unset field
                    $this->assertNull($item->getContent()->get('summary'));

                    $updates =  $item->getContent()->get('updates');
                    $y=0;
                    foreach ($updates as $update) {
                        switch ($y) {
                            case 0:
                                $this->assertEquals("An update 1A", $update->get('update_title')->__toString());
                                $this->assertEquals("Some more text here and here", $update->get('update_description')->__toString());
                                break;
                            case 1:
                                $this->assertEquals("An update 2A", $update->get('update_title')->__toString());
                                $this->assertEquals("Some more text here and there", $update->get('update_description')->__toString());
                                break;
                        }
                        $y++;
                    }
                    break;
                case 1:
                    $this->assertEquals(2, $item->getContent()->get('id')->getValue());
                    break;
            }
            $x++;
        }

        $project = $api->getOne(1);
        $this->assertEquals("Professional Economist Apprenticeship", $project->getContent()->get('title'));
        $this->assertEquals("<p>Lorem ipsum text here</p>", $project->getContent()->get('description'));
        $this->assertEquals("2018-07-11", $project->getContent()->get('open_date'));

        $project->getContent()->get('updates')->seek(1);
        $update = $project->getContent()->get('updates')->current();
        $this->assertEquals("An update 2A", $update->get('update_title')->__toString());
    }

    public function testPlainArray()
    {
        // Create a mock and queue responses
        $mock = new MockHandler([
            new Response(
                200,
                ['X-WP-Total' => 12, 'X-WP-TotalPages' => 2],
                file_get_contents(__DIR__ . '/../responses/custom/project.1.json')
            ),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $api = new RestData('something', new ContentModel(__DIR__ . '/config/custom/content_model.yaml'));
        $api->setClient($client);

        // Test it!
        $api->setContentType('projects');

        $project = $api->getOne(1);
        $this->assertTrue($project->getContent()->get('alt_name') instanceof PlainArray);
        $this->assertEquals('BA', $project->getContent()->get('alt_name')->getValue()[0]);
        $this->assertEquals('British Airways', $project->getContent()->get('alt_name')->getValue()[1]);
    }

}
