<?php
declare(strict_types=1);

namespace App\Tests\Frontend\Cms\Content;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Studio24\Frontend\Cms\Wordpress;
use Studio24\Frontend\Content\Url;
use Studio24\Frontend\ContentModel\ContentModel;

class WordPressTest extends TestCase
{
    public function testBasicData()
    {
        // Create a mock and queue responses
        $mock = new MockHandler([
            new Response(
                200,
                ['X-WP-Total' => 12, 'X-WP-TotalPages' => 2],
                file_get_contents(__DIR__ . '/../responses/demo/posts_1.json')
            ),
            new Response(
                200,
                ['X-WP-Total' => 12, 'X-WP-TotalPages' => 2],
                file_get_contents(__DIR__ . '/../responses/demo/posts_2.json')
            ),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $wordpress = new Wordpress('something');
        $wordpress->setClient($client);

        // Test it!
        $contentModel = new ContentModel(__DIR__ . '/config/demo/content_model.yaml');
        $wordpress->setContentModel($contentModel);
        $wordpress->setContentType('news');
        $pages = $wordpress->listPages();

        $this->assertEquals(1, $pages->getPagination()->getPage());
        $this->assertEquals(12, $pages->getPagination()->getTotalResults());
        $this->assertEquals(2, $pages->getPagination()->getTotalPages());

        $page = $pages->current();
        $this->assertEquals(1, $page->getId());
        $this->assertEquals("Hello world!", $page->getTitle());
        $this->assertEquals('2017-05-23', $page->getDatePublished()->getDate());
        $this->assertEquals('2017-05-23', $page->getDateModified()->getDate());
        $this->assertEquals("hello-world", $page->getUrlSlug());
        $this->assertEquals("<p>Welcome to <a href=\"http://wp-api.org/\">WP API Demo Sites</a>. This is your first post. Edit or delete it, then start blogging!</p>\n", $page->getContent()->current());
        $this->assertEquals("<p>Welcome to <a href=\"http://wp-api.org/\">WP API Demo Sites</a>. This is your first post. Edit or delete it, then start blogging!</p>\n", (string) $page);
        $this->assertEquals("<p>Welcome to WP API Demo Sites. This is your first post. Edit or delete it, then start blogging!</p>\n", $page->getExcerpt());

        $pages->next();
        $page = $pages->current();
        $this->assertEquals(35, $page->getId());
        $this->assertEquals("Quia corrupti quaerat et mollitia", $page->getTitle());

        $pages = $wordpress->listPages(2);

        $this->assertEquals(2, $pages->getPagination()->getPage());
        $this->assertEquals(12, $pages->getPagination()->getTotalResults());
        $this->assertEquals(2, $pages->getPagination()->getTotalPages());

        $page = $pages->current();
        $this->assertEquals(5, $page->getId());
        $this->assertEquals("Et aut qui a qui dolorum", $page->getTitle());

        $pages->next();
        $page = $pages->current();
        $this->assertEquals(29, $page->getId());
        $this->assertEquals("Rerum dolorum aut sunt vel ea", $page->getTitle());
    }

    public function testAcfData()
    {
        // Create a mock and queue responses
        $mock = new MockHandler([
            new Response(
                200,
                ['X-WP-Total' => 2, 'X-WP-TotalPages' => 1],
                file_get_contents(__DIR__ . '/../responses/acf/projects.json')
            ),
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../responses/acf/media/media.80.json')
            ),
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../responses/acf/media/media.81.json')
            ),

        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $contentModel = new ContentModel(__DIR__ . '/config/acf/content_model.yaml');
        $wordpress = new Wordpress('something', $contentModel);
        $wordpress->setContentType('project');
        $wordpress->setClient($client);

        // Test it!
        $pages = $wordpress->listPages();

        $this->assertEquals(1, $pages->getPagination()->getPage());
        $this->assertEquals(2, $pages->getPagination()->getTotalResults());
        $this->assertEquals(1, $pages->getPagination()->getTotalPages());

        $page = $pages->current();
        $this->assertEquals('79', $page->getId());
        $this->assertEquals("Lorem ipsum dolor sit school construction project", $page->getTitle());

        // Test array
        foreach ($page->getContent()->get('project_updates') as $key => $value) {
            switch ($key) {
                case 0:
                    $this->assertEquals("Update numero 1", $value->get('project_updates_project_update_title'));
                    $this->assertEquals("<p>Ahora algo es differente con esto documento.</p>\n", $value->get('project_updates_project_update_description'));
                    break;
                case 1:
                    $this->assertEquals("Update 11/03/2019", $value->get('project_updates_project_update_title'));
                    break;
            }
        }

        // Test documents
        // @todo Expand documents into actual documents
        // @see /Users/sjones/Sites/s24/frontend/src/Frontend/Cms/Wordpress.php getMediaField()
        // @see
//        foreach ($page->getContent()->get('project_documents') as $key => $value) {
//            switch ($key) {
//                case 0:
//                    $this->assertEquals("project_documents_project_documents_document", $value->getName());
//                    $this->assertEquals("http://ccs-agreements.cabinetoffice.localhost/wp-content/uploads/2019/02/test_2.pdf", $value->getUrl());
//                    $this->assertEquals("test_2", $value->getTitle());
//                    $this->assertEquals("", $value->getDescription());
//                    break;
//                case 1:
//                    $this->assertEquals("project_documents_project_documents_document", $value->getName());
//                    $this->assertEquals("http://ccs-agreements.cabinetoffice.localhost/wp-content/uploads/2019/02/test_4.pdf", $value->getUrl());
//                    $this->assertEquals("test_4", $value->getTitle());
//                    $this->assertEquals("", $value->getDescription());
//                    break;
//                default:
//                    break;
//            }
//        }
    }
}