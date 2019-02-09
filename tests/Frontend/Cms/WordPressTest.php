<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Studio24\Frontend\Cms\Wordpress;

class WordPressTest extends TestCase
{
    public function testBasicPostData()
    {
        // Create a mock and queue two responses
        $mock = new MockHandler([
            new Response(200, ['X-WP-Total' => 12, 'X-WP-TotalPages' => 2],
                file_get_contents(__DIR__ . '/../responses/demo/posts_1.json')),
            new Response(200, ['X-WP-Total' => 12, 'X-WP-TotalPages' => 2],
                file_get_contents(__DIR__ . '/../responses/demo/posts_2.json')),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $wordpress = new Wordpress('something');
        $wordpress->setClient($client);

        // Test it!
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
        $this->assertEquals("<p>Welcome to <a href=\"http://wp-api.org/\">WP API Demo Sites</a>. This is your first post. Edit or delete it, then start blogging!</p>\n", $page->getContent());
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

    public function testComplexPostData()
    {
        // Create a mock and queue two responses
        $mock = new MockHandler([
            new Response(200, ['X-WP-Total' => 1041, 'X-WP-TotalPages' => 105],
                file_get_contents(__DIR__ . '/../responses/complex/posts_1.json')),
            new Response(200, ['X-WP-Total' => 1041, 'X-WP-TotalPages' => 105],
                file_get_contents(__DIR__ . '/../responses/complex/posts_2.json')),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $wordpress = new Wordpress('something');
        $wordpress->setClient($client);

        // Test it!
        $pages = $wordpress->listPages();

        $this->assertEquals(1, $pages->getPagination()->getPage());
        $this->assertEquals(1041, $pages->getPagination()->getTotalResults());
        $this->assertEquals(105, $pages->getPagination()->getTotalPages());

        $page = $pages->current();
        $this->assertEquals(1, $page->getId());
        $this->assertEquals("When is a Marine Protected Area not a Marine Protected Area?", $page->getTitle());
        $this->assertEquals('2019-02-05T12:00:52:00+00:00', $page->getDatePublished()->__toString());
        $this->assertEquals('/news/marine-protected-area-not-marine-protected-area', $page->getUrl());
    }



}