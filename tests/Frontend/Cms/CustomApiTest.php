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
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $api = new RestData('something', new ContentModel(__DIR__ . '/config/custom/content_model.yaml'));
        $api->setClient($client);

        // Test it!
        $api->setContentType('projects');
        $projects = $api->list();

        $this->assertEquals(1, $projects->getPagination()->getPage());
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
                    foreach ($updates as $update) {
                        $y=0;
                        switch ($y) {
                            case 0:
                                $this->assertEquals( "An update 1B", $update->get('update_title'));
                                $this->assertEquals( "Some more text here and here", $update->get('update_description'));
                                break;
                            case 1:
                                $this->assertEquals(  "An update 2B", $update->get('update_title'));
                                $this->assertEquals( "Some more text here and there", $update->get('update_description'));
                                break;
                            $y++;
                        }
                    }
                    break;
                case 1:
                    $this->assertEquals(2, $item->getContent()->get('id')->getValue());
                    break;
            }
            $x++;
        }
    }
}
