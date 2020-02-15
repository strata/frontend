<?php

declare(strict_types=1);

namespace Studio24\Frontend\Content\Field;

use Studio24\Frontend\Exception\ContentFieldException;

/**
 * Image content field
 *
 * @package Studio24\Frontend\Content\Field
 */
class Image extends AssetField
{
    use SizesTrait;

    const TYPE = 'image';

    public static $allowedMimeTypes = [
        'image/jpeg',
        'image/gif',
        'image/png',
        'image/bmp',
        'image/tiff',
        'image/x-icon',
    ];

    protected $alt;

    /**
     * Create image content field
     *
     * @param string $name Content field name
     * @param string $url Asset URL
     * @param string|null $title Image title
     * @param string|null $description Image description
     * @param string|null $alt Alt text
     * @param array $sizes Array of alternative image sizes
     * @throws \Studio24\Frontend\Exception\ContentFieldException
     */
    public function __construct(string $name, string $url, string $title = null, string $description = null, string $alt = null, array $sizes = [])
    {
        parent::__construct($name, $url, $title, $description);

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
    public function __toString(): string
    {
        return $this->getUrl();
    }
}
