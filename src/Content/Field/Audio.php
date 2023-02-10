<?php

declare(strict_types=1);

namespace Strata\Frontend\Content\Field;

use Strata\Frontend\Exception\ContentFieldException;

/**
 * Document content field
 *
 * @package Strata\Frontend\Content\Field
 */
class Audio extends PlayableMediaAsset
{
    protected $mediaParameters;

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
     * Audio constructor.
     *
     * @param string $name
     * @param string $url
     * @param string $filesize
     * @param int|string $bitrate
     * @param string $length
     * @param array $media_parameters
     * @param string|null $title
     * @param string|null $description
     * @throws \Strata\Frontend\Exception\ContentFieldException
     */
    public function __construct(string $name, string $url, string $filesize, $bitrate, string $length, array $mediaParameters, string $title = null, string $description = null)
    {
        parent::__construct($name, $url, $filesize, $bitrate, $length, $title, $description);

        $this->setMediaParameters($mediaParameters);
    }


    /**
     * Returns array of media parameters, e.g. encoder, sample_rate
     *
     * @return array
     */
    public function getMediaParameters(): array
    {
        return $this->mediaParameters;
    }

    /**
     * Sets media parameters extra properties, e.g. encoder, sample_rate
     *
     * @param array $mediaParameters
     * @return $this
     */
    public function setMediaParameters(array $mediaParameters)
    {
        $this->mediaParameters = $mediaParameters;

        return $this;
    }
}
