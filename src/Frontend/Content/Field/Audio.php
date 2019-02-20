<?php
declare(strict_types=1);

namespace Studio24\Frontend\Content\Field;

/**
 * Document content field
 *
 * @package Studio24\Frontend\Content\Field
 */
class Audio extends AssetField
{
    const TYPE = 'audio';

    public static $allowedMimeTypes = [
        'audio/mpeg',
        'audio/x-realaudio',
        'audio/wav',
        'audio/ogg',
        'audio/midi',
        'audio/x-ms-wma',
        'audio/x-ms-wax',
        'audio/x-matroska',
    ];

    /**
     * Create audio content field
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
