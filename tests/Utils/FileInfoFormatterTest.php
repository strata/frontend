<?php

declare(strict_types=1);

namespace App\Tests\Frontend\Utils;

use PHPUnit\Framework\TestCase;
use Strata\Frontend\Utils\FileInfoFormatter;

class FileInfoFormatterTest extends TestCase
{
    public function testFileSizeToString()
    {
        $this->assertEquals('0 B', FileInfoFormatter::formatFileSize(null));
        $this->assertEquals('0 B', FileInfoFormatter::formatFileSize(''));
        $this->assertEquals('0 B', FileInfoFormatter::formatFileSize(0));
        $this->assertEquals('250 B', FileInfoFormatter::formatFileSize(250));
        $this->assertEquals('1014.47 MB', FileInfoFormatter::formatFileSize(1063751824));
        $this->assertEquals('930.87 GB', FileInfoFormatter::formatFileSize(999511627776));
        $this->assertEquals('1 TB+', FileInfoFormatter::formatFileSize(1199511627776));
    }
}
