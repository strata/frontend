<?php

namespace Data;

use PHPUnit\Framework\TestCase;
use Strata\Data\Http\Response\MockResponseFromFile;
use Strata\Frontend\Content\Page;
use Strata\Frontend\Data\Repository\WordPress\WordPress;
use Symfony\Component\HttpClient\MockHttpClient;

class WordPressTest extends TestCase
{
    public function testPing()
    {
        $responses = [
            new MockResponseFromFile(__DIR__ . '/wordpress/ping.json')
        ];
        $api = new WordPress('https://www.simonrjones.net/wp-json/');
        $client = new MockHttpClient($responses);
        $api->getProvider()->setHttpClient($client);

        $this->assertTrue($api->ping());
        return;
    }

    public function testEmptyList()
    {
        $responses = [
            new MockResponseFromFile(__DIR__ . '/craftcms/empty.json')
        ];
        $api = new CraftCms('https://example.com/api', __DIR__ . '/craftcms/content_model.yaml');
        $client = new MockHttpClient($responses);
        $api->getProvider()->setHttpClient($client);

        $query = <<<EOD
{
  entryCount(section: "pressReleases")
  entries(section: "pressReleases", limit: 2, offset: 0) {
    postDate
    status
    title
    url
  }
}
EOD;

        $data = $api->query($query);
        $api->setContentType('page');
        $collection = $api->mapCollection($data, '[entries]', '[entryCount]');

        $this->assertEmpty($collection);
    }

    public function testList()
    {
        $responses = [
            new MockResponseFromFile(__DIR__ . '/craftcms/list.json')
        ];
        $api = new CraftCms('https://example.com/api', __DIR__ . '/craftcms/content_model.yaml');
        $client = new MockHttpClient($responses);
        $api->getProvider()->setHttpClient($client);

        $query = <<<EOD
{
  entryCount(section: "blogPosts")
  entries(section: "blogPosts", limit: 2, offset: 0) {
    id
    postDate
    status
    title
    slug
  }
}
EOD;

        $data = $api->query($query);

        // @todo get GraphQL query to map to PageCollection
        $api->setContentType('page');
        $collection = $api->mapCollection($data, '[entries]', '[entryCount]');
        $this->assertSame(2, count($collection));

        /** @var Page $page */
        $page = $collection->current();
        $this->assertEquals('Blog post lorem ipsum dolor sit amet', $page->getTitle());

        $collection->next();
        $page = $collection->current();
        $this->assertEquals('Test title 2', $page->getTitle());
    }

    public function testPage()
    {
        $responses = [
            new MockResponseFromFile(__DIR__ . '/wordpress/page.json')
        ];
        $api = new WordPress('https://example.com/api', __DIR__ . '/wordpress/content_model.yaml');
        $client = new MockHttpClient($responses);
        $api->getProvider()->setHttpClient($client);

        $api->setContentType('page');
        $data = $api->getPage(1);
        $page = $api->mapPage($data);

        $this->assertEquals("Lorem ipsum page test", $page->getTitle());
        $expected = <<<EOD
<p>This is an example page lorem ipsum dolor sit amet et do lorem this is the page content in here from the main WYSIWYG editor.</p>

EOD;

        $this->assertEquals($expected, $page->getContent()->__toString());
        $this->assertEquals("Joe Bloggs", $page->getAuthor()->getName());
        $this->assertEquals('page-template.php', $page->getTemplate());
    }

}
