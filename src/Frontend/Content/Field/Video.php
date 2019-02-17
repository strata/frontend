<?php
declare(strict_types=1);

namespace Studio24\Frontend\Content\Field;

/**
 * Document content field
 *
 * @package Studio24\Frontend\Content\Field
 */
class Video extends AssetField
{
    const TYPE = 'video';

    public static $allowedMimeTypes = [
        'video/x-ms-asf',
        'video/x-ms-wmv',
        'video/x-ms-wmx',
        'video/x-ms-wm',
        'video/avi',
        'video/divx',
        'video/x-flv',
        'video/quicktime',
        'video/mpeg',
        'video/mp4',
        'video/ogg',
        'video/webm',
        'video/x-matroska',
    ];

    /**
     * Create video content field
     *
     * @param string $name Content field name
     * @param string $url Asset URL
     * @param string|null $title Title
     * @param string|null $description Description
     * @throws \Studio24\Frontend\Exception\ContentFieldException
     */
    public function __construct(string $name, string $url, string $title = null, string $description = null)
    {
        $this->setName($name);
        $this->setUrl($url);

        if (!empty($title)) {
            $this->setTitle($title);
        }
        if (!empty($description)) {
            $this->setDescription($description);
        }
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
