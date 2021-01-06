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

class TaxonomyTermsFieldTest extends TestCase
{
    public function testTaxonomyTermsField()
    {
        // Create a mock and queue two responses
        $mock = new MockHandler([
            new Response(
                200,
                ['X-WP-Total' => 1, 'X-WP-TotalPages' => 1],
                file_get_contents(__DIR__ . '/../../responses/content/field/post.17343.json')
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
        $api->setContentType('post');

        $postObject = $api->getPage(17343);

        $typeTermsField = $postObject->getContent()->get('type');
        $this->assertInstanceOf('Strata\Frontend\Content\Field\TaxonomyTerms', $typeTermsField);

        $typeTerms = $typeTermsField->getContent();
        $this->assertInstanceOf('Strata\Frontend\Content\Taxonomies\TermCollection', $typeTerms);

        $firstTerm = $typeTerms->current();

        $this->assertEquals(48, $firstTerm->getID());
        $this->assertEquals('views', $firstTerm->getSlug());

        $newsThemeTermsField = $postObject->getContent()->get('news_theme');
        $this->assertInstanceOf('Strata\Frontend\Content\Field\TaxonomyTerms', $newsThemeTermsField);

        $newsThemeTerms = $newsThemeTermsField->getContent();
        $this->assertInstanceOf('Strata\Frontend\Content\Taxonomies\TermCollection', $newsThemeTerms);

        $firstThemeTerm = $newsThemeTerms->current();
        $this->assertEquals(244, $firstThemeTerm->getID());
        $this->assertEquals('landscapes-habitats', $firstThemeTerm->getSlug());

        $newsThemeTerms->next();

        $secondThemeTerm = $newsThemeTerms->current();
        $this->assertEquals(248, $secondThemeTerm->getID());
        $this->assertEquals('species-on-the-brink', $secondThemeTerm->getSlug());
    }
}
