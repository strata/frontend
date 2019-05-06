<?php
declare(strict_types=1);

namespace Studio24\Frontend\Cms;

use GuzzleHttp\Client;
use Studio24\Frontend\Content\ContentInterface;
use Studio24\Frontend\Content\Field\ArrayContent;
use Studio24\Frontend\Content\Field\AssetField;
use Studio24\Frontend\Content\Field\Audio;
use Studio24\Frontend\Content\Field\Decimal;
use Studio24\Frontend\Content\Field\PlainArray;
use Studio24\Frontend\Content\Field\TaxonomyTerms;
use Studio24\Frontend\Content\Field\Video;
use Studio24\Frontend\Content\Field\ContentField;
use Studio24\Frontend\Content\Field\ContentFieldCollection;
use Studio24\Frontend\Content\Field\ContentFieldInterface;
use Studio24\Frontend\Content\Field\Document;
use Studio24\Frontend\Content\Head;
use Studio24\Frontend\Content\Menus\MenuItem;
use Studio24\Frontend\Content\Menus\Menu;
use Studio24\Frontend\Content\Taxonomies\Term;
use Studio24\Frontend\Content\Taxonomies\TermCollection;
use Studio24\Frontend\ContentModel\ContentFieldCollectionInterface;
use Studio24\Frontend\ContentModel\Field;
use Studio24\Frontend\Exception\ApiException;
use Studio24\Frontend\Exception\NotFoundException;
use Studio24\Frontend\Exception\ContentFieldException;
use Studio24\Frontend\Exception\ContentFieldNotSetException;
use Studio24\Frontend\Exception\ContentTypeNotSetException;
use Studio24\Frontend\Content\BaseContent;
use Studio24\Frontend\Content\Field\Boolean;
use Studio24\Frontend\Content\Field\Component;
use Studio24\Frontend\Content\Field\Date;
use Studio24\Frontend\Content\Field\DateTime;
use Studio24\Frontend\Content\Field\FlexibleContent;
use Studio24\Frontend\Content\Field\Image;
use Studio24\Frontend\Content\Field\Number;
use Studio24\Frontend\Content\Field\PlainText;
use Studio24\Frontend\Content\Field\Relation;
use Studio24\Frontend\Content\Field\RichText;
use Studio24\Frontend\Content\Field\ShortText;
use Studio24\Frontend\Content\Field\RelationArray;
use Studio24\Frontend\Content\Page;
use Studio24\Frontend\Content\PageCollection;
use Studio24\Frontend\Content\User;
use Studio24\Frontend\ContentModel\ContentModel;
use Studio24\Frontend\ContentModel\ContentType;
use Studio24\Frontend\ContentModel\FieldInterface;
use Studio24\Frontend\Api\Providers\Wordpress as WordpressApi;
use Studio24\Frontend\Utils\FileInfoFormatter;
use Studio24\Frontend\Utils\WordpressFieldFinder as FieldFinder;

/**
 * Class to manage access to Wordpress API and returns well-formed content objects
 *
 * This class is also responsible for caching results
 *
 * @todo This class needs a review to extract different purposes into different classes. Do this when integrate 2nd CMS data source
 *
 * @package Studio24\Frontend\Cms
 */
class Wordpress extends ContentRepository
{
    /**
     * API
     *
     * @var WordpressApi
     */
    protected $api;

    /**
     * Constructor
     *
     * @param string $baseUrl API base URI
     * @param ContentModel $contentModel Content model
     */
    public function __construct(string $baseUrl = '', ContentModel $contentModel = null)
    {
        $this->api = new WordpressApi($baseUrl);

        if ($contentModel instanceof ContentModel) {
            $this->setContentModel($contentModel);
        }
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

        $cacheKey = $this->buildCacheKey($this->getContentType()->getName(), 'list', $options, $page);

        if ($this->hasCache()) {
            $data = $this->cache->get($cacheKey, false);
            if ($data !== false) {
                return $data;
            }
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
            $this->cache->set($cacheKey, $pages, $this->getCacheLifetime());
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
        $cacheKey = $this->getCacheKey($this->getContentType()->getName(), $id);

        if ($this->hasCache()) {
            $data = $this->cache->get($cacheKey, false);
            if ($data !== false) {
                return $data;
            }
        }

        // Get content
        $data = $this->api->getPost($this->getContentApiEndpoint(), $id);
        $page = $this->createPage($data);

        if (!empty($data['author'])) {
            $this->api->ignoreErrorCode(404);
            $author = $this->api->getAuthor($data['author']);
            $this->api->restoreDefaultIgnoredErrorCodes();
            $page->setAuthor($this->createUser($author));
        }

        if ($this->hasCache()) {
            $this->cache->set($cacheKey, $page, $this->getCacheLifetime());
        }

        return $page;
    }

    /**
     * Backward-compatibility for getPageBySlug() method
     *
     * @param string $slug
     * @return Page
     */
    public function getPageBySlug(string $slug)
    {
        return $this->getPageByUrl('/' . trim($slug, '/') . '/');
    }

    /**
     * Return page based on slug
     *
     * @param string $url URL to select for this page (excluding domain)
     * @return Page
     * @throws ApiException
     * @throws ContentFieldException
     * @throws ContentTypeNotSetException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Studio24\Frontend\Exception\FailedRequestException
     * @throws \Studio24\Frontend\Exception\PaginationException
     * @throws \Studio24\Frontend\Exception\PermissionException
     */
    public function getPageByUrl(string $url)
    {
        $cacheKey = $this->getCacheKey($this->getContentType()->getName(), $url);

        if ($this->hasCache()) {
            $data = $this->cache->get($cacheKey, false);
            if ($data !== false) {
                return $data;
            }
        }

        // Get end part of page slug which should allow us to retrieve page in WordPress
        $parts = parse_url($url);
        $slug = rtrim($parts['path'], '/');
        $slug = explode('/', $slug);
        $slug = end($slug);

        // Get content
        $results = $this->api->listPosts($this->getContentApiEndpoint(), 1, ['slug' => $slug]);
        if ($results->getPagination()->getTotalResults() != 1) {
            throw new NotFoundException(sprintf('Page not found for requested URL: %s, slug: %s', $url, $slug), 404);
        }

        // Get single result
        $data = $results->getResponseData()[0];

        // Check this page matches requested URL, if not return 404
        $pageUrlParts = parse_url($data['link']);
        if (rtrim($pageUrlParts['path'], '/') != rtrim($parts['path'], '/')) {
            throw new NotFoundException(sprintf('Page URL %s does not match for requested URL: %s, slug: %s', $pageUrlParts['path'], $url, $slug), 400);
        }

        $page = $this->createPage($data);

        if (!empty($data['author'])) {
            $this->api->ignoreErrorCode(404);
            $author = $this->api->getAuthor($data['author']);
            $this->api->restoreDefaultIgnoredErrorCodes();
            $page->setAuthor($this->createUser($author));
        }

        if ($this->hasCache()) {
            $this->cache->set($cacheKey, $page, $this->getCacheLifetime());
        }

        return $page;
    }

    /**
     * Return media data from API
     *
     * @param int $id ID of media item to retrieve
     * @return array|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Studio24\Frontend\Exception\ContentFieldException
     * @throws \Studio24\Frontend\Exception\FailedRequestException
     * @throws \Studio24\Frontend\Exception\PermissionException
     */
    public function getMediaDataById(int $id): ?array
    {
        $cacheKey = $this->buildCacheKey('media', $id);

        if ($this->hasCache()) {
            $data = $this->cache->get($cacheKey, false);
            if ($data !== false) {
                return $data;
            }
        }

        // Get data from API
        $media_data = $this->api->getMedia($id);
        if (empty($media_data)) {
            return null;
        }

        if ($this->hasCache()) {
            $this->cache->set($cacheKey, $media_data, $this->getCacheLifetime());
        }

        return $media_data;
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

        $this->setMetaTagsAndTitle($page, $data);

        return $page;
    }

    /**
     * @param Page $page
     * @param array $data
     * @throws ContentTypeNotSetException
     * @throws \Studio24\Frontend\Exception\MetaTagNotAllowedException
     */
    public function setMetaTagsAndTitle(Page $page, array $data)
    {
        //default page title, description and featured image
        $title = $page->getTitle();

        if (!empty($this->getContentModel()->getGlobal('site_name'))) {
            $title .= ' | '.$this->getContentModel()->getGlobal('site_name');
        }

        $description = strip_tags($page->getExcerpt());
        $postImage = null;

        if (!empty($page->getFeaturedImage())) {
            $postImage = $page->getFeaturedImage()->getUrl();
        }

        //override default title and description by those set via Yoast
        if (isset($data['yoast'])) {
            if (isset($data['yoast']['title'])) {
                if (!empty($data['yoast']['title'])) {
                    $title = $data['yoast']['title'];
                }
            }

            if (isset($data['yoast']['metadesc'])) {
                if (!empty($data['yoast']['metadesc'])) {
                    $description = strip_tags($data['yoast']['metadesc']);
                }
            }
        }

        //set page title and meta description tags
        $page->getHead()->setTitle($title);
        $page->getHead()->addMeta("description", $description);

        if (!empty($data['yoast']['metakeywords'])) {
            $page->getHead()->addMeta('keywords', $data['yoast']['metakeywords']);
        }

        //meta robots tags
        if (!empty($data['yoast']['meta-robots-noindex']) || !empty($data['yoast']['meta-robots-nofollow'])) {
            $noindex = $data['yoast']['meta-robots-noindex'];
            $nofollow = $data['yoast']['meta-robots-nofollow'];
            $glue = "";
            if (!empty($noindex) && !empty($nofollow)) {
                $glue=", ";
            }
            $page->getHead()->addMeta("robots", $noindex . $glue . $nofollow);
        }

        //twitter card tags
        if (!empty($data['yoast']['twitter-title'])) {
            $page->getHead()->addMeta("twitter:title", $data['yoast']['twitter-title']);
        } else {
            $page->getHead()->addMeta("twitter:title", $title);
        }

        if (!empty($data['yoast']['twitter-description'])) {
            $page->getHead()->addMeta("twitter:description", $data['yoast']['twitter-description']);
        } else {
            $page->getHead()->addMeta("twitter:description", $description);
        }

        if (!empty($data['yoast']['twitter-image'])) {
            $page->getHead()->addMeta("twitter:image", $data['yoast']['twitter-image']);
        } elseif (!empty($postImage)) {
            $page->getHead()->addMeta("twitter:image", $postImage);
        }

        //opengraph tags
        if (!empty($data['yoast']['opengraph-title'])) {
            $page->getHead()->addMeta("og:title", $data['yoast']['opengraph-title']);
        } else {
            $page->getHead()->addMeta("og:title", $title);
        }

        if (!empty($data['yoast']['opengraph-description'])) {
            $page->getHead()->addMeta("og:description", $data['yoast']['opengraph-description']);
        } else {
            $page->getHead()->addMeta("og:description", $description);
        }

        if (!empty($data['yoast']['opengraph-image'])) {
            $page->getHead()->addMeta("og:image", $data['yoast']['opengraph-image']);
        } elseif (!empty($postImage)) {
            $page->getHead()->addMeta("og:image", $postImage);
        }
    }

    /**
     * Sets content from data array into the content object
     *
     * @param BaseContent $page
     * @param array $data
     * @return null
     * @throws ContentFieldNotSetException
     * @throws ContentTypeNotSetException
     * @throws \Studio24\Frontend\Exception\ContentFieldException
     */
    public function setContentFields(BaseContent $page, array $data)
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

        if (!empty(FieldFinder::featuredImage($data))) {
            $this->setFeaturedImage($page, FieldFinder::featuredImage($data));
        }

        // Default WordPress content field
        if (!empty(FieldFinder::content($data))) {
            $page->addContent(new RichText('content', FieldFinder::content($data)));
        }

        // ACF content fields
        if (isset($data['acf']) && is_array($data['acf'])) {
            $this->setCustomContentFields($this->getContentType(), $page, $data['acf']);
        }

        //taxonomy terms
        $validTaxonomies = $this->getContentType()->getTaxonomies();

        if (!empty($validTaxonomies)) {
            $this->setPageTaxonomies($validTaxonomies, $page, $data);
        }
    }

    /**
     * @param ContentInterface $page
     * @param $mediaID
     * @return ContentInterface
     * @throws ContentFieldException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Studio24\Frontend\Exception\FailedRequestException
     * @throws \Studio24\Frontend\Exception\PermissionException
     */
    public function setFeaturedImage(ContentInterface $page, $mediaID)
    {
        if (empty($mediaID) || !is_numeric($mediaID) || is_float($mediaID)) {
            return $page;
        }

        //image ID passed on
        $this->api->ignoreErrorCode(404);
        $field_data = $this->getMediaDataById($mediaID);
        $this->api->restoreDefaultIgnoredErrorCodes();
        if (empty($field_data)) {
            return $page;
        }

        // Add sizes
        $sizesData = [];
        $availableSizes = $this->getContentModel()->getGlobal('image_sizes');
        if ($availableSizes !== null) {
            foreach ($availableSizes as $sizeName) {
                if (isset($field_data['media_details']['sizes'][$sizeName])) {
                    array_push(
                        $sizesData,
                        array(
                            'url' => $field_data['media_details']['sizes'][$sizeName]['source_url'],
                            'width' => $field_data['media_details']['sizes'][$sizeName]['width'],
                            'height' => $field_data['media_details']['sizes'][$sizeName]['height'],
                            'name' => $sizeName
                        )
                    );
                }
            }
        }

        $image = new Image(
            'featured_image',
            $field_data['source_url'],
            $field_data['title']['rendered'],
            $field_data['caption']['rendered'],
            $field_data['alt_text'],
            $sizesData
        );

        $page->setFeaturedImage($image);

        return $page;
    }

    /**
     * Build up custom content fields from content model definition
     *
     * @param ContentType $contentType
     * @param ContentInterface $content
     * @param array $data
     * @return BaseContent
     * @throws ContentFieldNotSetException
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
            $contentField = $this->getContentField($contentField, $value);
            if ($contentField !== null) {
                $content->addContent($contentField);
            }
        }

        return $content;
    }

    /**
     * Return a content field populated with passed data
     *
     * @param FieldInterface $field Content field definition
     * @param mixed $value Content field value
     * @return ContentFieldInterface Populated content field object, or null on failure
     * @throws ContentFieldException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getContentField(FieldInterface $field, $value): ?ContentFieldInterface
    {
        try {
            $name = $field->getName();
            switch ($field->getType()) {
                case 'text':
                    return new ShortText($name, (string) $value);
                    break;

                case 'plaintext':
                    return new PlainText($name, (string) $value);
                    break;

                case 'richtext':
                    return new RichText($name, (string) $value);
                    break;

                case 'number':
                    return new Number($name, $value);
                    break;

                case 'decimal':
                    $precision = $field->getOption('precision', $this->getContentModel());
                    $round = $field->getOption('round', $this->getContentModel());
                    return new Decimal($name, $value, $precision, $round);
                    break;

                case 'date':
                    return new Date($name, $value);
                    break;

                case 'datetime':
                    return new DateTime($name, $value);
                    break;

                case 'boolean':
                    return new Boolean($name, $value);
                    break;

                case 'plainarray':
                    if (!is_array($value)) {
                        return null;
                    }
                    return new PlainArray($name, $value);

                case 'image':
                    $sizesData = array();
                    if (is_int($value)) {
                        //image ID passed on
                        $field_data = $this->getMediaDataById($value);

                        // Add sizes
                        $availableSizes = $field->getOption('image_sizes', $this->getContentModel());
                        if ($availableSizes !== null) {
                            foreach ($availableSizes as $sizeName) {
                                if (isset($field_data['media_details']['sizes'][$sizeName])) {
                                    array_push(
                                        $sizesData,
                                        array(
                                        'url' => $field_data['media_details']['sizes'][$sizeName]['source_url'],
                                        'width' => $field_data['media_details']['sizes'][$sizeName]['width'],
                                        'height' => $field_data['media_details']['sizes'][$sizeName]['height'],
                                        'name' => $sizeName
                                        )
                                    );
                                }
                            }
                        }

                        $image = new Image(
                            $name,
                            $field_data['source_url'],
                            $field_data['title']['rendered'],
                            $field_data['caption']['rendered'],
                            $field_data['alt_text'],
                            $sizesData
                        );

                        return $image;
                    } elseif (is_array($value)) {
                        //image array passed on

                        if (empty($value)) {
                            return null;
                        }

                        // Add sizes
                        $availableSizes = $field->getOption('image_sizes', $this->getContentModel());
                        if ($availableSizes !== null) {
                            foreach ($availableSizes as $sizeName) {
                                if (isset($value['sizes'][$sizeName])) {
                                    array_push(
                                        $sizesData,
                                        array(
                                        'url' => $value['sizes'][$sizeName],
                                        'width' => $value['sizes'][$sizeName.'-width'],
                                        'height' => $value['sizes'][$sizeName.'-height'],
                                        'name' => $sizeName
                                        )
                                    );
                                }
                            }
                        }

                        $image = new Image(
                            $name,
                            $value['url'],
                            $value['title'],
                            $value['caption'],
                            $value['alt'],
                            $sizesData
                        );

                        return $image;
                    }
                    break;

                case 'document':
                    //given an attachment, request data and create field
                    if (is_int($value)) {
                        $field_data = $this->getMediaDataById($value);

                        $filesize = $this->api->getMediaFileSize($field_data['source_url']);

                        $document = new Document(
                            $name,
                            $field_data['source_url'],
                            $filesize,
                            $field_data['title']['rendered'],
                            $field_data['alt_text']
                        );

                        return $document;
                    } elseif (is_array($value)) {
                        //given array of data, create field directy
                        if (isset($value['filesize'])) {
                            $filesize = FileInfoFormatter::formatFileSize($value['filesize']);
                        } else {
                            $filesize = $this->api->getMediaFileSize($value['url']);
                        }

                        $document = new Document(
                            $name,
                            $value['url'],
                            $filesize,
                            $value['title'],
                            $value['alt']
                        );

                        return $document;
                    } else {
                        return null;
                    }

                    break;
                case 'video':
                    $media_id = null;

                    if (is_int($value)) {
                        $media_id = $value;
                    } elseif (is_array($value)) {
                        $media_id = $value['id'];
                    } else {
                        return null;
                    }

                    $field_data = $this->getMediaDataById($media_id);

                    $filesize = FileInfoFormatter::formatFileSize($field_data['media_details']['filesize']);

                    $video = new Video(
                        $name,
                        $field_data['source_url'],
                        $filesize,
                        $field_data['media_details']['bitrate'],
                        $field_data['media_details']['length_formatted'],
                        $field_data['title']['rendered'],
                        $field_data['alt_text']
                    );

                    return $video;

                break;

                case 'audio':
                    $media_id = null;

                    if (is_int($value)) {
                        $media_id = $value;
                    } elseif (is_array($value)) {
                        $media_id = $value['id'];
                    } else {
                        return null;
                    }

                    $field_data = $this->getMediaDataById($media_id);

                    $filesize = FileInfoFormatter::formatFileSize($field_data['media_details']['filesize']);

                    $audio = new Audio(
                        $name,
                        $field_data['source_url'],
                        $filesize,
                        $field_data['media_details']['bitrate'],
                        $field_data['media_details']['length_formatted'],
                        $field_data['media_details'],
                        $field_data['title']['rendered'],
                        $field_data['alt_text']
                    );

                    return $audio;

                break;

                case 'array':
                    $array = new ArrayContent($name);

                    if (!is_array($value)) {
                        break;
                    }

                    if (empty($value)) {
                        break;
                    }

                    // Loop through data array
                    foreach ($value as $row) {
                        // For each row add a set of content fields
                        $item = new ContentFieldCollection();
                        foreach ($field as $childField) {
                            if (!isset($row[$childField->getName()])) {
                                continue;
                            }
                            $childValue = $row[$childField->getName()];
                            $contentField = $this->getContentField($childField, $childValue);
                            if ($contentField !== null) {
                                $item->addItem($contentField);
                            }
                        }
                        $array->addItem($item);
                    }

                    return $array;
                    break;

                case 'relation':
                    if (!is_array($value) || !$field->hasOption('content_type')) {
                        break;
                    }

                    // Swap to relation content type
                    $currentContentType = $this->getContentType()->getName();

                    $relation = new Relation($name);
                    $this->setContentType($field->getOption('content_type'));
                    $this->setContentFields($relation->getContent(), $value);

                    // Swap back to original content type
                    $this->setContentType($currentContentType);

                    return $relation;
                    break;

                case 'relation_array':
                    if (!is_array($value) || !$field->hasOption('content_type')) {
                        break;
                    }

                    // Swap to relation content type
                    $currentContentType = $this->getContentType()->getName();

                    $relationArray = new RelationArray($name, $field->getOption('content_type'));
                    foreach ($value as $row) {
                        $item = new Relation($name);
                        $this->setContentType($field->getOption('content_type'));
                        $this->setContentFields($item->getContent(), $row);
                        $relationArray->addItem($item);
                    }

                    // Swap back to original content type
                    $this->setContentType($currentContentType);

                    return $relationArray;
                    break;

                case 'flexible':
                    if (!is_array($value)) {
                        return null;
                    } elseif (empty($value)) {
                        return null;
                    }

                    $flexible = new FlexibleContent($name);

                    foreach ($value as $componentValue) {
                        if (!isset($field[$componentValue['acf_fc_layout']])) {
                            continue;
                        }

                        $componentName = $componentValue['acf_fc_layout'];

                        if (empty($field[$componentName])) {
                            continue;
                        }

                        $component = new Component($componentName);

                        foreach ($field[$componentName] as $componentFieldItem) {
                            if (!isset($componentValue[$componentFieldItem->getName()])) {
                                continue;
                            }
                                $componentFieldItemValue = $componentValue[$componentFieldItem->getName()];
                                $componentFieldItemObject = $this->getContentField($componentFieldItem, $componentFieldItemValue);
                            if ($componentFieldItemObject !== null) {
                                $component->addContent($componentFieldItemObject);
                            }
                        }

                        $flexible->addComponent($component);
                    }

                    return $flexible;
                    break;

                case 'taxonomyterms':
                    //@todo cater for situation in which term ID is returned as opposed to term object
                    //can receive single term or array of terms
                    if (!is_array($value)) {
                        return null;
                    }
                    if (empty($value)) {
                        return null;
                    }

                    $terms = new TermCollection();
                    if (isset($value['term_id'])) {
                        //we've got a single term, not an array of terms
                        $termsData = array($value);
                    } else {
                        $termsData = $value;
                    }

                    foreach ($termsData as $singleTermData) {
                        $link = $singleTermData['taxonomy'].'/'.$singleTermData['slug'];
                        $currentTerm = new Term(
                            $singleTermData['term_id'],
                            $singleTermData['name'],
                            $singleTermData['slug'],
                            $link,
                            $singleTermData['count'],
                            $singleTermData['description']
                        );
                        $terms->addItem($currentTerm);
                    }

                    $taxonomyTermField = new TaxonomyTerms($name);
                    $taxonomyTermField->setContent($terms);

                    return $taxonomyTermField;
                    break;
            }
        } catch (\Error $e) {
            $message = sprintf("Fatal error when creating content field '%s' (type: %s) for value: %s", $field->getName(), $field->getType(), print_r($value, true));
            throw new ContentFieldException($message, 0, $e);
        } catch (\Exception $e) {
            $message = sprintf("Exception thrown when creating content field '%s' (type: %s) for value: %s", $field->getName(), $field->getType(), print_r($value, true));
            throw new ContentFieldException($message, 0, $e);
        }

        return null;
    }

    public function setPageTaxonomies(array $validTaxonomies, BaseContent $page, array $data)
    {
        $taxonomies = array();

        if (empty($validTaxonomies)) {
            return;
        }

        foreach ($validTaxonomies as $taxonomyName) {
            if (!isset($data[$taxonomyName])) {
                continue;
            } elseif (empty($data[$taxonomyName])) {
                continue;
            }

            $taxonomies[$taxonomyName] = new TermCollection();

            foreach ($data[$taxonomyName] as $termID) {
                $term = $this->createTerm($taxonomyName, $termID);
                if ($term == null) {
                    continue;
                }
                $taxonomies[$taxonomyName]->addItem($term);
            }
        }

        $page->setTaxonomies($taxonomies);
    }

    /**
     * Generate user object from API data
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): ?User
    {
        if (empty($data)) {
            return null;
        }
        $user = new User();
        $user->setId($data['id'])
            ->setName($data['name']);
        if (!empty($data['description'])) {
            $user->setBio($data['description']);
        }
        return $user;
    }


    public function getMenu(int $id)
    {
        $cacheKey = $this->buildCacheKey('menu', $id);

        if ($this->hasCache()) {
            $data = $this->cache->get($cacheKey, false);
            if ($data !== false) {
                return $data;
            }
        }

        // Get menu data
        $data = $this->api->getMenu($id);

        if (empty($data)) {
            return null;
        }

        $menu = $this->createMenu($data);

        if ($this->hasCache()) {
            $this->cache->set($cacheKey, $menu, $this->getCacheLifetime());
        }
        
        return $menu;
    }

    private function createMenu($data): Menu
    {
        $menu = new Menu();

        $menu->setId($data['ID']);
        $menu->setName($data['name']);
        $menu->setSlug($data['slug']);
        $menu->setDescription($data['description']);

        $menu = $this->generateMenuItems($data['items'], $menu);

        return $menu;
    }

    /**
     * @param $array
     * @param Menu $menu
     * @return Menu
     */
    private function generateMenuItems($array, $menu)
    {
        $menu = clone $menu;
        foreach ($array as $element) {
            $menuItem = new MenuItem();
            $menuItem->setId($element['id']);
            $menuItem->setUrl($element['url']);
            $menuItem->setLabel($element['title']);

            if (isset($element['children'])) {
                $menu->getChildren()->addItem($this->generateMenuItemChildren($element['children'], $menuItem));
            } else {
                $menu->getChildren()->addItem($menuItem);
            }
        }
        return $menu;
    }
    // TODO refactor these duplicate functions

    /**
     * @param $array
     * @param MenuItem $menuItemParent
     * @return MenuItem
     */
    private function generateMenuItemChildren($array, $menuItemParent)
    {
        $menuItemParent = clone $menuItemParent;
        foreach ($array as $element) {
            $menuItem = new MenuItem();
            $menuItem->setId($element['id']);
            $menuItem->setUrl($element['url']);
            $menuItem->setLabel($element['title']);

            if (isset($element['children'])) {
                $menuItemParent->getChildren()->addItem($this->generateMenuItemChildren($element['children'], $menuItem));
            } else {
                $menuItemParent->getChildren()->addItem($menuItem);
            }
        }
        return $menuItemParent;
    }

    /**
     * Create term object from taxonomy and term id
     *
     * @param string $taxonomy
     * @param int $id
     * @return null|Term
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Studio24\Frontend\Exception\FailedRequestException
     * @throws \Studio24\Frontend\Exception\PermissionException
     */
    public function createTerm(string $taxonomy, int $id): ?Term
    {

        $termData = $this->getTerm($taxonomy, $id);

        if (empty($termData)) {
            return null;
        }

        $term = new Term(
            $termData['id'],
            $termData['name'],
            $termData['slug'],
            $termData['link'],
            $termData['count'],
            $termData['description']
        );

        return $term;
    }

    /**
     * Get term data
     *
     * @param string $taxonomy
     * @param int $id
     * @return array|null
     * @throws ApiException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Studio24\Frontend\Exception\FailedRequestException
     * @throws \Studio24\Frontend\Exception\PermissionException
     */
    public function getTerm(string $taxonomy, int $id): ?array
    {
        $cacheKey = $this->buildCacheKey('term', $taxonomy, $id);

        if ($this->hasCache()) {
            $data = $this->cache->get($cacheKey, false);
            if ($data !== false) {
                return $data;
            }
        }

        $this->api->ignoreErrorCode(404);
        $termData = $this->api->getTerm($taxonomy, $id);
        $this->api->restoreDefaultIgnoredErrorCodes();

        if ($this->hasCache()) {
            $this->cache->set($cacheKey, $termData, $this->getCacheLifetime());
        }

        return $termData;
    }

    /**
     * @param string $taxonomy
     * @return null|TermCollection
     */
    public function getAllTerms(string $taxonomy): ?TermCollection
    {
        //@todo do we need to restrict to a list of allowed taxonomies (defined in config file?)
        $taxonomyTermsData = $this->getAllTermsData($taxonomy);

        if (empty($taxonomyTermsData)) {
            return null;
        }

        $taxonomyTerms = new TermCollection();
        foreach ($taxonomyTermsData as $singleTermData) {
            $currentTerm = new Term(
                $singleTermData['id'],
                $singleTermData['name'],
                $singleTermData['slug'],
                $singleTermData['link'],
                $singleTermData['count'],
                $singleTermData['description']
            );
            $taxonomyTerms->addItem($currentTerm);
        }

        return $taxonomyTerms;
    }

    /**
     * @param string $taxonomy
     * @return array|null
     * @throws ApiException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Studio24\Frontend\Exception\FailedRequestException
     * @throws \Studio24\Frontend\Exception\PermissionException
     */
    public function getAllTermsData(string $taxonomy): ?array
    {
        $cacheKey = $this->buildCacheKey('allterms', $taxonomy);

        if ($this->hasCache()) {
            $data = $this->cache->get($cacheKey, false);

            if (!empty($data)) {
                return $data;
            }
        }

        //ignore 404s, usually a list of terms is used to displayed filters or menus on the side,
        //it's not the core of the page
        $this->api->ignoreErrorCode(404);
        $allTermsData = $this->api->getTaxonomyTerms($taxonomy);
        $this->api->restoreDefaultIgnoredErrorCodes();

        if ($this->hasCache()) {
            $this->cache->set($cacheKey, $allTermsData, $this->getCacheLifetime());
        }

        return $allTermsData;
    }
}
