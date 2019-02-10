<?php
declare(strict_types=1);

namespace Studio24\Frontend\Content;

use Studio24\Frontend\Content\Field\ContentFieldCollection;
use Studio24\Frontend\Content\Field\ContentFieldInterface;
use Studio24\Frontend\Content\Field\DateTime;

class Page implements ContentInterface
{
    protected $id;
    protected $title;

    /**
     * Content field collection
     *
     * @var ContentFieldCollection
     */
    protected $content;

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
    protected $urlSlug;
    protected $status;
    protected $contentType;

    protected $excerpt;

    /**
     * @var Url
     */
    protected $urlPattern;

    /**
     * Author user
     *
     * @var User
     */
    protected $author;

    public function __construct()
    {
        $this->content = new ContentFieldCollection();
    }

    /**
     * Set URL pattern
     *
     * @param $urlPattern
     */
    public function setUrlPattern(Url $urlPattern)
    {
        $this->urlPattern = $urlPattern;
    }

    /**
     * Get URL pattern
     *
     * @return Url
     */
    public function getUrlPattern(): Url
    {
        return $this->urlPattern;
    }

    /**
     * Return the full URL to the current page
     *
     * @return string
     * @throws \Studio24\Frontend\Exception\UrlException
     */
    public function getUrl() : string
    {
        return $this->urlPattern->getUrl($this);
    }

    /**
     * @return string
     */
    public function getStatus() : string
    {
        return $this->status;
    }

    /**
     * @param $status
     * @return Page
     */
    public function setStatus(string $status): Page
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Return whether the current page is published
     *
     * @return bool
     */
    public function isPublished() : bool
    {
        $publishedStatus = ['publish'];

        return in_array($this->status, $publishedStatus);
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
     * @return Page
     */
    public function setContentType(string $contentType): Page
    {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * @param $title
     * @return $this
     */
    public function setTitle(string $title) : Page
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle() : string
    {
        return $this->title;
    }

    /**
     * Add new content field
     *
     * @param ContentFieldInterface $content
     * @return Page
     */
    public function addContent(ContentFieldInterface $content) : Page
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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Page
     */
    public function setId($id) : Page
    {
        $this->id = $id;
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
     * @return Page
     * @throws \Studio24\Exception\ContentFieldException
     */
    public function setDatePublished($datePublished, $format = null, $timezone = null) : Page
    {
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
     * @return Page
     * @throws \Studio24\Exception\ContentFieldException
     */
    public function setDateModified($dateModified, $format = null, $timezone = null) : Page
    {
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
     * @return mixed
     */
    public function getUrlSlug() : string
    {
        return $this->urlSlug;
    }

    /**
     * @param mixed $slug
     * @return Page
     */
    public function setUrlSlug(string $slug) : Page
    {
        $this->urlSlug = $slug;
        return $this;
    }

    /**
     * Return excerpt, if not set auto-generates this based on trimmed page content
     *
     * @param int $limit Character length to trim string to
     * @return mixed
     */
    public function getExcerpt(int $limit = 200) : string
    {
        if (empty($this->excerpt)) {
            // @todo grab first 200 chars of page content
            return $this->trimContent('TODO', 200);
        }

        return $this->excerpt;
    }


    /**
     * Return a shorter version of content, cut to word boundaries and stripped of any HTML
     *
     * @param string $content String
     * @param int $limit Character length to trim string to
     * @return string Concatenated string
     */
    public function trimContent(string $content, int $limit = 200) : string
    {
        $content = strip_tags($content);
        $content = trim($content);

        if (preg_match('/^.{1,' . $limit . '}\b/s', $content, $m)) {
            $content = trim($m[0]);
        } else {
            // If preg doesn't work just cut string
            $content = substr($content, 0, $limit);
        }

        return trim($content);
    }

    /**
     * @param mixed $excerpt
     * @return Page
     */
    public function setExcerpt($excerpt) : Page
    {
        $this->excerpt = $excerpt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuthor() : User
    {
        return $this->author;
    }

    /**
     * Set author user
     *
     * @param User $author
     * @return Page
     */
    public function setAuthor(User $author) : Page
    {
        $this->author = $author;
        return $this;
    }
}
