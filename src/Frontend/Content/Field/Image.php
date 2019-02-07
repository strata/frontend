<?php
declare(strict_types=1);

namespace Studio24\Frontend\Content\Field;

use Studio24\Exception\ContentFieldException;

/**
 * Image content field
 *
 * @package Studio24\Frontend\Content\Field
 */
class Image extends ContentField
{
    const TYPE = 'image';

    protected $url;
    protected $title;
    protected $caption;
    protected $alt;

    /**
     * Image sizes collection
     *
     * @var ImageSizeCollection
     */
    protected $sizes;

    /**
     * Does the content field contain HTML?
     *
     * @return bool
     */
    public function hasHtml() : bool
    {
        return true;
    }

    /**
     * Create image content field
     *
     * @param string $name Content field name
     * @param string $imageUrl Image URL
     * @param string|null $title Image title
     * @param string|null $caption Image caption
     * @param string|null $alt Alt text
     * @param array $sizes Array of alternative image sizes
     * @throws \Studio24\Exception\ContentFieldException
     */
    public function __construct(string $name, string $imageUrl, string $title = null, string $caption = null, string $alt = null, array $sizes = [])
    {
        $this->setName($name);
        $this->setUrl($imageUrl);
        $this->sizes = new ImageSizeCollection();

        if (!empty($title)) {
            $this->setTitle($title);
        }
        if (!empty($caption)) {
            $this->setCaption($caption);
        }
        if (!empty($alt)) {
            $this->setAlt($alt);
        }
        if (!empty($sizes)) {
            $this->setSizes($sizes);
        }
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     * @return Image
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return Image
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * @param mixed $caption
     * @return Image
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * @param mixed $alt
     * @return Image
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;
        return $this;
    }

    /**
     * Add an image size
     *
     * @param string $url URL to image
     * @param int|null $width Width of image
     * @param int|null $height Height of image
     * @param string|null $name Name used to identify image size
     * @return Image
     */
    public function addSize(string $url, int $width = null, int $height = null, string $name = null) : Image
    {
        $this->sizes->addItem(new ImageSize($url, $width, $height, $name));
        return $this;
    }

    /**
     * Set many image sizes at once
     *
     * We're expecting an array formed of the following array keys:
     *   'url' => 'url',
     *   'width' => '',
     *   'height' => '',
     *   'name' => '',
     *
     * @param mixed $sizes
     * @throws ContentFieldException
     * @return Asset
     */
    public function setSizes(array $sizes)
    {
        foreach ($sizes as $size) {
            if (!empty($size['url'])) {
                throw new ContentFieldException("You must set 'url' for the image size");
            }
            $width = $size['width'] ?? null;
            $height = $size['height'] ?? null;
            $name = $size['name'] ?? null;

            $this->addSize($size['url'], $width, $height, $name);
        }
        return $this;
    }

    /**
     * Return collection of all image sizes
     *
     * @return ImageSizeCollection
     */
    public function getSizes() : ImageSizeCollection
    {
        return new $this->sizes;
    }

    /**
     * Get image size by width
     *
     * @param int $width
     * @return string|null Matched image size URL, or null
     */
    public function byWidth(int $width)
    {
        foreach ($this->getSizes() as $size) {
            if ($size->getWidth() == $width) {
                return $size;
            }
        }

        return null;
    }

    /**
     * Get image size by width
     *
     * @param int $height
     * @return string|null Matched image size URL, or null
     */
    public function byHeight(int $height)
    {
        foreach ($this->getSizes() as $size) {
            if ($size->getHeight() == $height) {
                return $size->getUrl();
            }
        }

        return null;
    }

    /**
     * Get image size by width & height
     *
     * @param int $width
     * @param int $height
     * @return string|null Matched image size URL, or null
     */
    public function byWidthHeight(int $width, int $height)
    {
        foreach ($this->getSizes() as $size) {
            if (($size->getWidth() == $width) &&
                ($size->getHeight() == $height)) {
                return $size->getUrl();
            }
        }

        return null;
    }

    /**
     * Get image size by name
     *
     * @param string $name
     * @return string|null Matched image size URL, or null
     */
    public function byName(string $name)
    {
        foreach ($this->getSizes() as $size) {
            if ($size->getName() === $name) {
                return $size->getUrl();
            }
        }

        return null;
    }

    /**
     * Return img URL
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->getUrl();
    }

    /**
     * Return string representation of content field
     *
     * @return string
     */
    public function __toString() : string
    {
        return $this->getUrl();
    }
}