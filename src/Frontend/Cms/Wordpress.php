<?php
declare(strict_types=1);

namespace Studio24\Frontend\Cms;

use GuzzleHttp\Client;
use Studio24\Frontend\Content\ContentInterface;
use Studio24\Frontend\Content\Field\ArrayContent;
use Studio24\Frontend\Content\Field\Document;
use Studio24\Frontend\Exception\ContentTypeNotSetException;
use Studio24\Frontend\Content\BaseContent;
use Studio24\Frontend\Content\Field\Boolean;
use Studio24\Frontend\Content\Field\Component;
use Studio24\Frontend\Content\Field\Date;
use Studio24\Frontend\Content\Field\DateTime;
use Studio24\Frontend\Content\Field\FlexibleContent;
use Studio24\Frontend\Content\Field\Image;
use Studio24\Frontend\Content\Field\PlainText;
use Studio24\Frontend\Content\Field\Relation;
use Studio24\Frontend\Content\Field\RichText;
use Studio24\Frontend\Content\Field\ShortText;
use Studio24\Frontend\Content\Page;
use Studio24\Frontend\Content\PageCollection;
use Studio24\Frontend\Content\User;
use Studio24\Frontend\ContentModel\ContentModel;
use Studio24\Frontend\ContentModel\ContentType;
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
     * Content model
     *
     * @var ContentModel
     */
    protected $contentModel;

    /**
     * Current content type
     *
     * @var ContentType
     */
    protected $contentType;

    /**
     * Constructor
     *
     * @param string $baseUrl API base URI
     * @param ContentModel $contentModel Content model
     * @param string $type Content type
     */
    public function __construct(string $baseUrl = '', ContentModel $contentModel = null, string $type = null)
    {
        $this->api = new WordpressApi($baseUrl);

        if ($contentModel instanceof ContentModel) {
            $this->setContentModel($contentModel);
        }
        if ($type !== null) {
            $this->setContentType($type);
        }
    }

    /**
     * Set the content model
     *
     * @param ContentModel $contentModel
     * @return Wordpress Fluent interface
     */
    public function setContentModel(ContentModel $contentModel): Wordpress
    {
        $this->contentModel = $contentModel;

        return $this;
    }

    /**
     * Return the content model
     *
     * @return ContentModel
     */
    public function getContentModel(): ContentModel
    {
        return $this->contentModel;
    }

    /**
     * Set HTTP client
     *
     * Useful for testing
     *
     * @param Client $client
     * @return Wordpress Fluent interface
     */
    public function setClient(Client $client): Wordpress
    {
        $this->api->setClient($client);

        return $this;
    }

    /**
     * Does the content type exist?
     *
     * @param string $type
     * @return bool
     */
    public function contentTypeExists(string $type): bool
    {
        return $this->contentModel->hasContentType($type);
    }

    /**
     * Do we have a valid content type and content model set?
     *
     * @return bool
     */
    public function hasContentType(): bool
    {
        if ($this->contentModel instanceof ContentModel && $this->contentType instanceof ContentType) {
            return true;
        }
        return false;
    }

    /**
     * Set the requested content type
     *
     * @param string $type
     * @return Wordpress
     */
    public function setContentType(string $type): Wordpress
    {
        if ($this->contentTypeExists($type)) {
            $this->contentType = $this->getContentModel()->getContentType($type);
        }

        return $this;
    }

    /**
     * Return the current content type
     *
     * @return ContentType
     * @throws ContentTypeNotSetException
     */
    public function getContentType(): ContentType
    {
        if (!$this->hasContentType()) {
            throw new ContentTypeNotSetException('Content type is not set!');
        }
        return $this->contentType;
    }

    /**
     * Return the content type API endpoint
     *
     * @return string
     * @throws ContentTypeNotSetException
     */
    public function getContentApiEndpoint(): string
    {
        if (!$this->hasContentType()) {
            throw new ContentTypeNotSetException('Content type is not set!');
        }

        return $this->getContentType()->getApiEndpoint();
    }

    /**
     * Return list of pages
     *
     * @see https://developer.wordpress.org/rest-api/reference/pages/#list-pages
     *
     * @param int $page Page number, default = 1
     * @param array $options Array of options to select data from WordPress
     * @return PageCollection;
     * @throws ContentTypeNotSetException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Studio24\Frontend\Exception\ContentFieldException
     * @throws \Studio24\Frontend\Exception\FailedRequestException
     * @throws \Studio24\Frontend\Exception\PermissionException
     * @throws \Studio24\Frontend\Exception\PaginationException
     */
    public function listPages(
        int $page = 1,
        array $options = []
    ): PageCollection {

        // @todo Need to add unique identifier for this data based on options array
        $cacheKey = sprintf('%s.list.%s', $this->getContentType()->getName(), $page);
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
     * @throws ContentTypeNotSetException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Studio24\Frontend\Exception\ContentFieldException
     * @throws \Studio24\Frontend\Exception\FailedRequestException
     * @throws \Studio24\Frontend\Exception\PermissionException
     */
    public function getPage(int $id): Page
    {
        $cacheKey = sprintf('%s.%s', $this->getContentType(), $id);
        if ($this->hasCache() && $this->cache->has($cacheKey)) {
            $page = $this->cache->get($cacheKey);
            return $page;
        }

        // Get content
        $data = $this->api->getPost($this->getContentApiEndpoint(), $id);
        $page = $this->createPage($data);

        if (!empty($data['author'])) {
            $author = $this->api->getAuthor($data['author']);
            $page->setAuthor($this->createUser($author));
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
     * @throws ContentTypeNotSetException
     * @throws \Studio24\Frontend\Exception\ContentFieldException
     */
    public function createPage(array $data): Page
    {
        $page = new Page();
        $page->setContentType($this->getContentType());
        $this->setContentFields($page, $data);

        return $page;
    }


    /**
     * Sets content from data array into the content object
     *
     * @param BaseContent $page
     * @param array $data
     * @return BaseContent
     * @throws ContentTypeNotSetException
     * @throws \Studio24\Frontend\Exception\ContentFieldException
     */
    public function setContentFields(BaseContent $page, array $data): ?BaseContent
    {
        if (empty($data)) {
            return null;
        }
        $page->setId(FieldFinder::id($data));
        $page->setTitle(FieldFinder::title($data));
        $page->setDatePublished(FieldFinder::datePublished($data));
        $page->setDateModified(FieldFinder::dateModified($data));
        $page->setStatus(FieldFinder::status($data));


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

        if (isset($data['acf'])) {
            $this->setCustomContentFields($this->getContentType(), $page, $data['acf']);
        }

        return $page;
    }

    /**
     * Build up custom content fields from content model definition
     *
     * @param ContentType $contentType
     * @param ContentInterface $content
     * @param array $data
     * @return BaseContent
     * @throws ContentTypeNotSetException
     * @throws \Studio24\Frontend\Exception\ContentFieldException
     */
    public function setCustomContentFields(ContentType $contentType, ContentInterface $content, array $data): ContentInterface
    {
        foreach ($contentType as $contentField) {

            $name = $contentField->getName();
            if (!isset($data[$name])) {
                continue;
            }

            $value = $data[$name];

            switch ($contentField->getType()) {
                case 'text':
                    $content->addContent(new ShortText($name, $value));
                    break;

                case 'plaintext':
                    $content->addContent(new PlainText($name, $value));
                    break;

                case 'richtext':
                    $content->addContent(new RichText($name, $value));
                    break;

                case 'date':
                    $content->addContent(new Date($name, $value));
                    break;

                case 'datetime':
                    $content->addContent(new DateTime($name, $value));
                    break;

                case 'boolean':
                    $content->addContent(new Boolean($name, $value));
                    break;

                case 'image':
                    $image = new Image(
                        $name,
                        $value['url'],
                        $value['title'],
                        $value['caption'],
                        $value['alt']
                    );

                    // Add sizes
                    $availableSizes = $contentField->getOption('image_sizes');
                    if ($availableSizes !== null) {
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
                    }
                    $content->addContent($image);
                    break;

                case 'document':

                    // Read document data from Media API
                    if (is_numeric($value)) {
                        // @todo
                    }

                    break;
                    
                // @todo array, document, video, audio

                case 'array':
                    $arrayField = new ArrayContent($name);



                    break;

                case 'relation':
                    $relation = new Relation($name);
                    $this->setContentFields($relation->getContent(), $value);
                    $content->addContent($relation);
                    break;

                /**
                 * @todo Build & test Flexible content field
                case 'flexible':
                    if (!is_array($value)) {
                        continue;
                    }

                    $flexible = new FlexibleContent($name);

                    foreach ($contentField as $componentType) {
                        $component = new Component($componentType->getName());
                        $this->setCustomContentFields($componentType, $component, $value);
                        $flexible->addComponent($component);
                    }

                    $content->addContent($flexible);
                    break;
                 */
            }
        }

        return $content;
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
