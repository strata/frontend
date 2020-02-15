<?php

declare(strict_types=1);

namespace App\Tests\Frontend\Cms\Content;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Strata\Frontend\Cms\RestData;
use Strata\Frontend\ContentModel\ContentModel;

/**
 * Test Twig rendering of content
 *
 * @package App\Tests\Frontend\Cms\Content
 */
class TwigAccessTest extends TestCase
{

    /**
     * Clear Twig cache folder
     */
    public function tearDown(): void
    {
        parent::tearDown();

        system('rm -rf ' . __DIR__ . '/twig/cache/*');
    }

    public function testTwigAccess()
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

        // Twig
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/twig/templates');
        $twig = new \Twig\Environment($loader, [
            'cache' => __DIR__ . '/twig/cache'
        ]);

        // Test it!
        $api->setContentType('projects');
        $projects = $api->list();

        $expected = <<<EOD
Some name here

EOD;
        $template = $twig->load('metadata.html.twig');
        $this->assertEquals($expected, $template->render(['metadata' => $projects->getMetadata()]));

        $expected = <<<EOD
This is page 1, displaying results 1-10

EOD;
        $template = $twig->load('pagination.html.twig');
        $this->assertEquals($expected, $template->render(['pagination' => $projects->getPagination()]));
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

        // Twig
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/twig/templates');
        $twig = new \Twig\Environment($loader, [
            'cache' => __DIR__ . '/twig/cache'
        ]);

        // Test it!
        $api->setContentType('projects');
        $project = $api->getOne(1);

        $expected = <<<EOD
BA. British Airways. Another name.

EOD;

        $template = $twig->load('plainarray1.html.twig');
        $this->assertEquals($expected, $template->render(['page' => $project]));

        $expected = <<<EOD
  - BA
  - British Airways
  - Another name

EOD;

        $template = $twig->load('plainarray2.html.twig');
        $this->assertEquals($expected, $template->render(['page' => $project]));

        $template = $twig->load('plainarray3.html.twig');
        $this->assertEquals('FULL' . PHP_EOL, $template->render(['page' => $project]));
    }
}
