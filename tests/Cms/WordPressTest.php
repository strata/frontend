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
use Studio24\Frontend\Exception\NotFoundException;

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
                [],
                file_get_contents(__DIR__ . '/../responses/acf/media/media.21.json')
            ),
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../responses/acf/users.1.json')
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
        $this->assertEquals('http://local.wp-api.test/wp-content/uploads/2019/03/Screen-Shot-2019-03-05-at-14.24.48-300x23.png', $page->getFeaturedImage()->byName('medium'));

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
        $this->assertEquals("Jane Doe", $page->getAuthor()->getName());

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
                ['Content-length' => 23857 ]
            ),
            new Response(
                200,
                ['Content-length' => 169812 ]
            ),
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../responses/acf/media/media.3496.json')
            ),
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../responses/acf/media/media.3495.json')
            ),
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../responses/acf/media/media.3496.json')
            ),
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../responses/acf/media/media.3495.json')
            ),
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../responses/acf/media/media.21.json')
            )
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
        $this->assertEmpty($page->getContent()->get('project_benefits')->__toString());
        $this->assertEmpty($page->getContent()->get('fake_field'));

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
        $docs = $page->getContent()->get('project_documents');
        $this->assertInstanceOf('Studio24\Frontend\Content\Field\ArrayContent', $docs);
        $this->assertEquals(3, count($docs));

        foreach ($docs as $key => $item) {
            $doc = $item->get('project_documents_project_documents_document');

            switch ($key) {
                case 0:
                    $this->assertEquals("http://localhost/wp-content/uploads/2019/02/test_2.pdf", $doc->getUrl());
                    $this->assertEquals("test_2", $doc->getTitle());
                    $this->assertEquals("23.3 KB", $doc->getFileSize());
                    $this->assertEmpty($doc->getDescription());
                    break;
                case 1:
                    $this->assertEquals("http://local.wp-api.test/wp-content/uploads/2019/03/timeline-IRIS-Education-website-roll-out-.pdf", $doc->getUrl());
                    $this->assertEquals("timeline - IRIS Education website roll out", $doc->getTitle());
                    $this->assertEquals("165.83 KB", $doc->getFileSize());
                    $this->assertEmpty($doc->getDescription());
                    break;
                case 2:
                    $this->assertEquals("165.83 KB", $doc->getFileSize());
                    break;
            }
        }

        //Test video
        $video = $page->getContent()->get('project_video');
        $this->assertInstanceOf('Studio24\Frontend\Content\Field\Video', $video);

        $this->assertEquals('http://local.client.com/wp-content/uploads/2019/02/Saint-Lucia-racer-moving-Jeremy-holden-FFI.mp4', $video->getValue());
        $this->assertEquals('6.42 MB', $video->getFileSize());
        $this->assertEquals('1862802', $video->getBitRate());
        $this->assertEquals('0:29', $video->getLength());
        $this->assertEquals('Saint-Lucia-racer-moving-Jeremy-holden-FFI', $video->getTitle());
        $this->assertEmpty($video->getDescription());

        $video = $page->getContent()->get('project_video_array');
        $this->assertInstanceOf('Studio24\Frontend\Content\Field\Video', $video);

        $this->assertEquals('http://local.client.com/wp-content/uploads/2019/02/Saint-Lucia-racer-moving-Jeremy-holden-FFI.mp4', $video->getValue());
        $this->assertEquals('6.42 MB', $video->getFileSize());
        $this->assertEquals('1862802', $video->getBitRate());
        $this->assertEquals('0:29', $video->getLength());
        $this->assertEquals('Saint-Lucia-racer-moving-Jeremy-holden-FFI', $video->getTitle());
        $this->assertEmpty($video->getDescription());

        //Test audio
        $audio = $page->getContent()->get('project_audio');
        $this->assertInstanceOf('Studio24\Frontend\Content\Field\Audio', $audio);

        $this->assertEquals('http://local.client.com/wp-content/uploads/2019/02/Kyoto-Bell.mp3', $audio->getValue());
        $this->assertEquals('32.24 KB', $audio->getFileSize());
        $this->assertEquals('128000', $audio->getBitRate());
        $this->assertEquals('0:02', $audio->getLength());
        $this->assertEquals('Kyoto Bell', $audio->getTitle());
        $this->assertEmpty($audio->getDescription());

        $audio = $page->getContent()->get('project_audio_array');
        $this->assertInstanceOf('Studio24\Frontend\Content\Field\Audio', $audio);

        $this->assertEquals('http://local.client.com/wp-content/uploads/2019/02/Kyoto-Bell.mp3', $audio->getValue());
        $this->assertEquals('32.24 KB', $audio->getFileSize());
        $this->assertEquals('128000', $audio->getBitRate());
        $this->assertEquals('0:02', $audio->getLength());
        $this->assertEquals('Kyoto Bell', $audio->getTitle());
        $this->assertEmpty($audio->getDescription());

        $image = $page->getContent()->get('image_by_id');
        $this->assertInstanceOf('Studio24\Frontend\Content\Field\Image', $image);
        $this->assertEquals('http://local.wp-api.test/wp-content/uploads/2019/03/Screen-Shot-2019-03-05-at-14.24.48.png', $image->getValue());
        $this->assertEquals('http://local.wp-api.test/wp-content/uploads/2019/03/Screen-Shot-2019-03-05-at-14.24.48-1024x80.png', $image->byName('fp-medium'));
    }


    public function testPage()
    {
        // Create a mock and queue responses
        $mock = new MockHandler([
            new Response(
                200,
                ['X-WP-Total' => 1, 'X-WP-TotalPages' => 1],
                file_get_contents(__DIR__ . '/../responses/acf/pages.json')
            ),
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../responses/acf/users.1.json')
            ),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $contentModel = new ContentModel(__DIR__ . '/config/acf/content_model.yaml');
        $wordpress = new Wordpress('something', $contentModel);
        $wordpress->setContentType('page');
        $wordpress->setClient($client);

        // Test it!
        $page = $wordpress->getPageByUrl('/lorem-ipsum-page-test/');

        $this->assertEquals("Lorem ipsum page test", $page->getTitle());
        $expected = <<<EOD
<p>This is an example page lorem ipsum dolor sit amet et do lorem this is the page content in here from the main WYSIWYG editor.</p>

EOD;

        $this->assertEquals($expected, $page->getContent()->__toString());
        $this->assertEquals("Joe Bloggs", $page->getAuthor()->getName());
        $this->assertEquals('page-template.php', $page->getTemplate());
    }

    public function testPageTaxonomy()
    {
        $mock = new MockHandler([
            new Response(
                200,
                ['X-WP-Total' => 1, 'X-WP-TotalPages' => 1],
                file_get_contents(__DIR__ . '/../responses/taxonomy/post.5049.json')
            ),
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../responses/taxonomy/taxonomy.categories.28.json')
            ),
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../responses/taxonomy/taxonomy.categories.1.json')
            ),
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../responses/acf/users.1.json')
            ),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $contentModel = new ContentModel(__DIR__ . '/config/taxonomies/content_model.yaml');
        $wordpress = new Wordpress('something', $contentModel);
        $wordpress->setContentType('news');
        $wordpress->setClient($client);

        // Test it!
        $page = $wordpress->getPage(5049);

        $pageTaxonomies = $page->getTaxonomies();

        $this->assertInstanceOf('Studio24\Frontend\Content\Taxonomies\TermCollection', $pageTaxonomies['categories']);
        $this->assertInstanceOf('Studio24\Frontend\Content\Taxonomies\Term', $pageTaxonomies['categories']->current());

        foreach ($pageTaxonomies['categories'] as $key => $termObject) {
            switch ($key) {
                case 0:
                    $this->assertEquals(28, $termObject->getId());
                    $this->assertEquals('http://localhost/category/test/', $termObject->getlink());
                    break;
                case 1:
                    $this->assertEquals('uncategorized', $termObject->getSlug());
                    $this->assertEmpty($termObject->getDescription());
                    break;
            }
        }
    }

    public function testFlexibleContent()
    {
        $mock = new MockHandler([
            new Response(
                200,
                ['X-WP-Total' => 1, 'X-WP-TotalPages' => 1],
                file_get_contents(__DIR__ . '/../responses/flexible-content/page.15.json')
            ),
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../responses/acf/users.1.json')
            ),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $contentModel = new ContentModel(__DIR__ . '/config/flexible-content/content_model.yaml');
        $wordpress = new Wordpress('something', $contentModel);
        $wordpress->setContentType('pages');
        $wordpress->setClient($client);

        $page = $wordpress->getPage(15);

        $flexibleContent = $page->getContent()->get('page_content');

        $this->assertInstanceOf('Studio24\Frontend\Content\Field\FlexibleContent', $flexibleContent);
        $this->assertEquals(3, count($flexibleContent));
        $this->assertEquals(1, count($flexibleContent->get('statement_block')));
        $this->assertEquals(2, count($flexibleContent->get('content')));

        $x = 0;
        foreach ($flexibleContent as $flexibleComponent) {
            $this->assertInstanceOf('Studio24\Frontend\Content\Field\Component', $flexibleComponent);

            switch ($x) {
                case 0:
                    $this->assertEquals('content', $flexibleComponent->getName());
                    foreach ($flexibleComponent->getContent() as $fieldName => $fieldValue) {
                        switch ($fieldName) {
                            case 'title':
                                $this->assertEquals('title', $fieldValue->getName());
                                $this->assertEquals('Biodiversity Champions', $fieldValue->getValue());
                                break;
                            case 'content':
                                $this->assertEquals('content', $fieldValue->getName());
                                $this->assertEquals('<p>Established over a century ago, Fauna &amp; Flora International (FFI) was the world’s first international wildlife conservation organisation.</p>', $fieldValue->getValue());
                                break;
                            case 'full_width':
                                $this->assertEquals('full_width', $fieldValue->getName());
                                $this->assertTrue($fieldValue->getValue());
                                break;
                            case 'trigger':
                                $this->assertEquals('trigger', $fieldValue->getName());
                                $this->assertFalse($fieldValue->getValue());
                                break;
                            case 'number_of_elements':
                                $this->assertEquals('number_of_elements', $fieldValue->getName());
                                $this->assertIsNumeric($fieldValue->getValue());
                                $this->assertEquals(1, $fieldValue->getValue());
                                break;
                        }
                    }
                    break;
                case 1:
                    $this->assertEquals('statement_block', $flexibleComponent->getName());
                    foreach ($flexibleComponent->getContent() as $fieldName => $fieldValue) {
                        switch ($fieldName) {
                            case 'statement_title':
                                $this->assertEquals('statement_title', $fieldValue->getName());
                                $this->assertEquals('Our mission', $fieldValue->getValue());
                                break;
                            case 'statement':
                                $this->assertEquals('statement', $fieldValue->getName());
                                $this->assertEquals('<p>is to conserve threatened species and ecosystems worldwide, choosing solutions that are sustainable, based on sound science, and which take into account human needs.</p>', $fieldValue->getValue());
                                break;
                        }
                    }
                    break;
                case 2:
                    break;
            }

            $x++;
        }
    }


    public function testInvalidPageUrl()
    {
        // Create a mock and queue two responses
        $mock = new MockHandler([
            new Response(
                200,
                ['X-WP-Total' => 1, 'X-WP-TotalPages' => 1],
                file_get_contents(__DIR__ . '/../responses/demo/post.1.json')
            ),
            new Response(
                200,
                [],
                '[]'
            ),
            new Response(
                200,
                [],
                '[]'
            ),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $api = new Wordpress('http://demo.wp-api.org/wp-json/wp/v2/');
        $api->setClient($client);
        $contentModel = new ContentModel(__DIR__ . '/config/demo/content_model.yaml');
        $api->setContentModel($contentModel);
        $api->setContentType('news');

        // Test it!
        $this->expectExceptionCode(400);
        $page = $api->getPageByUrl('/made/up/hello-world/');

        $this->expectExceptionCode(404);
        $page = $api->getPageByUrl('/made/up/url/');
    }

    public function testMissingPageImageAuthorTaxonomy()
    {
        $mock = new MockHandler([
            new Response(
                200,
                ['X-WP-Total' => 1, 'X-WP-TotalPages' => 1],
                file_get_contents(__DIR__ . '/../responses/demo/post.1b.json')
            ),
            new Response(
                404,
                [],
                '{"code":"rest_invalid_param","message":"page not found","data":{"status":404}}'
            ),
            new Response(
                404,
                [],
                '{"code":"rest_invalid_param","message":"page not found","data":{"status":404}}'
            ),
            new Response(
                404,
                [],
                '{"code":"rest_invalid_param","message":"page not found","data":{"status":404}}'
            ),
            new Response(
                200,
                ['X-WP-Total' => 1, 'X-WP-TotalPages' => 1],
                file_get_contents(__DIR__ . '/../responses/demo/post.1c.json')
            ),
            new Response(
                404,
                [],
                '{"code":"rest_invalid_param","message":"page not found","data":{"status":404}}'
            ),
            new Response(
                404,
                [],
                '{"code":"rest_invalid_param","message":"page not found","data":{"status":404}}'
            ),
            new Response(
                404,
                [],
                '{"code":"rest_invalid_param","message":"page not found","data":{"status":404}}'
            ),
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        $api = new Wordpress('http://demo.wp-api.org/wp-json/wp/v2/');
        $api->setClient($client);
        $contentModel = new ContentModel(__DIR__ . '/config/demo/content_model_with_taxonomy.yaml');
        $api->setContentModel($contentModel);
        $api->setContentType('news');
        try {
            $page = $api->getPageByUrl('/20171234/05/23/hello-world/');
        } catch (NotFoundHttpException $e) {
            throw new NotFoundHttpException('Resource not found', $e);
        }
        //test execution didn't stop due to 404 thrown by missing author, image, or taxonomy term
        $this->assertTrue(true);
        try {
            $page = $api->getPage(1234);
        } catch (NotFoundHttpException $e) {
            throw new NotFoundHttpException('Resource not found', $e);
        }
        //test execution didn't stop due to 404 thrown by missing author, image, or taxonomy term
        $this->assertTrue(true);
    }

    public function testRelationArray()
    {
        // Create a mock and queue response
        $mock = new MockHandler([
            new Response(
                200,
                ['X-WP-Total' => 1, 'X-WP-TotalPages' => 1],
                file_get_contents(__DIR__ . '/../responses/acf/page.9.json')
            ),
            new Response(
                200,
                ['X-WP-Total' => 1, 'X-WP-TotalPages' => 1],
                file_get_contents(__DIR__ . '/../responses/acf/users.1.json')
            ),
            new Response(
                200,
                ['X-WP-Total' => 1, 'X-WP-TotalPages' => 1],
                file_get_contents(__DIR__ . '/../responses/acf/users.195.json')
            ),
            new Response(
                200,
                ['X-WP-Total' => 1, 'X-WP-TotalPages' => 1],
                file_get_contents(__DIR__ . '/../responses/acf/users.195.json')
            ),
            new Response(
                200,
                ['X-WP-Total' => 1, 'X-WP-TotalPages' => 1],
                file_get_contents(__DIR__ . '/../responses/acf/users.195.json')
            ),
            new Response(
                200,
                ['X-WP-Total' => 1, 'X-WP-TotalPages' => 1],
                file_get_contents(__DIR__ . '/../responses/acf/users.195.json')
            ),
            new Response(
                200,
                ['X-WP-Total' => 1, 'X-WP-TotalPages' => 1],
                file_get_contents(__DIR__ . '/../responses/acf/users.195.json')
            ),
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        $api = new Wordpress('something');
        $api->setClient($client);
        $contentModel = new ContentModel(__DIR__ . '/config/acf/content_model.yaml');
        $api->setContentModel($contentModel);
        $api->setContentType('page2');

        // Test it!
        $page = $api->getPage(9);

        $careers = $page->getContent()->get('page_content')->get('careers');
        $this->assertEquals(1, count($careers));
        $x = 0;
        foreach ($careers as $career) {
            switch ($x) {
                case 0:
                    $careerItems = $career->getContent()->get('career');
                    $this->assertEquals(3, count($careerItems));
                    $y = 0;
                    foreach ($careerItems as $item) {
                        switch ($y) {
                            case 0:
                                $this->assertEquals("ACME Manager", $item->getContent()->getTitle());
                                $this->assertEquals('career', $item->getContentType());
                                break;
                            case 1:
                                $this->assertEquals("Project Manager", $item->getContent()->getTitle());
                        }
                        $y++;
                    }

                    $relatedPosts = $career->getContent()->get('related_posts');
                    $this->assertEquals(2, count($relatedPosts));
                    $y = 0;
                    foreach ($relatedPosts as $item) {
                        switch ($y) {
                            case 0:
                                $this->assertEquals("ACME Manager", $item->getContent()->getTitle());
                                $this->assertEquals('career', $item->getContentType());
                                $this->assertEquals("Permanent", $item->getContent()->getContent()->get('contract_length'));
                                break;
                            case 1:
                                $this->assertEquals("ABC123 Project", $item->getContent()->getTitle());
                                $this->assertEquals('project', $item->getContentType());
                                $this->assertEquals("ABC123", $item->getContent()->getContent()->get('project_id'));
                        }
                        $y++;
                    }

                    break;
            }
            $x++;
        }
    }
}
