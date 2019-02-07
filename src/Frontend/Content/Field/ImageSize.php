<?php
declare(strict_types=1);

namespace Studio24\Frontend\Content\Field;

/**
 * Class to represent different image sizes
 *
 * @package Studio24\Frontend\Content\Field
 */
class ImageSize
{
    protected $name;
    protected $url;
    protected $width;
    protected $height;

    /**
     * Constructor
     *
     * Set either the width/height or name to help find images
     *
     * @param string $url URL to image
     * @param int|null $width Width of image
     * @param int|null $height Height of image
     * @param string|null $name Name used to identify image size
     */
    public function __construct(string $url, int $width = null, int $height = null, string $name = null)
    {
        $this->url = $url;

        if ($width !== null) {
            $this->width = $width;
        }
        if ($height !== null) {
            $this->height = $height;
        }
        if ($name !== null) {
            $this->name = $name;
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ImageSize
     */
    public function setName(string $name): ImageSize
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return ImageSize
     */
    public function setUrl(string $url): ImageSize
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param mixed $width
     * @return Asset
     */
    public function setWidth($width): ImageSize
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param mixed $height
     * @return Asset
     */
    public function setHeight($height): ImageSize
    {
        $this->height = $height;
        return $this;
    }

}