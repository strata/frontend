<?php
declare(strict_types=1);

namespace Studio24\Frontend\Content\Field;

/**
 * Video content field
 *
 * @package Studio24\Frontend\Content\Field
 */
class Video extends PlayableMediaAsset
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
     * Video constructor.
     *
     * @param string $name
     * @param string $url
     * @param string $filesize
     * @param int|string $bitrate
     * @param string $length
     * @param string|null $title
     * @param string|null $description
     * @throws \Studio24\Frontend\Exception\ContentFieldException
     */
    public function __construct(string $name, string $url, string $filesize, $bitrate, string $length, string $title = null, string $description = null)
    {
        parent::__construct($name, $url, $filesize, $bitrate, $length, $title, $description);
    }
}
