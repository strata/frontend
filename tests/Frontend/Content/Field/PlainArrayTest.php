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

class PlainArrayTest extends TestCase
{
    public function testPlainArrayBuild()
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

        //associative array
        $projectAssocArray = $project->getContent()->get('icon_type')->getValue();

        $this->assertIsArray($projectAssocArray);
        $this->assertEquals('habitat', $projectAssocArray['value']);
        $this->assertEquals('Habitat', $projectAssocArray['label']);

        //non-associative array
        $intIndexArray = $project->getContent()->get('fruits')->getValue();

        $this->assertIsArray($intIndexArray);
        $this->assertIsString($project->getContent()->get('fruits')->__toString());

        foreach ($intIndexArray as $intKey => $itemValue) {
            $this->assertIsInt($intKey);
            switch ($intKey) {
                case 0:
                    $this->assertEquals('apple', $itemValue);
                    break;
                case 1:
                    $this->assertEquals('pear', $itemValue);
                    break;
                default:
                    break;
            }
        }

        //non-associative array with bool values
        $arrayOfBool = $project->getContent()->get('yesno')->getValue();

        $this->assertIsArray($arrayOfBool);

        foreach ($arrayOfBool as $intKey => $itemValue) {
            $this->assertIsInt($intKey);
            switch ($intKey) {
                case 0:
                    $this->assertIsInt($itemValue);
                    $this->assertEquals(0, $itemValue);
                    break;
                case 2:
                    $this->assertIsInt($itemValue);
                    $this->assertEquals(1, $itemValue);
                    break;
                default:
                    break;
            }
        }

        $this->assertTrue(true);
    }
}
