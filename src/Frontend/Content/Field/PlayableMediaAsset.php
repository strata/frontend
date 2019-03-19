<?php
declare(strict_types=1);

namespace Studio24\Frontend\Content\Field;

/**
 * Playable media content field, i.e. Audio and Video
 *
 * @package Studio24\Frontend\Content\Field
 */
class PlayableMediaAsset extends AssetField
{
    protected $filesize;
    protected $bitrate;
    protected $length;

    /**
     * PlayableMediaField constructor.
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
        $this->setName($name);
        $this->setUrl($url);
        $this->setFileSize($filesize);
        $this->setBitRate($bitrate);
        $this->setLength($length);

        if (!empty($title)) {
            $this->setTitle($title);
        }
        if (!empty($description)) {
            $this->setDescription($description);
        }
    }

    /**
     * Return file size
     *
     * @return string
     */
    public function getFileSize(): string
    {
        return $this->filesize;
    }

    /**
     * Return bit rate
     *
     * @return int
     */
    public function getBitRate(): int
    {
        return $this->bitrate;
    }

    /**
     * Return length (media duration)
     *
     * @return string
     */
    public function getLength(): string
    {
        return $this->length;
    }

    /**
     * Set file size
     *
     * @param string $filesize
     * @return string
     */
    protected function setFileSize(string $filesize)
    {
        $this->filesize = $filesize;
        return $this;
    }

    /**
     * Set bit rate
     *
     * @param int|string $bitrate
     * @return PlayableMediaField
     */
    protected function setBitRate($bitrate)
    {
        if (!is_numeric($bitrate)) {
            throw new Exception(sprintf('Invalid bit rate "%s", must be numeric.', $bitrate));
        }

        $this->bitrate = intval(round($bitrate));
        return $this;
    }

    /**
     * Set length
     *
     * @param string $length
     * @return string
     */
    protected function setLength(string $length)
    {
        $this->length = $length;
        return $this;
    }
}
