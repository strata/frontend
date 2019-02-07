<?php
declare(strict_types=1);

namespace Studio24\Frontend\Cms;

use Studio24\Frontend\Content\Field\Boolean;
use Studio24\Frontend\Content\Field\FlexibleContent;
use Studio24\Frontend\Content\Field\Image;
use Studio24\Frontend\Content\Field\PlainText;
use Studio24\Frontend\Content\Field\RichText;
use Studio24\Frontend\Content\Page;
use Studio24\Frontend\Content\PageCollection;
use Studio24\Frontend\Content\User;
use Studio24\Frontend\Traits\CacheTrait;
use Studio24\Frontend\Api\Providers\Wordpress as WordpressApi;

/**
 * Class to manage access to Wordpress API and returns well-formed content objects
 *
 * This class is also responsible for caching results
 *
 * @package Studio24\Frontend\Cms
 */
class Wordpress
{
    use CacheTrait;

    /**
     * API
     *
     * @var WordpressApi
     */
    protected $api;

    /**
     * Current content type
     *
     * @var string
     */
    protected $contentType = 'posts';

    /**
     * Array of available content types
     *
     * content type => API endpoint
     *
     * @var array
     */
    protected $contentTypes = [
        'posts'      => 'posts',
        'projects'   => 'projects',
    ];

    public function __construct()
    {
        $this->api = new WordpressApi();
    }

    public function hasContentType(string $type): bool
    {
        if (array_key_exists($type, $this->getContentTypes())) {
            return true;
        }
        return false;
    }

    public function setContentType(string $type): Wordpress
    {
        $this->contentType = $type;
        return $this;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getContentApiEndpoint(): string
    {
        return $this->contentTypes[$this->getContentType()];
    }

    public function getContentTypes(): array
    {
        return $this->contentTypes;
    }

    /**
     * Return list of pages
     *
     * @see https://developer.wordpress.org/rest-api/reference/pages/#list-pages
     *
     * @param int $page Page number, default = 1
     * @param array $options Array of options to select data from WordPress
     * @return PageCollection
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Studio24\Exception\ContentFieldException
     * @throws \Studio24\Exception\FailedRequestException
     * @throws \Studio24\Exception\PermissionException
     * @throws \Studio24\Frontend\Exception\PaginationException
     */
    public function listPages(int $page = 1, array $options = []): PageCollection
    {
        // @todo Need to add unique identifier for this data based on options array
        $cacheKey = sprintf('%s.list.%s', $this->getContentType(), $page);
        if ($this->hasCache() && $this->cache->has($cacheKey)) {
            $pages = $this->cache->get($cacheKey);
            return $pages;
        }

        $list = $this->api->listPosts($this->getContentApiEndpoint(), $page, $options);
        $pages = new PageCollection($list->getPagination());

        foreach ($list->getResponseData() as $pageData) {
            $pages->addItem($this->createPage($pageData));
        }

        $this->cache->set($cacheKey, $pages);

        return $pages;
    }

    /**
     * Return a page
     *
     * @param int $id
     * @param string $contentType
     * @return Page
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Studio24\Exception\ContentFieldException
     * @throws \Studio24\Exception\FailedRequestException
     * @throws \Studio24\Exception\PermissionException
     */
    public function getPage(int $id) : Page
    {
        $cacheKey = sprintf('%s.%s', $this->getContentType(), $id);
        if ($this->hasCache() && $this->cache->has($cacheKey)) {
            $page = $this->cache->get($cacheKey);
            return $page;
        }

        // Get content
        switch ($this->getContentType()) {
            case 'posts':
                $data = $this->api->getPost($this->getContentApiEndpoint(), $id);
                $page = $this->createPage($data);

                $author = $this->api->getAuthor($data['author']);
                $page->setAuthor($this->createUser($author));
                break;

            case 'projects':
                $data = $this->api->getPost($this->getContentApiEndpoint(), $id);
                $page = $this->createPage($data);

                break;
            default:
                throw new \Exception('Unrecognised content type: ' . $this->getContentType());
        }

        $this->cache->set($cacheKey, $page);

        return $page;
    }

    /**
     * Generate page object from API data
     *
     * @param array $data
     * @return Page
     * @throws \Studio24\Exception\ContentFieldException
     */
    public function createPage(array $data) : Page
    {
        dump($data);
        $page = new Page();
        $page->setId($data['id']);
        $page->setTitle($data['title']['rendered']);
        $page->setDatePublished($data['date']);
        $page->setDateModified($data['modified']);
        $page->setUrlSlug($data['slug']);
        $page->setStatus($data['status']);
        $page->setContentType($data['type']);

        if (isset($data['excerpt']) && !empty($data['excerpt']['rendered'])) {
            $page->setExcerpt($data['excerpt']['rendered']);
        }

        // Default WordPress content field
        if (isset($data['content']) && !empty($data['content']['rendered'])) {
            $page->addContent(new RichText('content', $data['content']['rendered']));
        }

        // ACF content fields
        if (isset($data['acf']) && is_array($data['acf'])) {
            foreach ($data['acf'] as $key => $value) {
                // Hard-code the available content types for now
                // @todo set via configuration
                switch ($key) {
                    case 'post_type':
                    case 'theme':
                    case 'description':
                    case 'image_credit':
                    case 'banner_title':
                    case 'banner_text':
                    case 'mini_summary':
                    case 'full_summary':
                        if (empty($value)) {
                            continue 2;
                        }
                        $page->addContent(new PlainText($key, $value));
                        break;
                    case 'video_loop':
                    case 'video_poster':
                    case 'featured':
                    case 'post_index_image':
                    case 'exclude_from_search':
                        $page->addContent(new Boolean($key, $value));
                        break;
                    case 'image':
                        if (empty($value['url'])) {
                            continue 2;
                        }
                        $image = new Image($key, $value['url'], $value['title'], $value['caption'], $value['alt']);

                        // Add sizes
                        $availableSizes = ['thumbnail', 'medium', 'medium_large', 'large', 'twentyseventeen-featured-image', 'twentyseventeen-thumbnail-avatar', 'issue-post-image'];
                        foreach ($availableSizes as $sizeName) {
                            if (isset($value['sizes'][$sizeName])) {
                                $width = $sizeName . '-width';
                                $height = $sizeName . '-height';
                                $image->addSize($value['sizes'][$sizeName], $value['sizes'][$width], $value['sizes'][$height], $sizeName);
                            }
                        }
                        $page->addContent($image);
                        break;

                    case 'author':
                        /**
                         * Test code:
                         * {% set author = page.content.author }}
                         * or
                         * {% set author = page.relation('author') }}
                         *
                         * {{ author.title }}
                         * {{ author.content.image.getSize('thumbnail') }}
                         *
                         * $relation = new Relation($key, $this->createPage($data));
                         * $page->addContent($relation);
                         *
                         * author = new "relation" to an existing content object (person post type)
                         *
                         * Test URL:
                         * http://local.fauna-flora.org/news/lets-talk-elephant-wasnt-room
                         *
                         * Elephant news post, ID 21717
                         * Written by:
                         * Tim Knight, Person ID 10031
                         *
                         */
                        break;

                    case 'page_content':
                        if (!is_array($value)) {
                            continue 2;
                        }
                        $flexible = new FlexibleContent($key);
                        foreach ($value as $key => $value) {
                            /**

                             $component = new Component('My component name');
                             $component->addContent(new PlainText('name1'));
                             $component->addContent(new Image('name2'));
                             $flexible->addComponent($component);

                             */
                        }
                        $page->addContent($flexible);
                }

                // @todo add relation (author = ACF post object)
            }
        }

        // Testing!
        //dump($page, $data);exit;

        return $page;
    }

    /**
     * Generate user object from API data
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data) : User
    {
        $user = new User();
        $user->setId($data['id'])
            ->setName($data['name']);
        if (!empty($data['description'])) {
            $user->setBio($data['description']);
        }
        return $user;
    }
}
