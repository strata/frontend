<?php
declare(strict_types=1);

namespace Studio24\Frontend\Cms;

use Studio24\Frontend\Content\BaseContent;
use Studio24\Frontend\Content\Page;
use Studio24\Frontend\Content\PageCollection;
use Studio24\Frontend\ContentModel\ContentModel;
use Studio24\Frontend\ContentModel\ContentType;
use Studio24\Frontend\Traits\CacheTrait;

/**
 * Base class for CMS functionality
 *
 * Purpose is to read content from API and map to content objects
 *
 * @package Studio24\Frontend\Cms
 */
abstract class ContentRepository
{
    use CacheTrait;

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
     * Set the content model
     *
     * @param ContentModel $contentModel
     * @return ContentRepository Fluent interface
     */
    public function setContentModel(ContentModel $contentModel): ContentRepository
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
     * Set the requested content type
     *
     * @param string $type
     * @return ContentRepository
     */
    public function setContentType(string $type): ContentRepository
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
     * Create a new page object
     *
     * This represents a content object (e.g. news article, page, case study, etc)
     *
     * @param array $data
     * @return Page
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
     * @param BaseContent $page Content object to add fields to
     * @param array $data Array of data to set
     * @return null
     */
    abstract public function setContentFields(BaseContent $page, array $data);
}
