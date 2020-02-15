<?php

declare(strict_types=1);

namespace Studio24\Frontend\Content\Field;

/**
 * Document content field
 *
 * @package Studio24\Frontend\Content\Field
 */
class Document extends AssetField
{
    use SizesTrait;

    const TYPE = 'document';

    public static $allowedMimeTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.ms-powerpoint',
        'application/vnd.ms-excel',
        'application/vnd.ms-project',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
        'application/vnd.openxmlformats-officedocument.presentationml.template',
        'application/vnd.openxmlformats-officedocument.presentationml.slide',
        'application/onenote',
        'application/vnd.oasis.opendocument.text',
        'application/vnd.oasis.opendocument.presentation',
        'application/vnd.oasis.opendocument.spreadsheet',
        'application/vnd.oasis.opendocument.graphics',
        'application/vnd.oasis.opendocument.chart',
        'application/wordperfect',
        'application/vnd.apple.keynote',
        'application/vnd.apple.numbers',
        'application/vnd.apple.pages',
    ];

    protected $filesize = '';

    /**
     * Create document content field
     *
     * @param string $name Content field name
     * @param string $url Asset URL
     * @param string $filesize Size of file as formatted string
     * @param string|null $title Title
     * @param string|null $description Description
     * @throws \Studio24\Frontend\Exception\ContentFieldException
     */
    public function __construct(string $name, string $url, string $filesize = null, string $title = null, string $description = null)
    {
        $this->setName($name);
        $this->setUrl($url);

        if (!empty($filesize)) {
            $this->setFileSize($filesize);
        }

        if (!empty($title)) {
            $this->setTitle($title);
        }
        if (!empty($description)) {
            $this->setDescription($description);
        }
    }

    /**
     * Return filezise
     *
     * @return string
     */
    public function getFileSize(): string
    {
        return $this->filesize;
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
     * Set file size
     *
     * @return string
     */
    protected function setFileSize(string $filesize)
    {
        $this->filesize = $filesize;
        return $this;
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
