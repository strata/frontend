<?php

declare(strict_types=1);

namespace Strata\Frontend\Content\Field;

use Strata\Frontend\Exception\InvalidMimeTypeException;

/**
 * Asset content field
 *
 * @package Strata\Frontend\Content\Field
 */
class AssetField extends ContentField
{
    protected $url;
    protected $title = '';
    protected $description = '';
    protected $mimeType;

    /**
     * Array of allowed mime types
     *
     * @todo Look at updating this to use symfony/mime once Symfony 4.3 has been released
     *
     * see https://codex.wordpress.org/Function_Reference/get_allowed_mime_types
     * see https://github.com/symfony/mime/blob/master/MimeTypes.php
     * @var array
     */
    public static $allowedMimeTypes = [];

    /**
     * Does the content field contain HTML?
     *
     * @return bool
     */
    public function hasHtml(): bool
    {
        return false;
    }

    /**
     * Create asset content field
     *
     * @param string $name Content field name
     * @param string $url Asset URL
     * @param string|null $title Asset title
     * @param string|null $description Asset description
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
     * Guess the asset content field type based on mime-type
     *
     * @param string $mimeType Mime-type
     * @return string|null
     */
    public static function guesser(string $mimeType): ?string
    {
        $types = [
            'Image'     => Image::$allowedMimeTypes,
            'Document'  => Document::$allowedMimeTypes,
            'Audio'     => Audio::$allowedMimeTypes,
            'Video'     => Video::$allowedMimeTypes,
        ];

        foreach ($types as $key => $allowedTypes) {
            if (in_array($mimeType, $allowedTypes)) {
                return $key;
            }
        }

        return null;
    }

    /**
     * Return a new instance of an asset object based on the mime-type
     *
     * @param string $mimeType Mime type
     * @param mixed ...$args Arguments to pass to new class constructor
     * @return ?AssetField
     */
    public static function factory($mimeType, ...$args): ?AssetField
    {
        $class = self::guesser($mimeType);
        if ($class === null) {
            return null;
        }
        $class = __NAMESPACE__  . '\\' . $class;
        return new $class(... $args);
    }

    /**
     * Is this mime type allowed by this asset content type?
     *
     * @param $type
     * @return bool
     */
    public function mimeTypeAllowed($type): bool
    {
        return in_array($type, self::$allowedMimeTypes);
    }

    public function setAllowedMimeTypes(array $types)
    {
        self::$allowedMimeTypes = $types;
    }

    public function addAllowedMimeType(string $type)
    {
        self::$allowedMimeTypes[] = $type;
    }

    /**
     * Return the mime type for this asset
     *
     * @return string
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * Set the asset mime type
     *
     * @param string $type Mime type
     * @throws InvalidMimeTypeException
     */
    public function setMimeType(string $type)
    {
        if ($this->mimeTypeAllowed($type)) {
            throw new InvalidMimeTypeException(sprintf('Invalid mime type "%s", allowed mime-types: %s', $type, implode(', ', self::$allowedMimeTypes)));
        }

        $this->mimeType = $type;
    }

    /**
     * Return file extension
     *
     * @return string
     */
    public function getExtension(): string
    {
        return pathinfo($this->getUrl(), PATHINFO_EXTENSION);
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     * @return string
     */
    public function setUrl(string $url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return Image
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Image
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
        return $this;
    }

    public function getValue()
    {
        return $this->__toString();
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
