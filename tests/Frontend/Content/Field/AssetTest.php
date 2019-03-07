<?php
declare(strict_types=1);

namespace App\Tests\Frontend\Content\Field;

use PHPUnit\Framework\TestCase;
use Studio24\Frontend\Content\Field\AssetField;

class AssetTest extends TestCase
{

    public function testGuesser()
    {
        $this->assertEquals('Document', AssetField::guesser('application/pdf'));
        $this->assertEquals('Document', AssetField::guesser('application/msword'));
        $this->assertEquals('Image', AssetField::guesser('image/jpeg'));
        $this->assertEquals('Image', AssetField::guesser('image/png'));
        $this->assertEquals('Video', AssetField::guesser('video/ogg'));
        $this->assertEquals('Audio', AssetField::guesser('audio/wav'));

        $this->assertNull(AssetField::guesser('application/octet-stream'));
        $this->assertNull(AssetField::guesser('fake'));

        $asset = AssetField::factory('application/pdf', 'my_document', 'url');
        $this->assertInstanceOf('Studio24\Frontend\Content\Field\Document', $asset);
        $this->assertEquals('my_document', $asset->getName());

        $asset = AssetField::factory('audio/wav', 'my_audio', 'url' , '219874', '129', '0:30' , []);
        $this->assertInstanceOf('Studio24\Frontend\Content\Field\Audio', $asset);
        $this->assertEquals('my_audio', $asset->getName());
    }
}
