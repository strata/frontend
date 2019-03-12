<?php
declare(strict_types=1);

namespace Studio24\Frontend\Content;

use Studio24\Frontend\Content\Field\ContentFieldCollection;
use Studio24\Frontend\Content\Field\ContentFieldInterface;
use Studio24\Frontend\Content\Field\DateTime;
use Studio24\Frontend\Content\Field\Image;
use Studio24\Frontend\ContentModel\ContentType;

class BaseContent implements ContentInterface, AddressableInterface
{
    /**
     * @var
     */
    protected $id;

    /**
     * @var ContentType
     */
    protected $contentType;

    /**
     * @var
     */
    protected $title;

    /**
     * @var string
     */
    protected $urlSlug;

    /**
     * Date published
     *
     * @var DateTime
     */
    protected $datePublished;

    /**
     * Date last modified
     *
     * @var DateTime
     */
    protected $dateModified;

    /**
     * @var
     */
    protected $status;

    /**
     * Content field collection
     *
     * @var ContentFieldCollection
     */
    protected $content;

    /**
     * Image object
     *
     * @var Image
     */
    protected $featuredImage;

    /**
     * Page constructor.
     */
    public function __construct()
    {
        $this->content = new ContentFieldCollection();
        $this->featuredImage = null;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return BaseContent
     */
    public function setId($id): BaseContent
    {
        if (empty($id)) {
            return $this;
        }

        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUrlSlug(): string
    {
        return $this->urlSlug;
    }

    /**
     * @param mixed $slug
     * @return Page
     */
    public function setUrlSlug(? string $slug): Page
    {
        $this->urlSlug = $slug;
        return $this;
    }

    /**
     * Set content type
     *
     * @param ContentType $contentType
     * @return BaseContent
     */
    public function setContentType(ContentType $contentType): BaseContent
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * Return the content type
     *
     * @return ContentType
     */
    public function getContentType(): ContentType
    {
        return $this->contentType;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param $title
     * @return $this
     */
    public function setTitle(?string $title): BaseContent
    {
        if (empty($title)) {
            return $this;
        }

        $this->title = $title;
        return $this;
    }

    /**
     * Get date published of page
     *
     * @return DateTime
     */
    public function getDatePublished() : DateTime
    {
        return $this->datePublished;
    }

    /**
     * Set date published for the page
     *
     * Uses the DateTime content field type
     *
     * @param mixed $datePublished DateTime object or parsable date time, see https://secure.php.net/manual/en/datetime.formats.compound.php
     * @param string $format Date format to create DateTime from, see https://secure.php.net/manual/en/datetime.createfromformat.php
     * @param mixed $timezone DateTimeZone or timezone string
     * @return BaseContent
     * @throws \Studio24\Frontend\Exception\ContentFieldException
     */
    public function setDatePublished($datePublished, $format = null, $timezone = null) : BaseContent
    {
        if (empty($datePublished)) {
            return $this;
        }

        if ($format !== null) {
            $this->datePublished = new DateTime('datePublished', $datePublished, $format);
        } else {
            $this->datePublished = new DateTime('datePublished', $datePublished);
        }

        if ($timezone !== null) {
            if (!$timezone instanceof \DateTimeZone) {
                $timezone = new \DateTimeZone($timezone);
            }
            $this->datePublished->getDateTime()->setTimezone($timezone);
        }

        return $this;
    }

    /**
     * Get last modified date of page
     *
     * @return DateTime
     */
    public function getDateModified() : DateTime
    {
        return $this->dateModified;
    }

    /**
     * Set the last modified date of the page
     *
     * Uses the DateTime content field type
     *
     * @param mixed $dateModified DateTime object or parsable date time, see https://secure.php.net/manual/en/datetime.formats.compound.php
     * @param string $format Date format to create DateTime from, see https://secure.php.net/manual/en/datetime.createfromformat.php
     * @param mixed $timezone DateTimeZone or timezone string
     * @return BaseContent
     * @throws \Studio24\Frontend\Exception\ContentFieldException
     */
    public function setDateModified($dateModified, $format = null, $timezone = null) : BaseContent
    {
        if (empty($dateModified)) {
            return $this;
        }

        if ($format !== null) {
            $this->dateModified = new DateTime('datePublished', $dateModified, $format);
        } else {
            $this->dateModified = new DateTime('datePublished', $dateModified);
        }

        if ($timezone !== null) {
            if (!$timezone instanceof \DateTimeZone) {
                $timezone = new \DateTimeZone($timezone);
            }
            $this->dateModified->getDateTime()->setTimezone($timezone);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param $status
     * @return BaseContent
     */
    public function setStatus(string $status): BaseContent
    {
        if (empty($status)) {
            return $this;
        }

        $this->status = $status;
        return $this;
    }

    /**
     * Return whether the current BaseContentObject is published
     *
     * @return bool
     */
    public function isPublished(): bool
    {
        $publishedStatus = ['publish'];

        return in_array($this->status, $publishedStatus);
    }

    /**
     * Add new content field
     *
     * @param ContentFieldInterface $contentField
     */
    public function addContent(ContentFieldInterface $contentField)
    {
        $this->content->addItem($contentField);
    }

    /**
     * Return collection of content fields
     *
     * @return ContentFieldCollection
     */
    public function getContent() : ContentFieldCollection
    {
        return $this->content;
    }

    /**
     * Return featured image as Image Object (or null)
     *
     * @return Image
     */
    public function getFeaturedImage() : ?Image
    {
        return $this->featuredImage;
    }

    public function setFeaturedImage(Image $featuredImage): BaseContent
    {
        $this->featuredImage = $featuredImage;

        return $this;
    }

    /**
     * Return string representation of the content
     *
     * @return string
     */
    public function __toString()
    {
        $content = '';
        foreach ($this->getContent() as $item) {
            $content .= $item->__toString();
        }
        return $content;
    }
}
