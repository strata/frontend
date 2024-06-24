<?php

declare(strict_types=1);

namespace Strata\Frontend\Content;

use Strata\Frontend\Content\Field\ContentFieldCollection;
use Strata\Frontend\Content\Field\ContentFieldInterface;
use Strata\Frontend\Content\Field\DateTime;
use Strata\Frontend\Content\Field\Image;
use Strata\Frontend\Schema\ContentType;

/**
 * Class to represent base content object
 *
 * Designed to be forgiving, values are null if not set
 * Setters use a fluent interface so you can chain methods
 */
class BaseContent implements ContentInterface, AddressableInterface
{
    protected $id = null;
    protected ContentType $contentType;
    protected ?string $title = null;
    protected ?string $urlSlug = null;
    protected DateTime $datePublished;
    protected DateTime $dateModified;
    protected $status;
    protected ContentFieldCollection $content;
    protected Image $featuredImage;

    /** @var TermCollection[] */
    protected array $taxonomies;

    public function __construct()
    {
        $this->content = new ContentFieldCollection();
        $this->featuredImage = null;
        $this->taxonomies = array();
    }

    /**
     * Return identifier for this content
     * @return string|int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set identifier for this content
     * @param string|int $id
     * @return BaseContent
     */
    public function setId(string|int $id): BaseContent
    {
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
    public function setUrlSlug(?string $slug): BaseContent
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
    public function getDatePublished(): DateTime
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
     * @throws \Strata\Frontend\Exception\ContentFieldException
     */
    public function setDatePublished($datePublished, $format = null, $timezone = null): BaseContent
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
    public function getDateModified(): DateTime
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
     * @throws \Strata\Frontend\Exception\ContentFieldException
     */
    public function setDateModified($dateModified, $format = null, $timezone = null): BaseContent
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
    public function getContent(): ContentFieldCollection
    {
        return $this->content;
    }

    /**
     * Remove a content field
     *
     * @param ContentFieldInterface $contentField
     */
    public function removeContent(ContentFieldInterface $contentField)
    {
        $this->content->removeItem($contentField);
    }

    /**
     * Return featured image as Image Object (or null)
     *
     * @return Image
     */
    public function getFeaturedImage(): ?Image
    {
        return $this->featuredImage;
    }

    /**
     * @param Image $featuredImage
     * @return BaseContent
     */
    public function setFeaturedImage(Image $featuredImage): BaseContent
    {
        $this->featuredImage = $featuredImage;

        return $this;
    }


    /**
     * Return array of TermCollection (one per taxonomy)
     *
     * @return array
     */
    public function getTaxonomies(): array
    {
        return $this->taxonomies;
    }

    /**
     * @param array $taxonomies
     * @return BaseContent
     */
    public function setTaxonomies(array $taxonomies): BaseContent
    {
        $this->taxonomies = $taxonomies;

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
