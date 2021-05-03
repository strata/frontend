<?php

namespace Data;

use PHPUnit\Framework\TestCase;
use Strata\Data\Http\Response\MockResponseFromFile;
use Strata\Frontend\Content\Page;
use Strata\Frontend\Data\CraftCms\CraftCms;
use Symfony\Component\HttpClient\MockHttpClient;

class CraftCmsTest extends TestCase
{
    public function testPing()
    {
        $responses = [
            new MockResponseFromFile(__DIR__ . '/craftcms/ping.json')
        ];
        $api = new CraftCms('https://example.com/api');
        $client = new MockHttpClient($responses);
        $api->getProvider()->setHttpClient($client);

        $this->assertTrue($api->ping());
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

    public function testItem()
    {
        $responses = [
            new MockResponseFromFile(__DIR__ . '/craftcms/item.json')
        ];
        $api = new CraftCms('https://example.com/api', __DIR__ . '/craftcms/content_model.yaml');
        $client = new MockHttpClient($responses);
        $api->getProvider()->setHttpClient($client);

        $query = <<<EOD
{
  entry(section: "blogPosts", id: "169") {
    title
    slug
    uri
    postDate
    status
    language
    localized {
      language
      uri
    }
    ... on blogPosts_default_Entry {
      authors {
        ... on authors_author_BlockType {
          authorName
          authorEmailAddress
        }
      }
      defaultFlexibleComponents {
        ... on defaultFlexibleComponents_textComponent_BlockType {
          typeHandle
          contentField 
        }
        ... on defaultFlexibleComponents_blockquoteComponent_BlockType {
          typeHandle
          quoteText
          citation 
        }
        ... on defaultFlexibleComponents_videoMediaComponent_BlockType {
          typeHandle
          videoTitle
          videoUrl {
            url
            type
            providerName
            providerUrl
          }
          videoCaption
          linkToVideoTranscript 
        }
      }
      youMayAlsoLike {
        title
        uri
      }
      postPageNotes
    }
  }
}
EOD;

        $data = $api->query($query);

        // Test raw data
        $array = $api->decode($data);
        $this->assertSame('Blog post lorem ipsum dolor sit amet', $array['entry']['title']);
        $this->assertSame('blog-post-lorem-ipsum-dolor-sit-amet', $array['entry']['slug']);

        // Test data mapped to model
        $api->setContentType('page');
        $page = $api->mapPage($data, '[entry]');
        $this->assertEquals('Blog post lorem ipsum dolor sit amet', $page->getTitle());
        $this->assertEquals('blog-post-lorem-ipsum-dolor-sit-amet', $page->getUrlSlug());
        $this->assertEquals('Blog post lorem ipsum dolor sit amet', $page->getHead()->getTitle());
        $this->assertEquals('Blog post lorem ipsum dolor sit amet', $page->getHead()->getMeta('og:title'));

        $authors = $page->getContent()->get('authors');
        $author = $authors->current();
        $this->assertEquals('Joe Bloggs', $author->get('authorName'));
    }

}
