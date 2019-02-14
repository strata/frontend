<?php
declare(strict_types=1);

namespace Studio24\Frontend\Cms;

use GuzzleHttp\Client;
use Studio24\Frontend\Content\Field\Boolean;
use Studio24\Frontend\Content\Field\FlexibleContent;
use Studio24\Frontend\Content\Field\Image;
use Studio24\Frontend\Content\Field\PlainText;
use Studio24\Frontend\Content\Field\Relation;
use Studio24\Frontend\Content\Field\RichText;
use Studio24\Frontend\Content\Page;
use Studio24\Frontend\Content\PageCollection;
use Studio24\Frontend\Content\User;
use Studio24\Frontend\Traits\CacheTrait;
use Studio24\Frontend\Api\Providers\Wordpress as WordpressApi;
use Studio24\Frontend\Utils\WordpressFieldFinder as FieldFinder;

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
      'posts'    => 'posts',
      'pages'    => 'pages',
      'media'    => 'media',
    ];

    /**
     * Constructor
     *
     * @param string $baseUrl API base URI
     */
    public function __construct(string $baseUrl = '')
    {
        $this->api = new WordpressApi($baseUrl);
    }

    /**
     * Set HTTP client
     *
     * Useful for testing
     *
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->api->setClient($client);
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
    public function listPages(
        int $page = 1,
        array $options = []
    ): PageCollection {
        // @todo Need to add unique identifier for this data based on options array
        $cacheKey = sprintf('%s.list.%s', $this->getContentType(), $page);
        if ($this->hasCache() && $this->cache->has($cacheKey)) {
            $pages = $this->cache->get($cacheKey);
            return $pages;
        }

        $list = $this->api->listPosts(
            $this->getContentApiEndpoint(),
            $page,
            $options
        );
        $pages = new PageCollection($list->getPagination());

        foreach ($list->getResponseData() as $pageData) {
            $pages->addItem($this->createPage($pageData));
        }

        if ($this->hasCache()) {
            $this->cache->set($cacheKey, $pages);
        }

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
    public function getPage(int $id): Page
    {
        $cacheKey = sprintf('%s.%s', $this->getContentType(), $id);
        if ($this->hasCache() && $this->cache->has($cacheKey)) {
            $page = $this->cache->get($cacheKey);
            return $page;
        }

        // Get content
        switch ($this->getContentType()) {
            case 'posts':
                $data = $this->api->getPost(
                    $this->getContentApiEndpoint(),
                    $id
                );
                $page = $this->createPage($data);

                $author = $this->api->getAuthor($data['author']);
                $page->setAuthor($this->createUser($author));
                break;

            case 'projects':
                $data = $this->api->getPost(
                    $this->getContentApiEndpoint(),
                    $id
                );
                $page = $this->createPage($data);

                break;
            default:
                throw new \Exception('Unrecognised content type: ' . $this->getContentType());
        }

        if ($this->hasCache()) {
            $this->cache->set($cacheKey, $page);
        }

        return $page;
    }

    /**
     * Generate page object from API data
     *
     * @param array $data
     * @return Page
     * @throws \Studio24\Exception\ContentFieldException
     */
    public function createPage(array $data): Page
    {
        $page = new Page();

        $this->setContentFields($page, $data);

        return $page;
    }

    /**
     * @param Page $page
     * @param $data
     * @return \Studio24\Frontend\Content\Page
     * @throws \Studio24\Exception\ContentFieldException
     */
    public function setContentFields($page, $data)
    {
        if (empty($data)) {
            return;
        }

        $page->setId(FieldFinder::id($data));
        $page->setTitle(FieldFinder::title($data));
        $page->setDatePublished(FieldFinder::datePublished($data));
        $page->setDateModified(FieldFinder::dateModified($data));
        $page->setStatus(FieldFinder::status($data));
        $page->setContentType(FieldFinder::type($data));

        if (!empty(FieldFinder::slug($data))) {
            $page->setUrlSlug(FieldFinder::slug($data));
        }

        if (!empty(FieldFinder::excerpt($data))) {
            $page->setExcerpt(FieldFinder::excerpt($data));
        }

        // Default WordPress content field
        if (!empty(FieldFinder::content($data))) {
            $page->addContent(new RichText('content', FieldFinder::content($data)));
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
                        $image = new Image(
                            $key,
                            $value['url'],
                            $value['title'],
                            $value['caption'],
                            $value['alt']
                        );

                        // Add sizes
                        $availableSizes = [
                          'thumbnail',
                          'medium',
                          'medium_large',
                          'large',
                          'twentyseventeen-featured-image',
                          'twentyseventeen-thumbnail-avatar',
                          'issue-post-image'
                        ];
                        foreach ($availableSizes as $sizeName) {
                            if (isset($value['sizes'][$sizeName])) {
                                $width = $sizeName . '-width';
                                $height = $sizeName . '-height';
                                $image->addSize(
                                    $value['sizes'][$sizeName],
                                    $value['sizes'][$width],
                                    $value['sizes'][$height],
                                    $sizeName
                                );
                            }
                        }
                        $page->addContent($image);
                        break;

                    case 'author':
                        // First check to see if the author field is empty
                        if (empty($value)) {
                            break;
                        }

                        $relation = new Relation($key);

                        self::setContentFields($relation->getContent(), $value);
                        $page->addContent($relation);

                        break;

                    case 'page_content':
                        if (!is_array($value)) {
                            continue 2;
                        }
                        $flexible = new FlexibleContent($key);
                        foreach ($value as $key => $value) {
                            /**
                             *
                             * $component = new Component('My component name');
                             * $component->addContent(new PlainText('name1'));
                             * $component->addContent(new Image('name2'));
                             * $flexible->addComponent($component);
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
    public function createUser(array $data): User
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
