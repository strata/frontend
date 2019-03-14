<?php
declare(strict_types=1);

namespace App\Tests\Frontend\Cms;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Studio24\Frontend\Cms\Wordpress;
use Studio24\Frontend\Content\Taxonomies\Term;

class TaxonomyTest extends TestCase
{

    public function testTermSetup() : void
    {
        // Create a mock and queue responses
        $mock = new MockHandler([
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../responses/taxonomy/taxonomy.framework_type.25.json')
            ),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $wordpress = new Wordpress('something');
        $wordpress->setClient($client);

        $taxonomyTerm = $wordpress->createTerm('framework_type', 25);

        $this->assertInstanceOf('Studio24\Frontend\Content\Taxonomies\Term', $taxonomyTerm);

        $this->assertEquals(25, $taxonomyTerm->getID());
        $this->assertEquals('Standard', $taxonomyTerm->getName());
        $this->assertEquals('standard', $taxonomyTerm->getSlug());
        $this->assertEquals('http://localhost/framework_type/standard/', $taxonomyTerm->getLink());
        $this->assertEquals(0, $taxonomyTerm->getCount());
        $this->assertEmpty($taxonomyTerm->getDescription());
    }
}