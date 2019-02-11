<?php
declare(strict_types=1);

namespace Studio24\Frontend\Content;

/**
 * Class Page
 * @package Studio24\Frontend\Content
 */
class Page extends BaseContentObject implements ContentInterface
{

    /**
     * @var
     */
    protected $urlSlug;

    /**
     * @var
     */
    protected $excerpt;

    /**
     * Author user
     * @var User
     */
    protected $author;

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
     */
    public function getUrl(): string
    {
        return $this->urlPattern->getUrl($this);
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
     * Return excerpt, if not set auto-generates this based on trimmed page content
     *
     * @param int $limit Character length to trim string to
     * @return mixed
     */
    public function getExcerpt(int $limit = 200): string
    {
        if (empty($this->excerpt)) {
            // @todo grab first 200 chars of page content
            return $this->trimContent(200, 'TODO');
        }

        return $this->excerpt;
    }


    /**
     * Return a shorter version of content, cut to word boundaries and stripped of any HTML
     *
     * @param int $limit Character length to trim string to
     * @param string $content String
     * @return string Concatenated string
     */
    public function trimContent(int $limit = 200, string $content): string
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
    public function setExcerpt($excerpt): Page
    {
        $this->excerpt = $excerpt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        if (!empty($this->author)) {
            return $this->author;
        }

        if ($this->getContent()->offsetExists('author')) {
            return $this->getContent()->offsetGet('author');
        }

        return $this->author;
    }

    /**
     * Set author user
     *
     * @param User $author
     * @return Page
     */
    public function setAuthor($author): Page
    {
        $this->author = $author;
        return $this;
    }


}