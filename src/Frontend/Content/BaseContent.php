<?php
declare(strict_types=1);

namespace Studio24\Frontend\Content;

use Studio24\Frontend\Content\Field\ContentFieldCollection;
use Studio24\Frontend\Content\Field\ContentFieldInterface;
use Studio24\Frontend\Content\Field\DateTime;

class BaseContent implements ContentInterface
{
    /**
     * @var
     */
    protected $id;

    /**
     * @var
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
     * Page constructor.
     */
    public function __construct()
    {
        $this->content = new ContentFieldCollection();
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
     * @return string
     */
    public function getContentType() : string
    {
        return $this->contentType;
    }

    /**
     * @param mixed $contentType
     * @return BaseContent
     */
    public function setContentType(string $contentType): BaseContent
    {
        $this->contentType = $contentType;
        return $this;
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
     * @throws \Studio24\Exception\ContentFieldException
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
     * @throws \Studio24\Exception\ContentFieldException
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
     * @param ContentFieldInterface $content
     * @return BaseContent
     */
    public function addContent(ContentFieldInterface $content) : BaseContent
    {
        $this->content->addItem($content);
        return $this;
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
