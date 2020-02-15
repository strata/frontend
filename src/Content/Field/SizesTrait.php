<?php

declare(strict_types=1);

namespace Strata\Frontend\Content\Field;

/**
 * Common code for managing image thumbnail sizes for an asset
 *
 * @package Strata\Frontend\Content\Field
 */
trait SizesTrait
{

    /**
     * Image sizes collection
     *
     * @var ImageSizeCollection
     */
    protected $sizes;

    /**
     * Add an image size
     *
     * @param string $url URL to image
     * @param int|null $width Width of image
     * @param int|null $height Height of image
     * @param string|null $name Name used to identify image size
     * @return Image
     */
    public function addSize(string $url, int $width = null, int $height = null, string $name = null): Image
    {
        $this->getSizes()->addItem(new ImageSize($url, $width, $height, $name));
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
     * @return AssetField
     */
    public function setSizes(array $sizes)
    {
        foreach ($sizes as $size) {
            if (empty($size['url'])) {
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
    public function getSizes(): ImageSizeCollection
    {
        if (!($this->sizes instanceof ImageSizeCollection)) {
            $this->sizes = new ImageSizeCollection();
        }

        return $this->sizes;
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
            if (
                ($size->getWidth() == $width) &&
                ($size->getHeight() == $height)
            ) {
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
}
