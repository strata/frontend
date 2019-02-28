<?php

namespace Studio24\Frontend\Utils;

/**
 * Class FileInfoFormatter
 * @package Studio24\Frontend\Utils
 */
class FileInfoFormatter
{

    /**
     * Converts a filesize (in bytes) to a formatted string (e.g. '23 MB')
     *
     * @param int $sizeInByte
     * @return string
     */
    public static function formatFileSize($sizeInByte = 0 ): string
    {
        switch ($sizeInByte) {
            case $sizeInByte < 1024:
                $size = $sizeInByte .' B';
                break;
            case $sizeInByte < 1048576:
                $size = round($sizeInByte / 1024, 2) .' KB';
                break;
            case $sizeInByte < 1073741824:
                $size = round($sizeInByte / 1048576, 2) . ' MB';
                break;
            case $sizeInByte < 1099511627776:
                $size = round($sizeInByte / 1073741824, 2) . ' GB';
                break;
        }

        return $size;
    }

}